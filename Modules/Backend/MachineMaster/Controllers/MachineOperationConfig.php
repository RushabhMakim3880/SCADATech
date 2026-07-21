<?php

namespace Modules\Backend\MachineMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\MachineMaster\Models\MachineOperationConfigModel;


use CodeIgniter\API\ResponseTrait;

class MachineOperationConfig extends ApiBaseController
{
    use ResponseTrait;


    protected $MachineOperationConfigModel;

    public function __construct()
    {
        $this->MachineOperationConfigModel = new MachineOperationConfigModel();
    }

    public function save($operationConfigId = 0)
    {

        $operationConfigId = (int)getKey($operationConfigId, "");

        if ($operationConfigId > 0) {
            if (!UserPermissionLib::userCanDo("MachineOperationConfig", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("MachineOperationConfig", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['operationConfigId'] = $operationConfigId;

        // validation Logic will go here
        $rules['machineId'] = [
            'label'  => 'Machine Id',
            'rules'  => 'required|integer|is_natural'
        ];
        $rules['operationCode'] = [
            'label'  => 'Operation Code',
            'rules'  => 'required|max_length[50]'
        ];
        $rules['operationType'] = [
            'label'  => 'Operation Type',
            'rules'  => 'required|max_length[50]'
        ];
        $rules['operationLabel'] = [
            'label'  => 'Operation Label',
            'rules'  => 'required|max_length[100]'
        ];
        $rules['positionX'] = [
            'label'  => 'Position X',
            'rules'  => 'decimal'
        ];
        $rules['positionY'] = [
            'label'  => 'Position Y',
            'rules'  => 'decimal'
        ];

        $rules['plcTriggerTag'] = [
            'label'  => 'Plc Trigger Tag',
            'rules'  => 'required|integer|is_natural'
        ];
        $rules['plcAckTag'] = [
            'label'  => 'Plc Ack Tag',
            'rules'  => 'required|integer|is_natural'
        ];



        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }

        //date/time/datetime conversion logic here.
        if (!empty($jsonInput['createdAt'])) {
            $jsonInput['createdAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['createdAt'])));
        }
        if (!empty($jsonInput['updatedAt'])) {
            $jsonInput['updatedAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['updatedAt'])));
        }


        $successMsg = 'Saved Successfully';
        if ($operationConfigId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->MachineOperationConfigModel->update($operationConfigId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {

            $jsonInput['tenantId'] = $this->user->tenantId;


            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $operationConfigId = $this->MachineOperationConfigModel->insert($jsonInput);

            if (!$operationConfigId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "machineOperationConfig", "operationConfigId", $operationConfigId);
        }

        $data = $this->MachineOperationConfigModel->find($operationConfigId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($operationConfigId)
    {
        $operationConfigId = (int)getKey($operationConfigId, "");

        if (!UserPermissionLib::userCanDo("MachineOperationConfig", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->MachineOperationConfigModel->find($operationConfigId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }


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

        $defaultColumns['machineOperationConfig_operationConfigId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['machineMaster_machineName'] = ['title' => 'Machine Name', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_operationCode'] = ['title' => 'Operation Code', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_operationType'] = ['title' => 'Operation Type', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_operationLabel'] = ['title' => 'Operation Label', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_positionX'] = ['title' => 'Position X', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_positionY'] = ['title' => 'Position Y', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_isMovableHead'] = ['title' => 'Is Movable Head', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_tagName'] = ['title' => 'PLC Tag', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_plcAdditionalData'] = ['title' => 'Plc Additional Data', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_description'] = ['title' => 'Description', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['machineOperationConfig_updatedAt'] = ['title' => 'Updated At', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];


        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $temp = $this->db->query("SELECT GROUP_CONCAT(machineName SEPARATOR '__') as `filterData` FROM machineMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['machineMaster_machineName']['filterType'] = 'select';
        $defaultColumns['machineMaster_machineName']['filterOptions'] = $foreignFilters;


        $filterData = ['1' => 'Yes', '0' => 'No'];
        $defaultColumns['machineOperationConfig_isMovableHead']['filterType'] = 'select';
        $defaultColumns['machineOperationConfig_isMovableHead']['filterOptions'] = $filterData;


        $temp = $this->db->query("SELECT GROUP_CONCAT(tagName SEPARATOR '__') as `filterData` FROM plcTagMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['plcTagMaster_tagName']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_tagName']['filterOptions'] = $foreignFilters;


        $temp = $this->db->query("SELECT GROUP_CONCAT(tagName SEPARATOR '__') as `filterData` FROM plcTagMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['plcTagMaster_tagName']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_tagName']['filterOptions'] = $foreignFilters;


        $defaultColumns['machineOperationConfig_createdAt']['filterType'] = 'date';
        $defaultColumns['machineOperationConfig_createdAt']['filterOptions'] = dateFilterOptions('past');


        $defaultColumns['machineOperationConfig_updatedAt']['filterType'] = 'date';
        $defaultColumns['machineOperationConfig_updatedAt']['filterOptions'] = dateFilterOptions('past');

        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'machineOperationConfig_operationConfigId';
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
        $select = ["machineOperationConfig.serialNo as machineOperationConfig_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "machineOperationConfig.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'machineOperationConfig LEFT JOIN machineMaster machineMaster ON machineMaster.machineId = machineOperationConfig.machineId 
LEFT JOIN plcTagMaster plcTagMaster ON plcTagMaster.tagId = machineOperationConfig.plcAckTag 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = machineOperationConfig.tenantId 
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

        // $debugSql = $sql;
        // foreach ($queryParameters as $key => $value) {
        //     $debugSql = str_replace(":$key:", (is_array($value) ? implode(",", $value) : "'$value'"), $debugSql);
        // }
        // debug($queryParameters);
        // debug($debugSql);
        // die();

        $data = $this->db->query($sql, $queryParameters)->getResult();


        /****************************************************************
                EDIT 3: Add column totals data here if required, else leave empty
         ****************************************************************/
        $columnTotalsData = [];
        //$columnTotalsData['NS_price'] = 12345; //you can run mysql query here to fetch total of the column


        $config = config('AppConfig');
        $mobileView = [];

        foreach ($data as $k => &$row) {

            $primaryKey = $row->machineOperationConfig_operationConfigId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                $dropdownItems = [];
                //edit button
                if (UserPermissionLib::userCanDo("MachineOperationConfig", 'edit')) {
                    $dropdownItems[] = ['label' => 'Edit', 'class' => 'apiPopup text-warning', 'icon' => 'fa fa-edit', 'attributes' => "data-size='xl' data-title='Edit Machine Operation Config' data-endpoint='" . ("MachineOperationConfig/addMachineOperation/" . setKey($primaryKey, "")) . "'"];
                }


                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->machineOperationConfig_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->machineOperationConfig_operationConfigId = $dropdownHtml;



                $mobileView[$k] = [
                    "titleBox1" => "",
                    "descriptionBox1" => "",
                    "titleBox2" => "",
                    "descriptionBox2" => "",
                    "actionBox" => '',
                    "statusBox" => "",
                    "dateBox" => "",
                ];
            }

            /*******************************************************
                EDIT 5: general data for screen,printing,export will go here.
             *******************************************************/

            $enumList = ['1' => 'Yes', '0' => 'No'];
            $row->machineOperationConfig_isMovableHead = $enumList[$row->machineOperationConfig_isMovableHead];
            $row->machineOperationConfig_createdAt = myDateTimeFormat($row->machineOperationConfig_createdAt);
            $row->machineOperationConfig_updatedAt = myDateTimeFormat($row->machineOperationConfig_updatedAt);


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

    public function delete($operationConfigId = 0)
    {
        $operationConfigId = (int)getKey($operationConfigId, "");

        if (!UserPermissionLib::userCanDo("MachineOperationConfig", 'delete')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($operationConfigId == 0) {
            return $this->fail('Invalid request', 400);
        }

        //set isDeleted = 1
        $this->MachineOperationConfigModel->update($operationConfigId, ['isDeleted' => 1]);

        // or put delete logic here if not using isDeleted field
        // $this->MachineOperationConfigModel->delete($operationConfigId);

        return $this->respondDeleted(['message' => 'Deleted successfully']);
    }




    public function getItemRecipeSteps()
    {
        $industryTypes = $this->db->query("SELECT IR.operationConfigId as id, IR.operationLabel AS `name` FROM machineOperationConfig IR WHERE  IR.tenantId = " . $this->user->tenantId . "")->getResult();

        foreach ($industryTypes as &$type) {
            $type->name = printable($type->name);
        }

        $response = [
            'status' => true,
            "message" => "",
            "data" => $industryTypes
        ];

        return $this->respond($response, 200);
    }
}
