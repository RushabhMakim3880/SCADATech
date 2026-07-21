<?php

namespace Modules\Backend\AalarmModules\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\AalarmModules\Models\AalarmModulesModel;


use CodeIgniter\API\ResponseTrait;

class AalarmModules extends ApiBaseController
{
    use ResponseTrait;


    protected $AalarmModulesModel;

    public function __construct()
    {
        $this->AalarmModulesModel = new AalarmModulesModel();
    }

    public function save($alarmId = 0)
    {

        $alarmId = (int)getKey($alarmId, "Alarmconfig");

        if ($alarmId > 0) {
            if (!UserPermissionLib::userCanDo("AalarmModules", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("AalarmModules", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['alarmId'] = $alarmId;

        // validation Logic will go here
        $rules['uiTagId'] = [
            'label'  => 'Ui Tag Id',
            'rules'  => 'required|integer|is_natural'
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

        if ($jsonInput['message'] and trim($jsonInput['message']) != "")
            $jsonInput['message'] = (string)$jsonInput['message'];
        else
            $jsonInput['message'] = null;


        $successMsg = 'Saved Successfully';
        if ($alarmId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->AalarmModulesModel->update($alarmId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {

            $jsonInput['tenantId'] = $this->user->tenantId;


            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $alarmId = $this->AalarmModulesModel->insert($jsonInput);

            if (!$alarmId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "AlarmConfig", "alarmId", $alarmId);
        }

        $data = $this->AalarmModulesModel->find($alarmId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($alarmId)
    {
        $alarmId = (int)getKey($alarmId, "Alarmconfig");

        if (!UserPermissionLib::userCanDo("AalarmModules", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->AalarmModulesModel->find($alarmId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->uid = setKey($details->alarmId, "Alarmconfig");

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

        $defaultColumns['AlarmConfig_alarmId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['uiTagMaster_tagName'] = ['title' => 'Scada Tag', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_loloTheresold'] = ['title' => 'Lolo Theresold', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_loTheresold'] = ['title' => 'Lo Theresold', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_hiTheresold'] = ['title' => 'Hi Theresold', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_hihiTheresold'] = ['title' => 'Hihi Theresold', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_message'] = ['title' => 'Message', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_isActive'] = ['title' => 'Is Active', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmConfig_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];




        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $temp = $this->db->query("SELECT GROUP_CONCAT(tagName SEPARATOR '__') as `filterData` FROM uiTagMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['uiTagMaster_tagName']['filterType'] = 'select';
        $defaultColumns['uiTagMaster_tagName']['filterOptions'] = $foreignFilters;


        $filterData = ['1' => 'Active', '0' => 'Inactive'];
        $defaultColumns['AlarmConfig_isActive']['filterType'] = 'select';
        $defaultColumns['AlarmConfig_isActive']['filterOptions'] = $filterData;



        $defaultColumns['AlarmConfig_createdAt']['filterType'] = 'date';
        $defaultColumns['AlarmConfig_createdAt']['filterOptions'] = dateFilterOptions('past');




        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'AlarmConfig_alarmId';
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
        $select = ["AlarmConfig.serialNo as AlarmConfig_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "AlarmConfig.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'AlarmConfig 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = AlarmConfig.tenantId 
LEFT JOIN uiTagMaster uiTagMaster ON uiTagMaster.uiTagId = AlarmConfig.uiTagId 
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

            $primaryKey = $row->AlarmConfig_alarmId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                // //edit button
                $editButton = '';
                if (UserPermissionLib::userCanDo("AalarmModules", 'edit')) {
                    $editButton = "<a href='#'class='btn btn-xs btn-warning ms-2 apiPopup'
                      data-size='xl'data-title='Alarm Config Form'
                      data-endpoint='" . base_url("AalarmModules/editAlarmconfig/" . setKey($primaryKey, "Alarmconfig")) . "'
                      title='Edit Alarm Config'>
                      <i class='fa fa-pencil-alt'></i>
                   </a>";
                }

                $row->AlarmConfig_alarmId = $row->AlarmConfig_serialNo . ' ' . $editButton;



                /***********************************************************************/
                //isActive: Toggle Code Starts 
                //If not required, comment this code, uncomment last line of this code part, remove "toggleIsActive" function code, remove route from route file for proper cleanup.
                /***********************************************************************/
                $enumList = ['1' => 'Active', '0' => 'Inactive'];

                $className = 'bg-success';
                if ($row->AlarmConfig_isActive == 0) {
                    $className = 'bg-danger';
                }
                $row->AlarmConfig_isActive = "<span title='Click to toggle " . printable('isActive') . "' class='badge $className bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/AalarmModules/toggleIsActive/" . setKey($primaryKey, "Alarmconfig") . "'>" . $enumList[$row->AlarmConfig_isActive] . "</span>";

                /***********************************************************************/
                //isActive: Toggle Code Ends 
                //if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box
                /***********************************************************************/
                //$enumList = ['1' => 'Active', '0' => 'Inactive'];
                //$row->AlarmConfig_isActive = $enumList[$row->AlarmConfig_isActive];


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

            $row->AlarmConfig_createdAt = myDateTimeFormat($row->AlarmConfig_createdAt);


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

    public function delete($alarmId = 0)
    {
        $alarmId = (int)getKey($alarmId, "Alarmconfig");

        if (!UserPermissionLib::userCanDo("AalarmModules", 'delete')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($alarmId == 0) {
            return $this->fail('Invalid request', 400);
        }

        //set isDeleted = 1
        $this->AalarmModulesModel->update($alarmId, ['isDeleted' => 1]);

        // or put delete logic here if not using isDeleted field
        // $this->AalarmModulesModel->delete($alarmId);

        return $this->respondDeleted(['message' => 'Deleted successfully']);
    }


    public function toggleIsActive($alarmId)
    {
        $alarmId = (int)getKey($alarmId, "Alarmconfig");

        if (!UserPermissionLib::userCanDo("AalarmModules", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($alarmId == 0) {
            return $this->fail('Invalid request', 400);
        }

        $this->db->table('AlarmConfig')->set('isActive', 'NOT isActive', false)->where('alarmId', $alarmId)->update();

        $response = [
            'status' => true,
            "message" => "Done.",
        ];

        return $this->respond($response, 200);
    }





    // {{CUSTOM_FUNCTIONS_FOR_VIEW_DETAILS}}
}
