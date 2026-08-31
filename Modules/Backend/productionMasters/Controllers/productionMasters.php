<?php

namespace Modules\Backend\productionMasters\Controllers;

use App\Controllers\ApiBaseController;
use CodeIgniter\API\ResponseTrait;

class productionMasters extends ApiBaseController
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

        $defaultColumns['productionMaster_serialNo'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['programAlignMaster_serialNo'] = ['title' => 'Program', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_serialNo'] = ['title' => 'Job', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_itemCode'] = ['title' => 'Item Recipe', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionMaster_quantityProduced'] = ['title' => 'Quantity Produced', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionMaster_startedAt'] = ['title' => 'Started At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionMaster_completedAt'] = ['title' => 'Completed At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['userMaster_username'] = ['title' => 'Username', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];


        

        /*****************************************
                EDIT 2: Define required columns filter and type with options here
        *****************************************/

        $temp = $this->db->query("SELECT GROUP_CONCAT(CONCAT(programId, ':', serialNo) SEPARATOR '__') as `filterData` FROM programAlignMaster WHERE tenantId = '" . $this->user->tenantId . "'")->getRow()->filterData;
        $programFilters = explode('__', $temp);
        $defaultColumns['programAlignMaster_serialNo']['filterType'] = 'custom';
        $defaultColumns['programAlignMaster_serialNo']['filterOptions'] = $programFilters;


        $temp = $this->db->query("SELECT GROUP_CONCAT(CONCAT(jobId, ':', serialNo) SEPARATOR '__') as `filterData` FROM productionJobCards WHERE tenantId = '" . $this->user->tenantId . "'")->getRow()->filterData;
        $jobFilters = explode('__', $temp);
        $defaultColumns['productionJobCards_serialNo']['filterType'] = 'custom';
        $defaultColumns['productionJobCards_serialNo']['filterOptions'] = $jobFilters;


$defaultColumns['productionMaster_startedAt']['filterType'] = 'date';
$defaultColumns['productionMaster_startedAt']['filterOptions'] = dateFilterOptions('past');


$defaultColumns['productionMaster_completedAt']['filterType'] = 'date';
$defaultColumns['productionMaster_completedAt']['filterOptions'] = dateFilterOptions('past');


$temp = $this->db->query("SELECT GROUP_CONCAT(username SEPARATOR '__') as `filterData` FROM userMaster")->getRow()->filterData;
$foreignFilters = explode('__', $temp);
$defaultColumns['userMaster_username']['filterType'] = 'select';
$defaultColumns['userMaster_username']['filterOptions'] = $foreignFilters;


        

        /*****************************************
                EDIT 3: Define default columns here  
        *****************************************/
        $configData['defaultOrderColumn'] = 'productionMaster_serialNo';
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
        $select = ["productionMaster.serialNo as productionMaster_serialNo", "productionMaster.productionId as productionMaster_productionId", "programAlignMaster.serialNo as programAlignMaster_serialNo", "productionJobCards.serialNo as productionJobCards_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
$where[] = "productionMaster.tenantId = '".$this->user->tenantId."'";
 // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'productionMaster LEFT JOIN productionJobCards productionJobCards ON productionJobCards.jobId = productionMaster.jobId 
LEFT JOIN programAlignMaster programAlignMaster ON programAlignMaster.programId = productionMaster.programId 
LEFT JOIN userMaster userMaster ON userMaster.userId = productionMaster.userId
LEFT JOIN itemRecipeMaster itemRecipeMaster ON itemRecipeMaster.itemRecipeId = productionJobCards.itemRecipeId ';


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
                    
                    if ($columnName == 'programAlignMaster_serialNo') {
                        $serialNo = explode(":", $filters[$columnName])[1] ?? $filters[$columnName];
                        $where[] = "programAlignMaster.serialNo = :" . $columnName . "_filter:";
                        $queryParameters[$columnName . '_filter'] = $serialNo;
                    } else if ($columnName == 'productionJobCards_serialNo') {
                        $serialNo = explode(":", $filters[$columnName])[1] ?? $filters[$columnName];
                        $where[] = "productionJobCards.serialNo = :" . $columnName . "_filter:";
                        $queryParameters[$columnName . '_filter'] = $serialNo;
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
        //$columnTotalsData['NS_price'] = 12345; //you can run mysql query here to fetch total of the column
        

        $config = config('AppConfig');
        $mobileView = [];

        foreach ($data as $k=>&$row) {

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                ********************************************/

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

            $row->productionMaster_startedAt = myDateTimeFormat($row->productionMaster_startedAt);
$row->productionMaster_completedAt = myDateTimeFormat($row->productionMaster_completedAt);
  

            
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