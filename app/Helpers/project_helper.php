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
        'body'       => $response->getBody(),
        'json'       => json_decode($response->getBody(), true)
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

        if ($tagId == 292) {
            // Mark 1
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 5]);
        } elseif ($tagId == 293) {
            // Mark 2
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 6]);
        } elseif ($tagId == 294) {
            // Mark 3
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 7]);
        } elseif ($tagId == 295) {
            // Mark 4
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 8]);
        } elseif ($tagId == 307) {
            // Head 1
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 3]);
        } elseif ($tagId == 309) {
            // Head 2
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 4]);
        } elseif ($tagId == 311) {
            // Head 3
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 1]);
        } elseif ($tagId == 313) {
            // Head 4
            $db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$value, 2]);
        } elseif ($tagId == 318) {
            // Cutting Position
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 6]);
        } elseif ($tagId == 215) {
            // Cutting Hold Down
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 6]);
        } elseif ($tagId == 308) {
            // Punch 1 position 308
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 3]);
        } elseif ($tagId == 218) {
            // Punch 1 Hold Down. 218
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 3]);
        } elseif ($tagId == 310) {
            // Punch 2 position 310
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 4]);
        } elseif ($tagId == 219) {
            // Punch 2 Hold Down. 219
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 4]);
        } elseif ($tagId == 312) {
            // Punch 3 position 312
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 1]);
        } elseif ($tagId == 220) {
            // Punch 3 Hold Down. 220
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 1]);
        } elseif ($tagId == 314) {
            // Punch 4 position 314
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 2]);
        } elseif ($tagId == 221) {
            // Punch 4 Hold Down. 221
            $db->query("UPDATE machineDetails SET holdDownX = ? WHERE machineDetailId = ?", [$value, 2]);
        } elseif ($tagId == 296) {
            // Marking Position. 296
            $db->query("UPDATE machineDetails SET xPosition = ? WHERE machineDetailId = ?", [$value, 5]);
        }
    }
}


function tagPermisionGroups()
{
    $permissions = [
        "OpHoming" => [
            'homingSpeed' => [222, 223, 224, 225, 226, 384, 385],
            'proxyWear' => [324, 330, 336, 342, 348, 386],
            'wear' => [326, 332, 338, 344, 350],
            'homePosition' => [322, 328, 334, 340, 346, 387],
        ],
        "OpSettings" => [
            'distance' => [232, 296, 221, 314, 220, 312, 219, 310, 218, 308, 215, 318],
            'punchSelection' => [307, 309, 311, 313, 292, 293, 294, 295],
            'barDetails' => [],
            'scrapDetails' => [230, 305],
            'princherSpeed' => [315, 317, 303, 306, 204, 397, 302, 607],
            'servoAccSettings' => [352, 354, 378, 379, 398, 399],
            'oilTemperature' => [320, 319, 400],
            'lubSettings' => [401, 402, 403, 404],
            'masterSettings' => [405, 406, 407, 414, 415, 416, 417, 418],
            'accumulatorPressureSettings' => [615, 197, 198],
            'markingPressureSettings' => [617, 297],
            'princherPressureSettings' => [616, 300, 301],
            'inFeedPressureSettings' => [618, 228, 229],
        ],
        "OpAuto" => [
            'autoSpeed' => [193, 194, 195, 196],
        ]
    ];

    return $permissions;
}
