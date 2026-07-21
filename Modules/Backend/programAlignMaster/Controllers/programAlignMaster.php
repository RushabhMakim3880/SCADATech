<?php

namespace Modules\Backend\programAlignMaster\Controllers;

use App\Controllers\ApiBaseController;
use CodeIgniter\API\ResponseTrait;

class programAlignMaster extends ApiBaseController
{
    use ResponseTrait;

    public function getDataTableColumns($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        /*****************************************
                EDIT 1: Define default columns here  
        *****************************************/
        $defaultColumns = [];

        $defaultColumns['programAlignMaster_programId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
$defaultColumns['programAlignMaster_completedCycles'] = ['title' => 'Completed Cycles', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_totalItems'] = ['title' => 'Total Items', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_totalOperations'] = ['title' => 'Total Operations', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_DA1'] = ['title' => 'DA1', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_DA2'] = ['title' => 'DA2', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_DB1'] = ['title' => 'DB1', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_DB2'] = ['title' => 'DB2', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_Marking1'] = ['title' => 'Marking1', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_Marking2'] = ['title' => 'Marking2', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_Marking3'] = ['title' => 'Marking3', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_Marking4'] = ['title' => 'Marking4', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_cuttings'] = ['title' => 'Cuttings', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_updatedBy'] = ['title' => 'Updated By', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_createdBy'] = ['title' => 'Created By', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_updatedAt'] = ['title' => 'Updated At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
$defaultColumns['programAlignMaster_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];


        

        /*****************************************
                EDIT 2: Define required columns filter and type with options here
        *****************************************/

$defaultColumns['programAlignMaster_updatedAt']['filterType'] = 'date';
$defaultColumns['programAlignMaster_updatedAt']['filterOptions'] = dateFilterOptions('past');


$defaultColumns['programAlignMaster_createdAt']['filterType'] = 'date';
$defaultColumns['programAlignMaster_createdAt']['filterOptions'] = dateFilterOptions('past');

$temp = $this->db->query("SELECT GROUP_CONCAT(CONCAT(userId, ':', firstName, ' ', lastName) SEPARATOR '__') as `filterData` FROM userMaster WHERE tenantId = '" . $this->user->tenantId . "'")->getRow()->filterData;
$userFilters = explode('__', $temp);
$defaultColumns['programAlignMaster_updatedBy']['filterType'] = 'custom';
$defaultColumns['programAlignMaster_updatedBy']['filterOptions'] = $userFilters;

$defaultColumns['programAlignMaster_createdBy']['filterType'] = 'custom';
$defaultColumns['programAlignMaster_createdBy']['filterOptions'] = $userFilters;



        

        /*****************************************
                EDIT 3: Define default columns here  
        *****************************************/
        $configData['defaultOrderColumn'] = 'programAlignMaster_programId';
        $configData['defaultOrderDirection'] = 'desc';

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
        $select = ["programAlignMaster.serialNo as programAlignMaster_serialNo", "programAlignMaster.updatedBy as programAlignMaster_updatedBy", "programAlignMaster.createdBy as programAlignMaster_createdBy"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
$where[] = "programAlignMaster.tenantId = '".$this->user->tenantId."'";
 // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'programAlignMaster LEFT JOIN userMaster createdByUser ON createdByUser.userId = programAlignMaster.createdBy 
LEFT JOIN userMaster updatedByUser ON updatedByUser.userId = programAlignMaster.updatedBy 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = programAlignMaster.tenantId 
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
                    if ($columnName == 'programAlignMaster_updatedBy' or $columnName == 'programAlignMaster_createdBy') {
                        $userId = explode(":", $filters[$columnName])[0] ?? $filters[$columnName];
                        $where[] = "$dbField = :" . $columnName . "_filter:";
                        $queryParameters[$columnName . '_filter'] = $userId;
                    }
                    
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

        $mobileView = [];

        foreach ($data as $k=>&$row) {

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                ********************************************/

                $row->programAlignMaster_programId = $row->programAlignMaster_serialNo;

                

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

            $row->programAlignMaster_updatedAt = myDateTimeFormat($row->programAlignMaster_updatedAt);
            $row->programAlignMaster_createdAt = myDateTimeFormat($row->programAlignMaster_createdAt);
            
            $row->programAlignMaster_updatedBy = username($row->programAlignMaster_updatedBy);
            $row->programAlignMaster_createdBy = username($row->programAlignMaster_createdBy);
 
            

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

    // {{CUSTOM_FUNCTIONS_FOR_VIEW_DETAILS}}

    private function getKpiFilters(): array
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return [];
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'] ?? [];

        return isset($jsonInput['filters']) && is_array($jsonInput['filters']) ? $jsonInput['filters'] : [];
    }

    private function buildKpiWhere(array $filters): array
    {
        $where = ["programAlignMaster.tenantId = :tenantId:"];
        $queryParameters = [
            "tenantId" => (int)$this->user->tenantId,
        ];

        $filterMap = [
            "programAlignMaster_updatedBy" => ["field" => "programAlignMaster.updatedBy", "type" => "user"],
            "programAlignMaster_createdBy" => ["field" => "programAlignMaster.createdBy", "type" => "user"],
            "programAlignMaster_updatedAt" => ["field" => "programAlignMaster.updatedAt", "type" => "date"],
            "programAlignMaster_createdAt" => ["field" => "programAlignMaster.createdAt", "type" => "date"],
        ];

        foreach ($filters as $columnName => $filterValue) {
            if (!isset($filterMap[$columnName])) {
                continue;
            }

            if (is_array($filterValue)) {
                $filterValue = reset($filterValue);
            }

            $filterValue = trim((string)$filterValue);
            if ($filterValue === '' || strtolower($filterValue) === 'all') {
                continue;
            }

            $filterConfig = $filterMap[$columnName];

            if ($filterConfig["type"] === "user") {
                $userId = trim(explode(":", $filterValue)[0] ?? $filterValue);
                if (!ctype_digit((string)$userId)) {
                    $where[] = "1 = 0";
                    continue;
                }

                $parameterName = $columnName . "_filter";
                $where[] = $filterConfig["field"] . " = :" . $parameterName . ":";
                $queryParameters[$parameterName] = (int)$userId;
                continue;
            }

            if ($filterConfig["type"] === "date") {
                $dateRange = dateFilterOptionRange($filterValue);
                if (!is_array($dateRange) || count($dateRange) !== 2) {
                    $where[] = "1 = 0";
                    continue;
                }

                $startParameterName = $columnName . "_startDate";
                $endParameterName = $columnName . "_endDate";
                $where[] = "DATE(" . $filterConfig["field"] . ") BETWEEN :" . $startParameterName . ": AND :" . $endParameterName . ":";
                $queryParameters[$startParameterName] = $dateRange[0];
                $queryParameters[$endParameterName] = $dateRange[1];
            }
        }

        return [$where, $queryParameters];
    }

    public function getKpiData()
    {
        [$where, $queryParameters] = $this->buildKpiWhere($this->getKpiFilters());
        $whereClause = !empty($where) ? " WHERE " . implode(' AND ', $where) : "";

        $row = $this->db->query(
            "SELECT
                COALESCE(SUM(COALESCE(completedCycles, 0)), 0) as totalCycleTime,
                COALESCE(SUM(COALESCE(totalItems, 0)), 0) as totalItems,
                COALESCE(SUM(COALESCE(DA1, 0) + COALESCE(DA2, 0) + COALESCE(DB1, 0) + COALESCE(DB2, 0)), 0) as totalPunches,
                COALESCE(SUM(COALESCE(Marking1, 0) + COALESCE(Marking2, 0) + COALESCE(Marking3, 0) + COALESCE(Marking4, 0)), 0) as totalMarking
            FROM programAlignMaster $whereClause",
            $queryParameters
        )->getRow();

        $data = [
            "totalCycleTime" => (int)($row->totalCycleTime ?? 0),
            "totalItems" => (int)($row->totalItems ?? 0),
            "totalPunches" => (int)($row->totalPunches ?? 0),
            "totalMarking" => (int)($row->totalMarking ?? 0),
        ];

        return $this->respond([
            "status" => true,
            "message" => "KPI data fetched successfully",
            "data" => $data,
        ], 200);
    }
}
