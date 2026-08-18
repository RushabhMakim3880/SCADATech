<?php

function priceBandsFilter()
{
    $bands = [];
    $bands[] = [
        "option" => "0-500",
        "text" => "0 to 500",
    ];
    $bands[] = [
        "option" => "500-1000",
        "text" => "500 to 1000",
    ];
    $bands[] = [
        "option" => "1000-2000",
        "text" => "1000 to 2000",
    ];
    $bands[] = [
        "option" => "2000-5000",
        "text" => "2000 to 5000",
    ];
    $bands[] = [
        "option" => "5000-500000",
        "text" => ">5000",
    ];

    return $bands;
}


function nodejsApi($method, $url, $data = null)
{
    $client = \Config\Services::curlrequest();
    if (!in_array($method, ['get', 'post'])) {
        throw new \InvalidArgumentException("Method must be 'get' or 'post'");
    }

    if ($method === 'get' && $data) {
        $url .= '?' . http_build_query($data);
    }

    if (!getenv('internalApiToken')) {
        throw new \RuntimeException("Internal API token is not set in environment variables.");
    }

    $options = [
        'headers' => [
            'X-Internal-Token' => getenv('internalApiToken')
        ],
        'http_errors' => false // << prevent exception on 4xx/5xx
    ];

    if ($method === 'post') {
        $options['json'] = $data ? $data : [];
    }

    $response = $client->request(
        strtoupper($method),
        'http://127.0.0.1:' . getenv("nodejsApiPort") . $url,
        $options
    );

    return [
        'statusCode' => $response->getStatusCode(),
        'body' => $response->getBody(),
        'json' => json_decode($response->getBody(), true)
    ];
}



function getMachineTypes()
{
    return ["SKIPPER", "Other"];
}


function getHeadTypes()
{
    return ["Punching", "Marking", "Cutting"];
}


function updateTagValuesToDb($tags)
{
    $db = \Config\Database::connect();

    foreach ($tags as $tagId => $value) {

        if ($tagId == 579) {
            // Mark 1
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 7]);
        } elseif ($tagId == 580) {
            // Mark 2
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 8]);
        } elseif ($tagId == 581) {
            // Mark 3
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 9]);
        } elseif ($tagId == 582) {
            // Mark 4
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 10]);
        } elseif ($tagId == 471) {
            // Head 1
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 4]);
        } elseif ($tagId == 472) {
            // Head 2
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 5]);
        } elseif ($tagId == 473) {
            // Head 3
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 6]);
        } elseif ($tagId == 468) {
            // Head 4
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 1]);
        } elseif ($tagId == 469) {
            // Head 5
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 2]);
        } elseif ($tagId == 470) {
            // Head 6
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 3]);
        } elseif ($tagId == 393) {
            // Cutting Position
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 8]);
        } elseif ($tagId == 392) {
            // Cutting Hold Down
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 8]);
        } elseif ($tagId == 403) {
            // Punch 1 position 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 4]);
        } elseif ($tagId == 395) {
            // Punch 1 Hold Down. 
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 4]);
        } elseif ($tagId == 404) {
            // Punch 2 position 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 5]);
        } elseif ($tagId == 396) {
            // Punch 2 Hold Down. 
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 5]);
        } elseif ($tagId == 405) {
            // Punch 3 position 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 6]);
        } elseif ($tagId == 397) {
            // Punch 3 Hold Down.
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 6]);
        } elseif ($tagId == 406) {
            // Punch 4 position 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 1]);
        } elseif ($tagId == 398) {
            // Punch 4 Hold Down. 
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 1]);
        } elseif ($tagId == 407) {
            // Punch 5 position 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 2]);
        } elseif ($tagId == 399) {
            // Punch 5 Hold Down. 
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 2]);
        } elseif ($tagId == 408) {
            // Punch 6 position 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 3]);
        } elseif ($tagId == 400) {
            // Punch 6 Hold Down. 
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 3]);
        } elseif ($tagId == 402) {
            // Marking Position. 
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 7]);
        }
    }
}


function tagPermisionGroups()
{
    $permissions = [
        "OpHoming" => [
            'machinePos' => [384, 385, 386, 387, 388, 389, 390],
            'proxyWear' => [421, 422, 423, 424, 425, 426, 427],
            'homePos' => [410, 411, 412, 413, 414, 415],
        ],
        "OpSettings" => [
            'distance' => [401, 402, 400, 408, 399, 407, 398, 406, 397, 405, 396, 404, 395, 403, 392, 393, 394],
            'autoSpeed' => [428, 429, 418, 420, 474, 456, 458, 460, 462, 464, 466, 416],
            'manualMaxSpeed' => [419, 457, 459, 461, 463, 465, 467],
            'safetyDistances' => [431, 432, 433, 434, 435, 436, 437, 430],
            'punchSelection' => [468, 469, 470, 471, 472, 473, 579, 580, 581, 582],
            'proxyDelayTime' => [567, 757, 544, 557, 538, 545, 562, 539, 552, 564, 561, 540, 553, 565, 560, 541, 554, 566, 559, 542, 555, 563, 558, 543, 556, 550, 549, 548, 576, 577, 551, 547, 578, 546, 569, 570, 571, 568, 572, 573, 574, 575],
            'servoTime' => [353, 757, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 352, 351],
            'accumulator' => [347, 442, 445],
            'marking' => [350, 438, 439, 440, 441],
            'princher' => [348, 444, 447],
            'temperature' => [449, 448, 450],
            'inFeed' => [349, 443, 446],
            'general' => [451, 452, 545, 455, 453, 475, 476, 493, 380, 383, 381]
        ]

    ];

    return $permissions;
}
