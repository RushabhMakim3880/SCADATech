<?php

namespace Modules\Backend\System\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;

use Modules\Backend\System\Models\StatusModel;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\Auth;


class StatusMaster extends ApiBaseController
{
    use ResponseTrait;

    protected $StatusModel;

    public function __construct()
    {
        $this->statusModel = new StatusModel();
    }

    public function getItem()
    {
        $itemData = $this->db->query("SELECT itemId as id, itemName as `name` 
    FROM ItemMaster 
    WHERE isDeleted = 0 
    AND isActive = 1 
    AND tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
            ->getResult();

        $response = [
            'status' => true,
            "message" => "Item retrieved successfully",
            "data" => $itemData
        ];

        return $this->respond($response, 200);
    }

    public function getUsers()
    {
        $itemData = $this->db->query("SELECT userId as id, userName as `name` 
    FROM UserMaster 
    WHERE isDeleted = 0 
    AND isActive = 1 
    AND tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
            ->getResult();

        $response = [
            'status' => true,
            "message" => "User retrieved successfully",
            "data" => $itemData
        ];

        return $this->respond($response, 200);
    }
    public function save($statusId = 0)
    {
        $statusId = (int)getKey($statusId, "status");

        if ((is_numeric($statusId) and $statusId === 0) or (is_string($statusId) and $statusId == "0")) {
            if (!UserPermissionLib::canAdd("statusMaster")) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            $statusData = $this->StatusModel->find($statusId);
            if (UserPermissionLib::canEdit($statusData, "statusMaster")) {
            } else {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['statusId'] = $statusId;

        if ($statusId == 0) {

            $rules['statusName'] = [
                'label'  => 'Status Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];

            $rules['module'] = [
                'label'  => 'Module',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        } else {
            $rules['statusId'] = [
                'label'  => 'Status ID',
                'rules'  => 'permit_empty|integer|is_natural',
                'errors' => [
                    'integer' => 'The {field} must be an integer.'
                ]
            ];
            $rules['statusName'] = [
                'label'  => 'Status Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
            $rules['module'] = [
                'label'  => 'Module',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        }

        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }


        $successMsg = 'Status created successfully';
        if ($statusId > 0) {

            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;
            $jsonInput['tenantId'] = 1; // Adding tenantId with default value 1

            $successMsg = 'Status updated successfully';

            //check if update success
            if (!$this->statusModel->update($statusId, $jsonInput)) {
                return $this->fail('Failed to update status', 500);
            }
        } else {
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $jsonInput['tenantId'] = 1; // Adding tenantId with default value 1
            // debug($jsonInput);
            // die;

            $statusId = $this->statusModel->insert($jsonInput);

            if (!$statusId) {
                return $this->fail('Failed to create status', 500);
            }
        }
        $status = $this->statusModel->find($statusId);

        return $this->respondCreated(['message' => $successMsg, 'status' => $status]);
    }

    public function get($statusId = 0)
    {
        $statusId = (int)getKey($statusId, "status");

        if ($statusId > 0) {
            if (!UserPermissionLib::userCanDo("statusMaster", 'view')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $status = $this->statusModel
            ->where('tenantId', 1) // Adding tenantId condition with default value 1
            ->find($statusId);

        if (!$status) {
            return $this->failNotFound('Status not found');
        }

        return $this->respond(['status' => true, 'message' => 'Status retrieved successfully', 'data' => $status]);
    }

    public function getDataTableColumns($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        /*****************************************
        EDIT 1: Define default columns here  
         *****************************************/
        $defaultColumns = [];
        $defaultColumns["SM_statusId"] = ["title" => "", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["SM_statusName"] = ["title" => "Status Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_module"] = ["title" => "Module", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_statusType"] = ["title" => "Status Type", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_isDefaultEntry"] = ["title" => "Is Default Entry", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_isEditable"] = ["title" => "Is Editable", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_isAction"] = ["title" => "Is Action", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_icon"] = ["title" => "Icons", "visible" => true, "orderable" => true, "searchable" => true];
        // $defaultColumns["SM_textColor"] = ["title" => "Text Color", "visible" => true, "orderable" => true, "searchable" => true];
        // $defaultColumns["SM_bgColor"] = ["title" => "BG Color", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_isActive"] = ["title" => "Status", "visible" => true, "orderable" => true, "searchable" => true];



        /*****************************************
        EDIT 2: Define required columns filter and type with options here
         *****************************************/
        //     $temp = $this->db->query("SELECT GROUP_CONCAT(module SEPARATOR '__') as filterData 
        // FROM statusMaster 
        // WHERE isDeleted = 0 
        // AND isActive = 1 
        // AND tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
        //         ->getRow()
        //         ->filterData;

        // $temp = $this->db->query("SELECT GROUP_CONCAT(module SEPARATOR '__') as filterData FROM statusMaster WHERE isActive = 1")->getRow()->filterData;


        //         $moduleFilters = explode("__", $temp);

        $isActiveFilter = ["1" => "Active", "0" => "Inactive"];
        $defaultColumns["SM_isActive"]["filterType"] = 'select';
        $defaultColumns["SM_isActive"]["filterOptions"] = $isActiveFilter;

        // $defaultColumns["SM_module"]["filterType"] = 'select';
        // $defaultColumns["SM_module"]["filterOptions"] = $moduleFilters;

        $statusTypeFilter = ["Open" => "Open", "Won" => "Won", "Lost" => "Lost"];
        $defaultColumns["SM_statusType"]["filterType"] = 'select';
        $defaultColumns["SM_statusType"]["filterOptions"] = $statusTypeFilter;

        $booleanFilter = ["1" => "Yes", "0" => "No"];
        $defaultColumns["SM_isEditable"]["filterType"] = 'select';
        $defaultColumns["SM_isEditable"]["filterOptions"] = $booleanFilter;

        $defaultColumns["SM_isDefaultEntry"]["filterType"] = 'select';
        $defaultColumns["SM_isDefaultEntry"]["filterOptions"] = $booleanFilter;

        $defaultColumns["SM_isAction"]["filterType"] = 'select';
        $defaultColumns["SM_isAction"]["filterOptions"] = $booleanFilter;

        /*****************************************
        EDIT 3: Define default columns here  
         ****************************************/
        $configData['defaultOrderColumn'] = 'SM_statusId';
        $configData['defaultOrderDirection'] = 'asc';


        /********************************************
        WARNING:: DO NOT EDIT BELOW THIS LINE
         ********************************************/

        // add index as array item "name" and converted to normal array.
        foreach ($defaultColumns as $key => &$column) {
            $column['name'] = $key;
        }
        $defaultColumns = array_values($defaultColumns);

        $userId = $this->user->userId;

        $ex = $this->db->query("SELECT `value` 
    FROM userSettings 
    WHERE userId = $userId 
    AND `key` = '$module' 
    ")
            ->getRow();

        if ($ex) {
            $columnSetting = json_decode($ex->value, true);

            // Step 1: Create an associative array for user-defined columns for quick lookup
            $userColumns = [];
            foreach ($columnSetting as $col) {
                $userColumns[$col['name']] = $col;
            }

            // Step 2: Reorder and update visibility in the master data
            $reorderedColumns = [];
            foreach ($columnSetting as $col) {
                foreach ($defaultColumns as $masterCol) {
                    if ($masterCol['name'] === $col['name']) {
                        // Update visibility from user settings
                        $masterCol['visible'] = (int)$col['visible'];
                        $reorderedColumns[] = $masterCol;
                        break;
                    }
                }
            }

            // Step 3: Update the master data with reordered columns, and keep the rest of the columns at the end to include all new columns
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

        $response = [
            'status' => true,
            "data" => $configData
        ];

        return $this->respond($response, 200);
    }


    public function getDataTableData()
    {
        /*****************************************
        EDIT 1: Define default select and where conditions here  
         *****************************************/
        $select = ["SM.textColor as SM_textColor, SM.bgColor as SM_bgColor"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []
        $where = ["SM.isDeleted = '0' "]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.

        $dbTable = 'statusMaster SM 
   '; // Adding tenantId condition to the statusMaster table
        // $where[] = "SM.tenantId = " . $this->user->tenantId;


        /********************************************
            WARNING:: DO NOT EDIT BELOW THIS LINE
         ********************************************/
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $temp = json_decode($this->getDataTableColumns($jsonInput['module'])->getBody())->data;
        $myColumns = [];
        foreach ($temp->columns as $col) {
            $myColumns[$col->name] = $col;
        }

        $queryParameters = [];
        $columns = $jsonInput['columns'];
        $seachWhere = [];

        $search = $jsonInput['search']['value'];
        $filters = $jsonInput['filters'];

        $isDownload = false;
        if (isset($jsonInput['downloadType']) or !isset($jsonInput['draw'])) {
            $isDownload = true;
        }

        // var_dump($columns);

        foreach ($columns as $column) {

            //ignroe column if does not exists in our column configuration. to protect against sql injection
            if (!isset($myColumns[$column['data']])) {
                continue;
            }

            $dbField = str_replace('_', ".", $column['data']);
            $columnName = $column['data'];
            $select[] =  $dbField . " as " . $columnName;

            if ($search != '' and $column['searchable'] == true) {
                $seachWhere[] = $dbField . " LIKE :searchTerm:";
                $queryParameters["searchTerm"] = "%$search%";
            }

            // check if filter data received from the client, and column has filter configuration
            if (isset($filters[$columnName]) and $filters[$columnName] != '' and isset($myColumns[$columnName]->filterType)) {

                //now check filter type.
                $filterType = $myColumns[$columnName]->filterType;

                if ($filterType == 'checkbox') {
                    $where[] = "$dbField IN :" . $columnName . "_checkbox:";
                    $queryParameters[$columnName . '_checkbox'] = $filters[$columnName];
                } else if ($filterType == 'date') {
                    $dateRange = dateFilterOptionRange($filters[$columnName]);
                    $where[] = "DATE($dbField) BETWEEN :" . $columnName . "_startDate: AND :" . $columnName . "_endDate:";
                    $queryParameters[$columnName . '_startDate'] = $dateRange[0];
                    $queryParameters[$columnName . '_endDate'] = $dateRange[1];
                } else if ($filterType == 'numberRange') {
                    $range = explode("-", $filters[$columnName]);
                    $where[] = "$dbField BETWEEN :" . $columnName . "_startRange: AND :" . $columnName . "_endRange:";
                    $queryParameters[$columnName . '_startRange'] = $range[0];
                    $queryParameters[$columnName . '_endRange'] = $range[1];
                } else if ($filterType == 'custom') {

                    /******************************************************************
                     * EDIT 2: Custom filter type, you can add your custom filters here based on field name
                     * ****************************************************************/
                    // if ($columnName == 'UM_lockoutUntil') {
                    //     if ($filters[$columnName] == 1) {
                    //         $where[] = "$dbField IS NOT NULL AND $dbField > :" . $columnName . "_now:";
                    //         $queryParameters[$columnName . '_now'] = timenow();
                    //     } else if ($filters[$columnName] == 0) {
                    //         $where[] = "$dbField IS NULL OR $dbField < :" . $columnName . "_now:";
                    //         $queryParameters[$columnName . '_now'] = timenow();
                    //     }
                    // }

                    /********************************************
                        WARNING:: DO NOT EDIT BELOW THIS LINE
                     ********************************************/
                } else {
                    $where[] = "$dbField = :" . $columnName . "_filter:";
                    $queryParameters[$columnName . '_filter'] = $filters[$columnName];
                }
            }
        }

        // Set Order By
        if (!empty($jsonInput['order'])) {
            $orderIndex = $jsonInput['order'][0]["column"];
            $orderDirection = $jsonInput['order'][0]["dir"];
            $orderColumn = $columns[$orderIndex]['data'];
            $orderBy = " ORDER BY $orderColumn $orderDirection";
        }

        // Build WHERE Clause
        if (!empty($searchWhere)) {
            $where[] = "(" . implode(' OR ', $searchWhere) . ")";
        }
        $whereClause = !empty($where) ? " WHERE " . implode(' AND ', $where) : "";

        // Set Limit and Offset
        $limit = !empty($jsonInput['length']) ? (int) $jsonInput['length'] : 10;
        $offset = !empty($jsonInput['start']) ? (int) $jsonInput['start'] : 0;

        $sql = "SELECT " . implode(", ", $select) . " FROM $dbTable $whereClause $orderBy LIMIT :limit: OFFSET :offset:";
        $queryParameters['limit'] = (int)$limit;
        $queryParameters['offset'] = (int)$offset;

        // $debugSql = $sql;
        // foreach ($queryParameters as $key => $value) {
        //     $debugSql = str_replace(":$key:", (is_array($value) ? implode(",", $value) : "'$value'"), $debugSql);
        // }

        $data = $this->db->query($sql, $queryParameters)->getResult();

        /****************************************************************
            EDIT 3: Add column totals data here if required, else leave empty
         ****************************************************************/
        $columnTotalsData = [];
        // $columnTotalsData['UM_serialNumber'] = 100; //you can run mysql query here to fetch total of the column

        foreach ($data as &$row) {
            $statusList = [];
            $temp = $this->db->query("SELECT * 
    FROM statusMaster 
    WHERE isActive = 1 
   ") // Adding tenantId condition with default value 1
                ->getResult();
            foreach ($temp as $t) {
                $statusList[$t->statusId] = $t;
            }

            if (!$isDownload) {
                /*******************************************************
                    NOTE: for html and data made for screen only, keep inside this if condition.
                 *******************************************************/
                $action = "<a href='" . base_url("statusMaster/editStatus/" . setKey($row->SM_statusId, "status")) . "'><i class='fa fa-edit'></i></a>";

                $row->SM_statusId = $row->SM_statusId . " " . $action;

                $status = "<span data-rowbgcolor='" . $row->SM_bgColor . "' style='color:" . $row->SM_textColor . "'><i class='{$row->SM_icon} fs-24 fs-25px' ></i>";

                $row->SM_icon = $status;
            }

            /*******************************************************
            general data for screen,printing,export will go here.
             *******************************************************/

            // $row->ITM_industryTypeName = printable($row->ITM_industryTypeName);
            if ($row->SM_isActive) {
                $row->SM_isActive = 'Active';
            } else {
                $row->SM_isActive = 'In Active';
            }
            if ($row->SM_isEditable) {
                $row->SM_isEditable = 'Yes';
            } else {
                $row->SM_isEditable = 'No';
            }
            if ($row->SM_isDefaultEntry) {
                $row->SM_isDefaultEntry = 'Yes';
            } else {
                $row->SM_isDefaultEntry = 'No';
            }
            if ($row->SM_isAction) {
                $row->SM_isAction = 'Yes';
            } else {
                $row->SM_isAction = 'No';
            }
            /********************************************
            WARNING:: DO NOT EDIT BELOW THIS LINE
             ********************************************/

            // unset item that does not exists in $columns, to avoid required select items like userId as we want to use it in data prepearation but not in the response
            foreach ($row as $key => $value) {
                if (!isset($myColumns[$key])) {
                    unset($row->$key);
                }
            }
        }

        $totalRecords = $this->db->query("SELECT COUNT(*) as total 
    FROM $dbTable 
    WHERE SM.isDeleted = 0 
   ") // Adding tenantId condition with default value 1
            ->getRow()->total;
        $fileteredRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable $whereClause", $queryParameters)->getRow()->total;

        $header = [];
        foreach ($columns as $column) {
            $header[] = $column['name'];
        }

        $response = [
            'draw' => $jsonInput['draw'],
            'module' => $jsonInput['module'],
            'header' => $header,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $fileteredRecords,
            'data' => $data,
            'columnTotals' => $columnTotalsData,
            // 'sql' => $sql
        ];

        return $this->respond($response, 200);
    }

    public function getStatusList($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        $status = $this->db->query("SELECT statusId as id, statusName as name FROM statusMaster WHERE module = '$module' AND isActive = 1 AND isDeleted = 0 AND isAction='0' AND isDefaultEntry='0' AND tenantId = " . $this->user->tenantId . " ORDER BY statusName")->getResult();

        $response = [
            'status' => true,
            "data" => $status
        ];

        return $this->respond($response, 200);
    }
}
