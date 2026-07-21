<?php

namespace Modules\Backend\AalarmModules\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\AalarmModules\Models\AlarmLogModel;


use CodeIgniter\API\ResponseTrait;

class AlarmLog extends ApiBaseController
{
    use ResponseTrait;


    protected $AlarmLogModel;

    public function __construct()
    {
        $this->AlarmLogModel = new AlarmLogModel();
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

        $defaultColumns['AlarmLog_logId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        // $defaultColumns['AlarmConfig_alarmId'] = ['title' => 'Alarm Id', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['uiTagMaster_tagName'] = ['title' => 'Tag Name', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmLog_alarmType'] = ['title' => 'Alarm Type', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmLog_triggerValue'] = ['title' => 'Trigger Value', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmLog_triggerTime'] = ['title' => 'Trigger Time', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmLog_resolveTime'] = ['title' => 'Resolve Time', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['AlarmLog_isResolved'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];



        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        // $temp = $this->db->query("SELECT GROUP_CONCAT(alarmId SEPARATOR '__') as `filterData` FROM AlarmConfig")->getRow()->filterData;
        // $foreignFilters = explode('__', $temp);
        // $defaultColumns['AlarmConfig_alarmId']['filterType'] = 'select';
        // $defaultColumns['AlarmConfig_alarmId']['filterOptions'] = $foreignFilters;


        $temp = $this->db->query("SELECT GROUP_CONCAT(tagName SEPARATOR '__') as `filterData` FROM uiTagMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['uiTagMaster_tagName']['filterType'] = 'select';
        $defaultColumns['uiTagMaster_tagName']['filterOptions'] = $foreignFilters;


        $filterData = ['lo' => 'Lo', 'lolo' => 'Lolo', 'hi' => 'Hi', 'hihi' => 'Hihi'];
        $defaultColumns['AlarmLog_alarmType']['filterType'] = 'select';
        $defaultColumns['AlarmLog_alarmType']['filterOptions'] = $filterData;


        $defaultColumns['AlarmLog_triggerTime']['filterType'] = 'date';
        $defaultColumns['AlarmLog_triggerTime']['filterOptions'] = dateFilterOptions('past');


        $defaultColumns['AlarmLog_resolveTime']['filterType'] = 'date';
        $defaultColumns['AlarmLog_resolveTime']['filterOptions'] = dateFilterOptions('past');


        $filterData = ['1' => 'Resolved', '0' => 'Unresolved'];
        $defaultColumns['AlarmLog_isResolved']['filterType'] = 'select';
        $defaultColumns['AlarmLog_isResolved']['filterOptions'] = $filterData;


        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'AlarmLog_logId';
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
        $select = ["AlarmLog.serialNo as AlarmLog_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "AlarmLog.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'AlarmLog LEFT JOIN AlarmConfig AlarmConfig ON AlarmConfig.alarmId = AlarmLog.alarmId 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = AlarmLog.tenantId 
LEFT JOIN uiTagMaster uiTagMaster ON uiTagMaster.uiTagId = AlarmLog.uiTagId 
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

            $primaryKey = $row->AlarmLog_logId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                $dropdownItems = [];
                //edit button
                if (UserPermissionLib::userCanDo("AlarmLog", 'edit')) {
                    $dropdownItems[] = ['label' => 'Edit', 'class' => 'apiPopup text-warning', 'icon' => 'fa fa-edit', 'attributes' => "data-size='xl' data-title='Alarmlog Form' data-endpoint='" . ("AlarmLog/editAlarmlog/" . setKey($primaryKey, "Alarmlog")) . "'"];
                }
                //delete button
                if (UserPermissionLib::userCanDo("AlarmLog", 'delete')) {
                    $dropdownItems[] = ['label' => 'Delete', 'href' => 'javascript:;', 'class' => 'text-danger apiAction', 'icon' => 'fa fa-trash', 'attributes' => "data-confirm='Are you sure to delete this Alarmlog?' data-endpoint='" . ("api/AlarmLog/delete/" . setKey($primaryKey, "Alarmlog")) . "'"];
                }

                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->AlarmLog_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->AlarmLog_logId = $dropdownHtml;

                //display active status

                if (($row->AlarmLog_isResolved == '1')) {
                    $row->AlarmLog_isResolved = "Resolved";
                } else {
                    $row->AlarmLog_isResolved = "Unresolved";
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
                if (($row->AlarmLog_isResolved == '1')) {
                    $row->AlarmLog_isResolved = "Resolved";
                } else {
                    $row->AlarmLog_isResolved = "Unresolved";
                }
            }


            /*******************************************************
                EDIT 5: general data for screen,printing,export will go here.
             *******************************************************/

            $row->AlarmLog_triggerTime = myDateTimeFormat($row->AlarmLog_triggerTime);
            $row->AlarmLog_resolveTime = myDateTimeFormat($row->AlarmLog_resolveTime);


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

}
