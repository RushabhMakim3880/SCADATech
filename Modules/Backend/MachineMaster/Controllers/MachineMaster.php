<?php

namespace Modules\Backend\MachineMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\MachineMaster\Models\MachineMasterModel;


use CodeIgniter\API\ResponseTrait;

class MachineMaster extends ApiBaseController
{
    use ResponseTrait;


    protected $MachineMasterModel;

    public function __construct()
    {
        $this->MachineMasterModel = new MachineMasterModel();
    }

    public function save($machineId = 0)
    {

        $machineId = (int)getKey($machineId, "machineMaster");

        if ($machineId > 0) {
            if (!UserPermissionLib::userCanDo("MachineMaster", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("MachineMaster", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['machineId'] = $machineId;

        // validation Logic will go here

        if ($machineId > 0) {
            $rules['machineCode'] = [
                'label'  => 'Machine Code',
                'rules'  => 'required|max_length[50]',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                    'max_length' => 'The {field} cannot exceed 50 characters.',
                ]
            ];
        } else {
            $rules['machineId'] = [
                'label'  => 'Machine ID',
                'rules'  => 'permit_empty|integer|is_natural',
                'errors' => [
                    'integer' => 'The {field} must be an integer.'
                ]
            ];
            $rules['machineCode'] = [
                'label' => 'Machine Code',
                'rules' => 'required|max_length[50]|is_unique[machineMaster.machineCode]',
                'errors' => [
                    'is_unique' => 'This Machine Code already exists. Please choose a different one.'
                ]
            ];
        }

        $rules['machineName'] = [
            'label'  => 'Machine Name',
            'rules'  => 'required|max_length[100]'
        ];
        $rules['machineType'] = [
            'label'  => 'Machine Type',
            'rules'  => 'required|max_length[100]'
        ];


        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }


        /******************************
         * Multiline data processing and validation Started.
         ******************************/
        // Normalize single row data into an array format
        $machineDetails = $jsonInput['machineDetails'] ?? [];
        unset($jsonInput['machineDetails']);

        foreach ($machineDetails as &$row) {
            if (!is_array($row)) {
                $row = [$row];
            }
        }

        $machineDetails = $this->processChildTableData($machineDetails, "headName");

        //remove empty products
        foreach ($machineDetails as $k => &$row) {
            if ($row['headName'] == "") {
                unset($machineDetails[$k]);
            }
            $row['tenantId'] = $this->user->tenantId;
        }

        //disallow empty machineDetails
        if (empty($machineDetails)) {
            return $this->fail('Machine Details cannot be empty', 400);
        }

        // Ensure all required fields are present
        foreach ($machineDetails as $k => &$row) {
            if (empty($row['headName']) || empty($row['headType']) || empty($row['xPosition']) || empty($row['side'])) {
                return $this->fail('All fields in Machine Details are required', 400);
            }

            // for headType marking, ensure markingCassets is a number and >0
            if ($row['headType'] == 'Marking' && (!isset($row['markingCassets']) || !is_numeric($row['markingCassets']) || $row['markingCassets'] <= 0)) {
                return $this->fail('Marking Cassets must be a number greater than 0 for Marking head type', 400);
            }

            // for headType punching side must be A or B
            if ($row['headType'] == 'Punching' && !in_array($row['side'], ['A', 'B'])) {
                return $this->fail('Side must be A or B for Punching head type', 400);
            }

            // for headType not punching, side must be N/A
            if ($row['headType'] != 'Punching' && $row['side'] != 'N/A') {
                return $this->fail('Side must be N/A for non-Punching head types', 400);
            }

            // make sure xPosition is a numeric decimal
            if (!isset($row['xPosition']) || !is_numeric($row['xPosition'])) {
                return $this->fail('X Position must be a numeric value', 400);
            }
        }


        //date/time/datetime conversion logic here.
        if (!empty($jsonInput['createdAt'])) {
            $jsonInput['createdAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['createdAt'])));
        }
        if (!empty($jsonInput['updatedAt'])) {
            $jsonInput['updatedAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['updatedAt'])));
        }


        $successMsg = 'Saved Successfully';
        if ($machineId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->MachineMasterModel->update($machineId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {

            $jsonInput['tenantId'] = $this->user->tenantId;


            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $machineId = $this->MachineMasterModel->insert($jsonInput);

            if (!$machineId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "machineMaster", "machineId", $machineId);
        }

        if (!empty($machineDetails)) {
            $this->syncChildTable("machineDetails", "machineDetailId", "machineId", $machineId, $machineDetails);
        }

        $data = $this->MachineMasterModel->find($machineId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($machineId)
    {
        $machineId = (int)getKey($machineId, "machineMaster");

        if (!UserPermissionLib::userCanDo("MachineMaster", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->MachineMasterModel->find($machineId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->machineDetails = $this->getChildTableData("machineDetails", "machineId", $machineId);

        return $this->respond(['status' => true, 'message' => '', 'data' => $details]);
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

        $defaultColumns['machineMaster_machineId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['machineMaster_machineCode'] = ['title' => 'Machine Code', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineMaster_machineName'] = ['title' => 'Machine Name', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineMaster_machineType'] = ['title' => 'Machine Type', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineMaster_isActive'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineMaster_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];

        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $isActiveFilter = ["1" => "Active", "0" => "In Active"];
        $defaultColumns["machineMaster_isActive"]["filterType"] = 'select';
        $defaultColumns["machineMaster_isActive"]["filterOptions"] = $isActiveFilter;

        $defaultColumns['machineMaster_createdAt']['filterType'] = 'date';
        $defaultColumns['machineMaster_createdAt']['filterOptions'] = dateFilterOptions('past');


        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'machineMaster_machineId';
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

        $ex = $this->db->query("SELECT `value` FROM userSettings WHERE userId = $userId AND tenantId='" . $this->user->tenantId . "' AND `key` = '$module'")->getRow();

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
        $select = ["machineMaster.serialNo as machineMaster_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "machineMaster.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'machineMaster LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = machineMaster.tenantId 
';


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
        $searchWhere = [];

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
                $searchWhere[] = $dbField . " LIKE :searchTerm:";
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
        $orderBy = "";
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



        $data = $this->db->query($sql, $queryParameters)->getResult();


        /****************************************************************
                EDIT 3: Add column totals data here if required, else leave empty
         ****************************************************************/
        $columnTotalsData = [];
        //$columnTotalsData['NS_price'] = 12345; //you can run mysql query here to fetch total of the column


        $config = config('AppConfig');
        $mobileView = [];

        foreach ($data as $k => &$row) {

            $primaryKey = $row->machineMaster_machineId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                // //edit button
                $editButton = '';
                if (UserPermissionLib::userCanDo("MachineMaster", 'edit')) {
                    $editButton = "<a href='" . base_url("MachineMaster/editMachine/" . setKey($primaryKey, "machineMaster")) . "' 
                      class='btn btn-xs btn-warning ms-2' 

                      title='Edit Machine'>
                      <i class='fa fa-pencil-alt'></i>
                   </a>";
                }

                $row->machineMaster_machineId = $row->machineMaster_serialNo . ' ' . $editButton;

                //display active status

                if ($row->machineMaster_isActive == 1) {
                    $row->machineMaster_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/MachineMaster/changeStatus/" . setKey($primaryKey, "") . "'>Active</span>";
                } else {
                    $row->machineMaster_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/MachineMaster/changeStatus/" . setKey($primaryKey, "") . "'>In Active</span>";
                }


                $mobileView[$k] = [
                    "titleBox1" => "$row->machineMaster_machineCode | $row->machineMaster_machineName",
                    "descriptionBox1" => "",
                    "titleBox2" => "",
                    "descriptionBox2" => "",
                    "actionBox" => '',
                    "statusBox" => "$row->machineMaster_isActive",
                    "dateBox" => "<span class='badge bg-secondary'>Created At: " . myDateTimeFormat($row->machineMaster_createdAt) . "</span>",

                ];
            } else {
                /*******************************************************
                specific data for printing,export will go here.
                 *******************************************************/
                if (($row->machineMaster_isActive == '1')) {
                    $row->machineMaster_isActive = "Active";
                } else {
                    $row->machineMaster_isActive = "In Active";
                }
            }


            /*******************************************************
                EDIT 5: general data for screen,printing,export will go here.
             *******************************************************/

            $row->machineMaster_createdAt = myDateTimeFormat($row->machineMaster_createdAt);
            $row->machineMaster_machineName = printable($row->machineMaster_machineName);
            $row->machineMaster_machineType = printable($row->machineMaster_machineType);

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

        $totalRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable WHERE 1")->getRow()->total;
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
            'mobileView' => $mobileView,
            'columnTotals' => $columnTotalsData,
            'extraData' => [
                "totalRecords" => $totalRecords,
            ],
            // 'sql' => $sql
        ];

        return $this->respond($response, 200);
    }

    public function changeStatus($machineId = 0)
    {
        $machineId = (int)getKey($machineId, "");

        if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $machine = $this->MachineMasterModel->where('tenantId', $this->user->tenantId)->find($machineId);

        if (!$machine) {
            return $this->failNotFound('Item not found');
        }

        $status = $machine->isActive == 1 ? 0 : 1;

        $this->db->query("UPDATE machineMaster SET isActive = ? WHERE machineId = ? AND tenantId = " . $this->user->tenantId . "", [$status, $machineId]);

        $response = [
            'status' => true,
            "message" => "Machine Status updated successfully",
        ];

        return $this->respond($response, 200);
    }

    public function getMachineList()
    {
        $machineData = $this->db->query("SELECT M.machineId as id, M.machineName AS `name` FROM machineMaster M WHERE M.isActive = 1 AND M.tenantId = " . $this->user->tenantId . "")->getResult();

        foreach ($machineData as &$type) {
            $type->name = printable($type->name);
        }

        $response = [
            'status' => true,
            "message" => "",
            "data" => $machineData
        ];

        return $this->respond($response, 200);
    }

    public function getMachineSetup()
    {
        $data = $this->db->query("SELECT * FROM machineSetup WHERE tenantId = " . $this->user->tenantId)->getResult();

        $machineData = [];
        foreach ($data as $row) {
            $machineData["$row->machineDetailId||$row->childId"] = $row->value;
        }
        $response = [
            'status' => true,
            "message" => "",
            "data" => $machineData
        ];

        return $this->respond($response, 200);
    }

    public function saveMachineSetup()
    {
        $input = $this->getInputData();
        $jsonInput = $input["jsonInput"];

        foreach ($jsonInput as $k => $v) {
            $machineDetailId = explode("||", $k)[0];
            $childId =  explode("||", $k)[1];

            $ex = $this->db->query("SELECT * FROM machineSetup WHERE tenantId = " . $this->user->tenantId . " AND machineDetailId='$machineDetailId' AND childId='$childId'")->getRow();

            if ($ex) {
                $this->db->query("UPDATE machineSetup SET value = ? WHERE machineSetupId = ?", [$v, $ex->machineSetupId]);
            } else {
                $this->db->query("INSERT INTO machineSetup (tenantId, machineDetailId, childId, value) VALUES (?, ?, ?, ?)", [$this->user->tenantId, $machineDetailId, $childId, $v]);
            }
        }

        $response = [
            'status' => true,
            "message" => "Machine Setup saved successfully",
        ];

        return $this->respond($response, 200);
    }
}
