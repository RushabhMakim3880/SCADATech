<?php

namespace Modules\Backend\Setting\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\Setting\Models\SettingModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;

class Setting extends ApiBaseController
{
    use ResponseTrait;

    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }
    public function getSetting()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }
        $settings = $this->settingModel->where("tenantId", $this->user->tenantId) // Adding tenantId condition with default value 1
            ->findAll();

        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }
        return $this->respond(['status' => true, 'message' => '', 'data' => $settingsArray]);
    }

    public function saveSetting()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $input = $this->getInputData();

        $jsonInput = $input['jsonInput'];

        foreach ($jsonInput as $key => $value) {

            if (is_array($value)) {
                continue;
            }

            $existingSetting = $this->db->table('tenantCompanySettings')
                ->where('key', $key)
                ->where('tenantId', $this->user->tenantId)
                ->get()
                ->getRow();


            if (is_null($existingSetting)) {
                $this->db->table('tenantCompanySettings')->insert([
                    'key'   => $key,
                    'value' => $value,
                    'tenantId' => $this->user->tenantId
                ]);
            } else {
                $this->db->table('tenantCompanySettings')->update([
                    'value' => $value
                ], [
                    'key' => $key,
                    'tenantId' => $this->user->tenantId
                ]);
            }
        }

        // Clear the cache
        service('cache')->delete('1_tenantCompanySettings');



        return $this->respondCreated(['status' => true, 'message' => 'Data saved successfully']);
    }

    public function getCompanyMasterSetting()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }
        $companyMasterSettingsModel = new \Modules\Backend\Setting\Models\CompanyMasterSettingsModel();
        $settings = $companyMasterSettingsModel->where("tenantId", $this->user->tenantId)->findAll();

        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }
        return $this->respond(['status' => true, 'message' => '', 'data' => $settingsArray]);
    }

    public function saveCompanyMasterSetting()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return redirect()->to('');
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        foreach ($jsonInput as $key => $value) {
            if (is_array($value)) {
                continue;
            }

            $existingSetting = $this->db->table('companyMasterSettings')
                ->where('key', $key)
                ->where('tenantId', $this->user->tenantId)
                ->get()
                ->getRow();

            if (is_null($existingSetting)) {
                $this->db->table('companyMasterSettings')->insert([
                    'key'       => $key,
                    'value'     => $value,
                    'tenantId'  => $this->user->tenantId,
                    'companyId' => 1,
                ]);
                $newId = $this->db->insertID();
                if (function_exists('assignSerialNumber')) {
                    assignSerialNumber($this->user->tenantId, "companyMasterSettings", "companySettingsId", $newId);
                }
            } else {
                $this->db->table('companyMasterSettings')->update([
                    'value' => $value
                ], [
                    'key'      => $key,
                    'tenantId' => $this->user->tenantId
                ]);
            }
        }

        return $this->respondCreated(['status' => true, 'message' => 'Data saved successfully']);
    }

    public function getDataTableColumns($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        $defaultColumns = [];
        $defaultColumns['companyMasterSettings_serialNo']          = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['companyMasterSettings_companySettingsId'] = ['title' => 'ID', 'visible' => false, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['companyMasterSettings_companyId']        = ['title' => 'Company ID', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['companyMasterSettings_key']              = ['title' => 'Key', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['companyMasterSettings_value']            = ['title' => 'Value', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];

        $configData['defaultOrderColumn'] = 'companyMasterSettings_serialNo';
        $configData['defaultOrderDirection'] = 'asc';

        foreach ($defaultColumns as $key => &$column) {
            $column['name'] = $key;
        }
        $defaultColumns = array_values($defaultColumns);

        $userId = $this->user->userId;
        $ex = $this->db->query("SELECT `value` FROM userSettings WHERE userId = $userId AND tenantId='" . $this->user->tenantId . "' AND `key` = '$module'")->getRow();

        if ($ex) {
            $columnSetting = json_decode($ex->value, true);

            $userColumns = [];
            foreach ($columnSetting as $col) {
                $userColumns[$col['name']] = $col;
            }

            $reorderedColumns = [];
            foreach ($columnSetting as $col) {
                foreach ($defaultColumns as $masterCol) {
                    if ($masterCol['name'] === $col['name']) {
                        $masterCol['visible'] = (int)$col['visible'];
                        $reorderedColumns[] = $masterCol;
                        break;
                    }
                }
            }

            $finalColumns = [];
            foreach ($defaultColumns as $masterCol) {
                if (!isset($userColumns[$masterCol['name']])) {
                    $masterCol['visible'] = false;
                    $finalColumns[] = $masterCol;
                }
            }

            $defaultColumns = array_merge($reorderedColumns, $finalColumns);
        }

        $configData["columns"] = $defaultColumns;

        return $this->respond([
            'status' => true,
            "data"   => $configData
        ], 200);
    }

    public function getDataTableData()
    {
        $select = [];
        $where = ["1", "companyMasterSettings.tenantId = '" . $this->user->tenantId . "'"];
        $dbTable = 'companyMasterSettings companyMasterSettings';

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $temp = json_decode($this->getDataTableColumns($jsonInput['module'])->getBody())->data;
        $myColumns = [];
        foreach ($temp->columns as $col) {
            $myColumns[$col->name] = $col;
        }

        $queryParameters = [];
        $columns = $jsonInput['columns'];
        $searchWhere = [];

        $search = $jsonInput['search']['value'] ?? '';
        $filters = $jsonInput['filters'] ?? [];

        $isDownload = isset($jsonInput['downloadType']) || !isset($jsonInput['draw']);

        foreach ($columns as $column) {
            if (!isset($myColumns[$column['data']])) {
                continue;
            }

            $dbField = str_replace('_', ".", $column['data']);
            $columnName = $column['data'];
            $select[] = $dbField . " as " . $columnName;

            if ($search != '' && $column['searchable'] == true) {
                $searchWhere[] = $dbField . " LIKE :searchTerm:";
                $queryParameters["searchTerm"] = "%$search%";
            }

            if (isset($filters[$columnName]) && $filters[$columnName] !== '' && isset($myColumns[$columnName]->filterType)) {
                $where[] = "$dbField = :" . $columnName . "_filter:";
                $queryParameters[$columnName . '_filter'] = $filters[$columnName];
            }
        }

        $orderBy = " ORDER BY companyMasterSettings.companySettingsId ASC";
        if (!empty($jsonInput['order'])) {
            $orderIndex = $jsonInput['order'][0]["column"];
            $orderDirection = $jsonInput['order'][0]["dir"];
            $orderColumn = $columns[$orderIndex]['data'];
            $dbOrderColumn = str_replace('_', ".", $orderColumn);
            $orderBy = " ORDER BY $dbOrderColumn $orderDirection";
        }

        if (!empty($searchWhere)) {
            $where[] = "(" . implode(' OR ', $searchWhere) . ")";
        }
        $whereClause = !empty($where) ? " WHERE " . implode(' AND ', $where) : "";

        $limit = !empty($jsonInput['length']) ? (int) $jsonInput['length'] : 10;
        $offset = !empty($jsonInput['start']) ? (int) $jsonInput['start'] : 0;

        $sql = "SELECT " . implode(", ", $select) . " FROM $dbTable $whereClause $orderBy LIMIT :limit: OFFSET :offset:";
        $queryParameters['limit'] = (int)$limit;
        $queryParameters['offset'] = (int)$offset;

        $data = $this->db->query($sql, $queryParameters)->getResult();

        foreach ($data as &$row) {
            if (!$isDownload) {
                $encodedId = setKey($row->companyMasterSettings_companySettingsId, "companyMasterSettings");
                $action = "<button type='button' class='btn btn-sm btn-primary apiPopup me-1' data-title='Edit Setting' data-size='lg' data-endpoint='setting/addCompanyMasterSetting/" . $encodedId . "'><i class='fa fa-edit'></i></button>";
                $action .= "<button type='button' class='btn btn-sm btn-danger deleteBtn' data-endpoint='api/setting/deleteCompanyMasterSetting/" . $encodedId . "'><i class='fa fa-trash'></i></button>";

                $row->companyMasterSettings_serialNo = $row->companyMasterSettings_serialNo . " " . $action;
            }

            $row->companyMasterSettings_key = printable($row->companyMasterSettings_key);
            $row->companyMasterSettings_value = printable($row->companyMasterSettings_value);

            foreach ($row as $key => $value) {
                if (!isset($myColumns[$key])) {
                    unset($row->$key);
                }
            }
        }

        $totalRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable WHERE tenantId = '" . $this->user->tenantId . "'")->getRow()->total;
        $filteredRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable $whereClause", $queryParameters)->getRow()->total;

        $header = [];
        foreach ($columns as $column) {
            $header[] = $column['name'];
        }

        $response = [
            'draw'            => $jsonInput['draw'] ?? 1,
            'module'          => $jsonInput['module'],
            'header'          => $header,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
            'columnTotals'    => [],
        ];

        return $this->respond($response, 200);
    }

    public function getCompanyMasterSettingById($id = 0)
    {
        $realId = getKey($id, "companyMasterSettings");
        if (!$realId) {
            $realId = $id;
        }

        $setting = $this->db->table('companyMasterSettings')
            ->where('companySettingsId', $realId)
            ->where('tenantId', $this->user->tenantId)
            ->get()
            ->getRow();

        if (!$setting) {
            return $this->failNotFound('Setting not found');
        }

        return $this->respond(['status' => true, 'data' => $setting]);
    }

    public function saveCompanyMasterSettingItem()
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return $this->failUnauthorized('Permission denied');
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $companySettingsId = isset($jsonInput['companySettingsId']) ? getKey($jsonInput['companySettingsId'], "companyMasterSettings") : 0;
        if (!$companySettingsId && isset($jsonInput['companySettingsId'])) {
            $companySettingsId = (int)$jsonInput['companySettingsId'];
        }

        $key       = trim($jsonInput['key'] ?? '');
        $value     = trim($jsonInput['value'] ?? '');
        $companyId = (int)($jsonInput['companyId'] ?? 1);

        if (empty($key)) {
            return $this->fail('Key is required');
        }

        if ($companySettingsId > 0) {
            $this->db->table('companyMasterSettings')->update([
                'key'       => $key,
                'value'     => $value,
                'companyId' => $companyId,
            ], [
                'companySettingsId' => $companySettingsId,
                'tenantId'          => $this->user->tenantId,
            ]);
            $msg = 'Company setting updated successfully';
        } else {
            $this->db->table('companyMasterSettings')->insert([
                'key'       => $key,
                'value'     => $value,
                'companyId' => $companyId,
                'tenantId'  => $this->user->tenantId,
            ]);
            $newId = $this->db->insertID();
            if (function_exists('assignSerialNumber')) {
                assignSerialNumber($this->user->tenantId, "companyMasterSettings", "companySettingsId", $newId);
            }
            $msg = 'Company setting created successfully';
        }

        return $this->respondCreated(['status' => true, 'message' => $msg]);
    }

    public function deleteCompanyMasterSetting($id = 0)
    {
        if (!UserPermissionLib::userCanDo("setting", 'view')) {
            return $this->failUnauthorized('Permission denied');
        }

        $realId = getKey($id, "companyMasterSettings");
        if (!$realId) {
            $realId = $id;
        }

        $this->db->table('companyMasterSettings')
            ->where('companySettingsId', $realId)
            ->where('tenantId', $this->user->tenantId)
            ->delete();

        return $this->respond(['status' => true, 'message' => 'Company setting deleted successfully']);
    }
}

