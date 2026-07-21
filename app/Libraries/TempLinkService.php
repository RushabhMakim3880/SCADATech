<?php

namespace App\Libraries;

use App\Models\TempLinkModel;
use CodeIgniter\I18n\Time;

class TempLinkService
{
    protected $model;

    public function __construct()
    {
        $this->model = new TempLinkModel();
        $this->cleanUp();
    }

    public function generateLink($originalUrl, $expiryMinutes, array $payload = [])
    {

        $normalized = $this->normalizedJson($payload);
        $payloadHash = md5($normalized);

        // first check if the link is already generated, than update expiry time and  return the same link
        $exists = $this->model->where('originalUrl', $originalUrl)
            ->where('payloadHash', $payloadHash)
            ->where('expiresAt >', Time::now()->toDateTimeString())
            ->first();

        if ($exists) {
            $this->model->update($exists['id'], [
                'expiresAt' => Time::now()->addMinutes($expiryMinutes),
            ]);
            return base_url("r/{$exists['token']}");
        }

        do {
            $token = bin2hex(random_bytes(6)); // 12-char token
            $exists = $this->model->where('token', $token)->first();
        } while ($exists); // Ensure unique token

        $expiresAt = Time::now()->addMinutes($expiryMinutes);

        // Store token in DB
        $this->model->insert([
            'token'       => $token,
            'originalUrl' => $originalUrl,
            'payload'     => $normalized,
            'payloadHash' => $payloadHash,
            'expiresAt'   => $expiresAt,
            'createdAt'   => Time::now()->toDateTimeString(),
        ]);

        return base_url("r/{$token}");
    }

    public function validateToken($token)
    {
        if (!$token) {
            return null;
        }

        $record = $this->model->where('token', $token)
            ->where('expiresAt >', Time::now()->toDateTimeString())
            ->first();

        return $record ?: null;
    }

    public function getPayload($token)
    {
        $token = getKey($token, "tempLink");
        $record = $this->validateToken($token);
        return $record ? json_decode($record['payload'], true) : null;
    }

    private function cleanUp()
    {
        // Remove expired tokens older than 1 month
        $this->model->where('expiresAt <', Time::now()->toDateTimeString())
            ->where('createdAt <', Time::now()->subMonths(1)->toDateTimeString())
            ->delete();
    }

    private function normalizeArray(array &$arr)
    {
        foreach ($arr as &$value) {
            if (is_array($value)) {
                $this->normalizeArray($value);
            }
        }
        ksort($arr);
    }

    private function normalizedJson(array $data): string
    {
        $this->normalizeArray($data);
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
