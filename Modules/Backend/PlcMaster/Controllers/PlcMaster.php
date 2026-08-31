<?php

namespace Modules\Backend\PlcMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\PlcMaster\Models\PlcMasterModel;
use Modules\Backend\PlcMaster\Models\PlcTagMasterModel;



use CodeIgniter\API\ResponseTrait;

class PlcMaster extends ApiBaseController
{
    use ResponseTrait;


    protected $PlcMasterModel;
    protected $PlcTagMasterModel;


    public function __construct()
    {
        $this->PlcMasterModel = new PlcMasterModel();
        $this->PlcTagMasterModel = new PlcTagMasterModel();
    }

    public function save($plcId = 0)
    {

        $plcId = (int)getKey($plcId, "plc");

        if ($plcId > 0) {
            if (!UserPermissionLib::userCanDo("PlcMaster", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("PlcMaster", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['plcId'] = $plcId;



        // validation Logic will go here
        $rules['machineId'] = [
            'label'  => 'Machine Id',
            'rules'  => 'required|integer|is_natural'
        ];
        $rules['plcName'] = [
            'label'  => 'Plc Name',
            'rules'  => 'required|max_length[100]'
        ];
        $rules['plcName'] = [
            'label'  => 'Plc Name',
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
        $plcTagMaster = $jsonInput['plcTagMaster'] ?? [];
        unset($jsonInput['plcTagMaster']);

        foreach ($plcTagMaster as &$row) {
            if (!is_array($row)) {
                $row = [$row];
            }
        }

        $plcTagMaster = $this->processChildTableData($plcTagMaster, "tagGroupId");

        //remove empty products
        foreach ($plcTagMaster as $k => &$row) {
            if ($row['tagGroupId'] == "") {
                unset($plcTagMaster[$k]);
            }
            $row['tenantId'] = $this->user->tenantId;
            $row['scaleFactor'] = 1;
            $row['offset'] = 0;
        }


        //date/time/datetime conversion logic here.
        if (!empty($jsonInput['createdAt'])) {
            $jsonInput['createdAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['createdAt'])));
        }
        if (!empty($jsonInput['updatedAt'])) {
            $jsonInput['updatedAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['updatedAt'])));
        }


        $successMsg = 'Saved Successfully';
        if ($plcId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->PlcMasterModel->update($plcId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {

            $jsonInput['tenantId'] = $this->user->tenantId;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $plcId = $this->PlcMasterModel->insert($jsonInput);

            if (!$plcId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "plcMaster", "plcId", $plcId);
        }


        if (!empty($plcTagMaster)) {
            $this->syncChildTable("plcTagMaster", "tagId", "plcId", $plcId, $plcTagMaster);
        }

        $data = $this->PlcMasterModel->find($plcId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($plcId)
    {
        $plcId = (int)getKey($plcId, "plc");

        if (!UserPermissionLib::userCanDo("PlcMaster", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->PlcMasterModel->find($plcId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->plcTagMaster = $this->getChildTableData("plcTagMaster", "plcId", $plcId);
        // process item for ajax based dropdown
        // foreach ($details->plcTagMaster as &$row) {
        //     $sql = "SELECT PT.groupName
        //                 FROM plcTagGroupMaster PT 
        //                 WHERE PT.tenantId =  " . $this->user->tenantId . " AND PT.tagGroupId = " . $row["tagGroupId"];
        //     $item = $this->db->query($sql)->getRow();
        //     $row['tagGroupId'] = [
        //         "id" => $row['tagGroupId'],
        //         "text" => $item->groupName
        //     ];
        // }

        return $this->respond(['status' => true, 'message' => 'Data retrived successfully', 'data' => $details]);
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

        $defaultColumns['plcMaster_plcId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['machineMaster_machineName'] = ['title' => 'Machine Name', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcMaster_plcName'] = ['title' => 'Plc Name', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcMaster_protocol'] = ['title' => 'Protocol', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcMaster_ipAddress'] = ['title' => 'Ip Address', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcMaster_port'] = ['title' => 'Port', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcMaster_modbusDeviceId'] = ['title' => 'Modbus Device Id', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcMaster_description'] = ['title' => 'Description', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcMaster_status'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcMaster_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];


        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $temp = $this->db->query("SELECT GROUP_CONCAT(machineName SEPARATOR '__') as `filterData` FROM machineMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['machineMaster_machineName']['filterType'] = 'select';
        $defaultColumns['machineMaster_machineName']['filterOptions'] = $foreignFilters;


        $filterData = ['modbus-tcp' => 'Modbus-tcp', 'opc-ua' => 'Opc-ua', 'mqtt' => 'Mqtt', 'custom' => 'Custom'];
        $defaultColumns['plcMaster_protocol']['filterType'] = 'select';
        $defaultColumns['plcMaster_protocol']['filterOptions'] = $filterData;

        $filterData = ["1" => "Active", "0" => "In Active"];
        $defaultColumns["plcMaster_status"]["filterType"] = 'select';
        $defaultColumns["plcMaster_status"]["filterOptions"] = $filterData;


        $defaultColumns['plcMaster_createdAt']['filterType'] = 'date';
        $defaultColumns['plcMaster_createdAt']['filterOptions'] = dateFilterOptions('past');


        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'plcMaster_plcId';
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
        $select = ["plcMaster.serialNo as plcMaster_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "plcMaster.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.

        $dbTable = 'plcMaster LEFT JOIN machineMaster machineMaster ON machineMaster.machineId = plcMaster.machineId 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = plcMaster.tenantId 
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

        foreach ($data as &$row) {

            $primaryKey = $row->plcMaster_plcId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                $dropdownItems = [];
                //edit button
                if (UserPermissionLib::userCanDo("PlcMaster", 'edit')) {
                    // $dropdownItems[] = ['label' => 'Edit', 'class' => 'apiPopup text-warning', 'icon' => 'fa fa-edit', 'attributes' => "data-size='xl' data-title='Addplcmaster Form' data-endpoint='" . ("PlcMaster/addPlcMaster/" . setKey($primaryKey, "plc")) . "'"];
                    $dropdownItems[] = [
                        'label' => 'Edit',
                        'href' => base_url("PlcMaster/editPlcMaster/" . setKey($primaryKey, "plc")),
                        'class' => 'dropdown-item text-primary',
                        'icon' => 'fa fa-pencil-alt',
                        'attributes' => "target='' title='Edit Plc'"
                    ];

                    $dropdownItems[] = [
                        'label' => 'Connect',
                        'href' => "javascript:;",
                        'class' => 'dropdown-item text-primary apiAction',
                        'icon' => 'fa fa-play',
                        'attributes' => "title='Connect PLC' data-endpoint='api/OpMasterFront/initPlc/" . setKey($primaryKey, "plc") . "'"
                    ];

                    $dropdownItems[] = [
                        'label' => 'Sync Tags',
                        'href' => "javascript:;",
                        'class' => 'dropdown-item text-primary apiAction',
                        'icon' => 'fa fa-refresh',
                        'attributes' => "title='Sync Tags From PLC' data-endpoint='api/OpMasterFront/syncTags/" . setKey($primaryKey, "plc") . "'"
                    ];
                }

                $dropdownItems[] = ['label' => 'Tag Config', 'href' => base_url("PlcTagMaster/managePlctagmaster/" . setKey($primaryKey, "plc")), 'class' => 'dropdown-item text-primary', 'icon' => 'fas fa-tags', 'attributes' => "target='' title='Tag Config'"];

                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->plcMaster_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->plcMaster_plcId = $dropdownHtml;

                //display active status

                if ($row->plcMaster_status == 1) {
                    $row->plcMaster_status = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/PlcMaster/changeStatus/" . setKey($primaryKey, "plc") . "'>Active</span>";
                } else {
                    $row->plcMaster_status = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/PlcMaster/changeStatus/" . setKey($primaryKey, "plc") . "'>In Active</span>";
                }
            } else {
                /*******************************************************
                specific data for printing,export will go here.
                 *******************************************************/
                if (($row->plcMaster_status == '1')) {
                    $row->plcMaster_status = "Active";
                } else {
                    $row->plcMaster_status = "In Active";
                }
            }

            /*******************************************************
                EDIT 5: general data for screen,printing,export will go here.
             *******************************************************/


            $row->plcMaster_createdAt = myDateTimeFormat($row->plcMaster_createdAt);


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
            'columnTotals' => $columnTotalsData,
            'extraData' => [
                "totalRecords" => $totalRecords,
            ],
            // 'sql' => $sql
        ];

        return $this->respond($response, 200);
    }

    public function changeStatus($plcId = 0)
    {
        $plcId = (int)getKey($plcId, "plc");

        if (!UserPermissionLib::userCanDo("PlcMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $plcData = $this->PlcMasterModel->where('tenantId', $this->user->tenantId)->find($plcId);

        if (!$plcData) {
            return $this->failNotFound('Data found');
        }

        $status = $plcData->status == 1 ? 0 : 1;

        // $this->db->query("UPDATE plcMaster SET plcMaster = ? WHERE plcId = ? AND tenantId = " . $this->user->tenantId . "", [$status, $plcId]);
        $this->db->query("UPDATE plcMaster SET status = ? WHERE plcId = ? AND tenantId = " . $this->user->tenantId . "", [$status, $plcId]);

        $response = [
            'status' => true,
            "message" => " Status updated successfully",
        ];

        return $this->respond($response, 200);
    }

    public function getPlcList()
    {
        $plcData = $this->db->query("SELECT P.plcId as id, P.plcName AS `name` FROM plcMaster P WHERE P.status = 1 AND P.tenantId = " . $this->user->tenantId . "")->getResult();

        foreach ($plcData as &$type) {
            $type->name = printable($type->name);
        }

        $response = [
            'status' => true,
            "message" => "",
            "data" => $plcData
        ];

        return $this->respond($response, 200);
    }

    public function getPlcTagList()
    {
        $tagData = $this->db->query("SELECT PT.tagId as id, PT.tagName AS `name`,PT.dataType FROM plcTagMaster PT WHERE PT.isActive = 1 AND PT.tenantId = " . $this->user->tenantId . "")->getResult();

        foreach ($tagData as &$type) {
            $type->name = printable($type->name) . ' (' . $type->dataType . ')';
        }


        $response = [
            'status' => true,
            "message" => "",
            "data" => $tagData
        ];

        return $this->respond($response, 200);
    }
}
