<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\UserPermissionLib;
use App\Libraries\Auth;

class ApiBaseController extends ResourceController
{
    /**
     * Initialize before each request
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->db = db_connect();

        if (Auth::check()) {
            $this->user = Auth::user();
            UserPermissionLib::setUser($this->user);
        }

        $this->clientType = $this->request->getHeaderLine('X-Client-Type');
    }

    public function getInputData()
    {
        $jsonInput = [];
        $uploadedFiles = [];

        // check if post method
        if ($this->request->getMethod() == 'POST') {


            if ($this->request->getHeaderLine('Content-Type') === 'application/json') {
                // Handle JSON input
                try {
                    $jsonInput = $this->request->getBody();
                    if (empty($jsonInput)) {
                        $this->returnFailedResponse('Invalid input submitted', 400);
                    }

                    $jsonInput = json_decode($jsonInput, true);
                    if (!is_array($jsonInput)) {
                        $this->returnFailedResponse('Invalid input format', 400);
                    }
                } catch (\Exception $e) {
                    $this->returnFailedResponse('Invalid JSON Format', 400);
                }
            } elseif (strpos($this->request->getHeaderLine('Content-Type'), 'multipart/form-data') !== false) {
                // Handle multipart/form-data

                $jsonData = $this->request->getFile('mtplJsonData');
                if ($jsonData && $jsonData->isValid()) {
                    // Read JSON from the uploaded file
                    $jsonInput = json_decode(file_get_contents($jsonData->getTempName()), true);

                    if (!$jsonInput) {
                        $this->returnFailedResponse('Invalid input submitted', 400);
                    }
                }

                $uploadedFiles = $this->request->getFiles();

                $this->handleFileUploads($uploadedFiles);

                foreach ($uploadedFiles as $key => $file) {
                    if ($key === 'mtplJsonData' && $file->isValid()) {
                        unset($uploadedFiles[$key]);
                    }
                }
            } else {
                // Unsupported content type
                $this->returnFailedResponse('Unsupported Content-Type', 400);
            }
        }

        return [
            'jsonInput' => sanitizeData($jsonInput),
            'uploadedFiles' => $uploadedFiles,
        ];
    }

    public function uploadFile($uploadedFileObject, $filePath, $newFileName = "", $randomName = false, $overWrite = false)
    {
        if ($uploadedFileObject->isValid()) {
            $newFileName = $newFileName == "" ? $uploadedFileObject->getName() : $newFileName;
            $newFileName = $randomName ? $uploadedFileObject->getRandomName() : $newFileName;
            $uploadedFileObject->move($filePath, $newFileName, $overWrite);
            return $newFileName;
        } else {
            return false;
        }
    }

    protected function handleFileUploads($files)
    {
        // Flatten the file array if it is multidimensional
        foreach ($files as $file) {
            if (is_array($file)) {
                foreach ($file as $subFile) {
                    $this->validateUpload($subFile);
                }
            } else {
                $this->validateUpload($file);
            }
        }
    }

    protected function validateUpload($file)
    {
        // List of accepted extensions
        $config = config('AppConfig');
        $maxFileSizeMB = $config->maxFileSizeMB;

        $acceptedExtensions = explode(',', $config->allowedFileTypes);
        $acceptedExtensions = array_map('trim', $acceptedExtensions);
        $acceptedExtensions = array_filter($acceptedExtensions, fn($ext) => $ext !== '');
        $acceptedExtensions = array_values($acceptedExtensions); // reindex if needed

        // strtolower all array items.
        $acceptedExtensions = array_map('strtolower', $acceptedExtensions);

        if ($file->isValid() && !$file->hasMoved()) {
            $extension = strtolower($file->getClientExtension());
            if (!in_array($extension, $acceptedExtensions) and $extension != "") {
                $this->returnFailedResponse('One of the file you are trying to upload is prohibited from upload.', 400);
            }

            if ($file->getSizeByUnit('mb') > $maxFileSizeMB) {
                $this->returnFailedResponse('One of the file you are trying to upload is too large.', 400);
            }
        }
    }

    protected function returnFailedResponse($message = 'Invalid Request', $statusCode = 400)
    {
        $response = [
            'status' => false,
            'messages' => [$message],
        ];
        $this->response
            ->setJSON($response)
            ->setStatusCode($statusCode)
            ->send(); // Directly send the response
        exit();
    }

    public function processChildTableData($data, $firstField)
    {
        // Ensure all values are arrays
        foreach ($data as &$row) {
            if (!is_array($row)) {
                $row = [$row];
            }
        }

        // Rearrange data correctly
        $newData = [];
        $totalItems = isset($data[$firstField]) && is_array($data[$firstField]) ? count($data[$firstField]) : 0;


        for ($index = 0; $index < $totalItems; $index++) {
            foreach ($data as $field => $values) {
                $newData[$index][$field] = isset($values[$index]) ? $values[$index] : null; // Ensure correct indexing
            }
        }

        return $newData;
    }


    public function syncChildTable($tableName, $primaryKey, $parentColumn, $parentId, $incomingData)
    {
        $result = [
            "deletedRows" => 0,
            "updatedRows" => 0,
            "insertedRows" => 0,
            "noChange" => 0
        ];

        if (empty($incomingData)) {
            // No incoming data — delete all child rows for this parent
            $this->db->table($tableName)->where($parentColumn, $parentId)->delete();
            $result["deletedRows"] = $this->db->affectedRows();
            return $result;
        }

        // Extract all primary keys from incoming data
        $incomingIds = [];
        foreach ($incomingData as $row) {
            if (!empty($row[$primaryKey])) {
                $incomingIds[] = $row[$primaryKey];
            }
        }
        $incomingIdsStr = empty($incomingIds) ? 'NULL' : implode(',', $incomingIds);

        // Delete rows not in incoming data
        $this->db->query("DELETE FROM $tableName WHERE $parentColumn = ? AND ($primaryKey NOT IN ($incomingIdsStr) OR $primaryKey IS NULL)", [$parentId]);
        $result["deletedRows"] = $this->db->affectedRows();

        // Insert/Update incoming rows
        foreach ($incomingData as $row) {
            $id = $row[$primaryKey] ?? '';
            $row[$parentColumn] = $parentId;

            if (!empty($id)) {
                $this->db->table($tableName)->where($primaryKey, $id)->update($row);
                if ($this->db->affectedRows() > 0) {
                    $result["updatedRows"]++;
                } else {
                    $result["noChange"]++;
                }
            } else {
                $this->db->table($tableName)->insert($row);
                $lastInsertId = $this->db->insertID();
                assignSerialNumber($this->user->tenantId, $tableName, $primaryKey, $lastInsertId);
                $result["insertedRows"]++;
            }
        }

        return $result;
    }



    public function getChildTableData($tableName, $parentColumn, $parentId)
    {
        // Fetch all rows where parentColumn = parentId
        $query = $this->db->table($tableName)->where($parentColumn, $parentId)->get();

        // Return as an associative array
        return $query->getResultArray();
    }
}
