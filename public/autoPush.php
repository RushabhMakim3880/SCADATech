<?php
// autoPush.php , put inside public directory, should be accessible from project.com/autoPush.php

$secret = getEnvVar("gitAutoPush.secret", "nosecret");  // This should be the same secret you configure in Gogs webhook
$phpPath = getEnvVar("phpPath", "php"); // Path to PHP executable, default is "php"
$branch = trim(shell_exec("git rev-parse --abbrev-ref HEAD 2>&1"));
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);
$currentScriptDirectory = __DIR__;
$projectDirectory = dirname($currentScriptDirectory);
$logFile = $projectDirectory . "/writable/gitLog.txt";  // Make sure to set the correct path for your log file
// Initialize a variable for log data
$logData = "Log request received at " . date('Y-m-d H:i:s') . "\n";
$logData .= "Secret: $secret \n";
$logData .= "Branch: $branch \n";
// Log the headers for debugging
$logData .= "Headers: \n";
$logData .= print_r(getallheaders(), true) . "\n";



if (isset($_SERVER['HTTP_X_GITEA_SIGNATURE']) && $_SERVER['HTTP_X_GITEA_SIGNATURE'] == hash_hmac('sha256', $payload, $secret)) {

    if (trim($data['ref']) === "refs/heads/$branch") {

        // The commands to run
        $commands = [
            "git fetch --tags",
            "git reset --hard origin/$branch 2>&1", // discard all local changes, force exact repo state
            "cd $projectDirectory && composer install --no-dev --ignore-platform-reqs 2>&1", // install based on composer.lock
            "cd $projectDirectory && $phpPath spark mtpl:run migrate 2>&1", // Run migrations
            "cd $projectDirectory && $phpPath spark mtpl:run seed 2>&1", // Run migrations
        ];

        // Execute each command and capture the output
        foreach ($commands as $command) {
            $output = shell_exec($command);
            $logData .= "Command: $command\n";
            $logData .= "Output: $output\n";
        }

        echo "Deployed!";
    } else {
        $logData .= "Received Branch: '" . $data['ref'] . "'\n";
        $logData .= "Current Branch: '$branch'\n";
        echo "Wrong branch";
    }
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);

    $logData .= "Invalid request. Data: " . print_r($data, true) . "\n";
    $logData .= "Expected hash: " . hash_hmac('sha256', $payload, $secret) . "\n";
    $logData .= "Received hash: " . $_SERVER['HTTP_X_GITEA_SIGNATURE'] . "\n";

    echo "Invalid request";
}

// Save the log data, overwriting the existing log
$logData .= "Raw Payload: " . print_r($data, true) . "\n";
$logData .= "----------------------------------------\n";
file_put_contents($logFile, $logData);




function getEnvVar($key, $default = null)
{
    $envFile = dirname(__DIR__) . '/.env'; // Adjust path if needed
    if (!file_exists($envFile)) {
        return $default;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || !str_contains($line, '=')) {
            continue;
        }
        [$envKey, $envVal] = explode('=', $line, 2);
        if (trim($envKey) === $key) {
            return trim(trim($envVal), "\"'");
        }
    }

    return $default;
}
