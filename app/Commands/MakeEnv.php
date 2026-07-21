<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Minishlink\WebPush\VAPID;

class MakeEnv extends BaseCommand
{
    protected $group       = 'custom';
    protected $name        = 'make:env';
    protected $description = 'Generate .env file from env.template with prompts and random secrets';

    public function run(array $params)
    {
        $templatePath = ROOTPATH . 'env.template';
        $outputPath   = ROOTPATH . '.env';

        if (!is_file($templatePath)) {
            CLI::error('env.template not found in project root');
            return;
        }

        $lines         = file($templatePath, FILE_IGNORE_NEW_LINES);
        $output        = [];
        $pendingMeta   = null;
        $vapidKeys     = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, '#@')) {
                $pendingMeta = $this->parseMeta($trimmed);
                continue;
            }

            if ($pendingMeta) {
                $comment = $pendingMeta['comment'] ?? null;
                if ($comment) {
                    CLI::write("\n" . CLI::color("Info: $comment", 'yellow'));
                }

                $keyValue = explode('=', $line, 2);
                $key = trim($keyValue[0] ?? '');

                if (!$key) continue;

                $value = $this->resolveValue($pendingMeta, $vapidKeys);
                $output[] = "$key = $value";

                $pendingMeta = null;
            } else {
                $output[] = $line;
            }
        }

        file_put_contents($outputPath, implode("\n", $output));
        CLI::write("\n.env file generated successfully ✅", 'green');
    }

    protected function parseMeta(string $line): array
    {
        $meta = [];
        $parts = explode(';', str_replace('#@', '', trim($line)));
        foreach ($parts as $part) {
            [$k, $v] = explode('=', $part, 2) + [null, null];
            if ($k) $meta[trim($k)] = trim($v);
        }
        return $meta;
    }

    protected function resolveValue(array $meta, &$vapidKeys)
    {
        $label = $meta['label'] ?? 'Enter value';
        $type  = $meta['type'] ?? 'input';
        $validate = $meta['validate'] ?? null;

        switch ($type) {
            case 'input':
                do {
                    $value = CLI::prompt($label);
                    $isValid = match ($validate) {
                        'email' => filter_var($value, FILTER_VALIDATE_EMAIL),
                        'url'   => filter_var($value, FILTER_VALIDATE_URL),
                        'number' => is_numeric($value),
                        default => trim($value) !== '',
                    };

                    if (!$isValid) {
                        CLI::error("Invalid value. Please enter a valid $validate.");
                    }
                } while (!$isValid);

                return $value;

            case 'select':
                $options = explode('|', $meta['options'] ?? '');
                return CLI::prompt($label, $options);

            case 'generate':
                if (($meta['keyType'] ?? '') === 'webPushPublicKey' || ($meta['keyType'] ?? '') === 'webPushPrivateKey') {
                    if (!$vapidKeys) {
                        $vapidKeys = VAPID::createVapidKeys();
                    }
                    return $meta['keyType'] === 'webPushPublicKey'
                        ? $vapidKeys['publicKey']
                        : $vapidKeys['privateKey'];
                }
                $length = (int)($meta['length'] ?? 32);
                return bin2hex(random_bytes($length));

            default:
                return '';
        }
    }
}
