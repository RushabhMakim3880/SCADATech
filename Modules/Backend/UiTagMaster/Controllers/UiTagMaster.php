<?php

namespace Modules\Backend\UiTagMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\UiTagMaster\Models\UiTagMasterModel;


use CodeIgniter\API\ResponseTrait;

class UiTagMaster extends ApiBaseController
{
    use ResponseTrait;


    protected $UiTagMasterModel;

    public function __construct()
    {
        $this->UiTagMasterModel = new UiTagMasterModel();
    }

    public function save($uiTagId = 0)
    {

        $uiTagId = (int)getKey($uiTagId, "Uitagmast");

        if ($uiTagId > 0) {
            if (!UserPermissionLib::userCanDo("UiTagMaster", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("UiTagMaster", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['uiTagId'] = $uiTagId;

        // validation Logic will go here
        $rules['tagId'] = [
            'label'  => 'Tag Id',
            'rules'  => 'required|integer|is_natural'
        ];
        $rules['tagGroupId'] = [
            'label'  => 'Tag Group Id',
            'rules'  => 'required|integer|is_natural'
        ];
        $rules['tagName'] = [
            'label'  => 'Tag Name',
            'rules'  => 'required|max_length[100]'
        ];


        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }

        //date/time/datetime conversion logic here.
        if (!empty($jsonInput['updatedAt'])) {
            $jsonInput['updatedAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['updatedAt'])));
        }
        if (!empty($jsonInput['createdAt'])) {
            $jsonInput['createdAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['createdAt'])));
        }

        $jsonInput['minValue'] = (isset($jsonInput['minValue']) && $jsonInput['minValue'] !== '' && $jsonInput['minValue'] !== null) ? (float)$jsonInput['minValue'] : null;
        $jsonInput['maxValue'] = (isset($jsonInput['maxValue']) && $jsonInput['maxValue'] !== '' && $jsonInput['maxValue'] !== null) ? (float)$jsonInput['maxValue'] : null;


        $successMsg = 'Saved Successfully';
        if ($uiTagId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->UiTagMasterModel->update($uiTagId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {

            $jsonInput['tenantId'] = $this->user->tenantId;


            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $uiTagId = $this->UiTagMasterModel->insert($jsonInput);

            if (!$uiTagId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "uiTagMaster", "uiTagId", $uiTagId);
        }

        $data = $this->UiTagMasterModel->find($uiTagId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($uiTagId)
    {
        $uiTagId = (int)getKey($uiTagId, "Uitagmast");

        if (!UserPermissionLib::userCanDo("UiTagMaster", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->UiTagMasterModel->find($uiTagId);

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

        $defaultColumns['uiTagMaster_uiTagId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => false];
        $defaultColumns['uiTagMaster_tagName'] = ['title' => 'Scada Tag', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcTagGroupMaster_groupName'] = ['title' => 'Group Name', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_tagName'] = ['title' => 'PLC Tag', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_dataType'] = ['title' => 'Data Type', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_readWrite'] = ['title' => 'Read Write', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['uiTagMaster_isActive'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['uiTagMaster_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];


        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $temp = $this->db->query("SELECT GROUP_CONCAT(tagName SEPARATOR '__') as `filterData` FROM plcTagMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['plcTagMaster_tagName']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_tagName']['filterOptions'] = $foreignFilters;


        $temp = $this->db->query("SELECT GROUP_CONCAT(groupName SEPARATOR '__') as `filterData` FROM plcTagGroupMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['plcTagGroupMaster_groupName']['filterType'] = 'select';
        $defaultColumns['plcTagGroupMaster_groupName']['filterOptions'] = $foreignFilters;


        $filterData = ['1' => 'Yes', '0' => 'No'];
        $defaultColumns['uiTagMaster_isActive']['filterType'] = 'select';
        $defaultColumns['uiTagMaster_isActive']['filterOptions'] = $filterData;


        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'uiTagMaster_uiTagId';
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
        $select = ["uiTagMaster.serialNo as uiTagMaster_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "uiTagMaster.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'uiTagMaster LEFT JOIN userMaster userMaster ON userMaster.userId = uiTagMaster.createdBy 
                    LEFT JOIN plcTagGroupMaster plcTagGroupMaster ON plcTagGroupMaster.tagGroupId = uiTagMaster.tagGroupId 
                    LEFT JOIN plcTagMaster plcTagMaster ON plcTagMaster.tagId = uiTagMaster.tagId 
                    LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = uiTagMaster.tenantId 
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

            $primaryKey = $row->uiTagMaster_uiTagId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                //edit button
                $editButton = '';
                if (UserPermissionLib::userCanDo("UiTagMaster", 'edit')) {
                    $editButton = "<a href='#'class='btn btn-xs btn-warning ms-2 apiPopup'
                      data-size='xl'data-title='Edit Scada Tag'
                      data-endpoint='" . base_url("UiTagMaster/addUiTag/" . setKey($primaryKey, "Uitagmast")) . "'
                      title='Edit Scada Tag'>
                      <i class='fa fa-pencil-alt'></i>
                   </a>";
                }

                $row->uiTagMaster_uiTagId = $row->uiTagMaster_serialNo . ' ' . $editButton;

                //display active status

                if ($row->uiTagMaster_isActive == 1) {
                    $row->uiTagMaster_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/UiTagMaster/changeStatus/" . setKey($primaryKey, "Uitagmast") . "'>Active</span>";
                } else {
                    $row->uiTagMaster_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/UiTagMaster/changeStatus/" . setKey($primaryKey, "Uitagmast") . "'>In Active</span>";
                }

                $mobileView[$k] = [
                    "titleBox1" => "",
                    "descriptionBox1" => "",
                    "titleBox2" => "",
                    "descriptionBox2" => "",
                    "actionBox" => '',
                    "statusBox" => "",
                    "dateBox" => "",
                ];
            } else {
                /*******************************************************
                specific data for printing,export will go here.
                 *******************************************************/
                if (($row->uiTagMaster_isActive == '1')) {
                    $row->uiTagMaster_isActive = "Active";
                } else {
                    $row->uiTagMaster_isActive = "In Active";
                }
            }

            /*******************************************************
                EDIT 5: general data for screen,printing,export will go here.
             *******************************************************/

            $row->plcTagMaster_tagName = printable($row->plcTagMaster_tagName);
            $row->uiTagMaster_tagName = printable($row->uiTagMaster_tagName);
            $row->plcTagGroupMaster_groupName = printable($row->plcTagGroupMaster_groupName);
            $row->uiTagMaster_createdAt = myDateTimeFormat($row->uiTagMaster_createdAt);


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

    public function changeStatus($uiTagId = 0)
    {
        $uiTagId = (int)getKey($uiTagId, "Uitagmast");

        if (!UserPermissionLib::userCanDo("UiTagMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $uiTagData = $this->UiTagMasterModel->where('tenantId', $this->user->tenantId)->find($uiTagId);

        if (!$uiTagData) {
            return $this->failNotFound('Data found');
        }

        $status = $uiTagData->isActive == 1 ? 0 : 1;

        $this->db->query("UPDATE uiTagMaster SET isActive = ? WHERE uiTagId = ? AND tenantId = " . $this->user->tenantId . "", [$status, $uiTagId]);

        $response = [
            'status' => true,
            "message" => " Status updated successfully",
        ];

        return $this->respond($response, 200);
    }

    public function getUiTag()
    {
        $plcData = $this->db->query("SELECT U.uiTagId as id, U.tagName AS `name`,P.dataType FROM uiTagMaster U LEFT JOIN plcTagMaster P ON P.tagId = U.tagId WHERE U.isActive = 1 AND U.tenantId = " . $this->user->tenantId . "")->getResult();

        foreach ($plcData as &$type) {
            // $type->name = printable($type->name);
            $type->name = printable($type->name) . ' (' . $type->dataType . ')';
        }

        $response = [
            'status' => true,
            "message" => "",
            "data" => $plcData
        ];

        return $this->respond($response, 200);
    }
}
