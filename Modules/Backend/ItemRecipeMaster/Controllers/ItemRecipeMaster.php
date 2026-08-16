<?php

namespace Modules\Backend\ItemRecipeMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\ItemRecipeMaster\Models\ItemRecipeMasterModel;
use App\Libraries\SelfRefDataLib;

use CodeIgniter\API\ResponseTrait;

class ItemRecipeMaster extends ApiBaseController
{
    use ResponseTrait;


    protected $ItemRecipeMasterModel;

    public function __construct()
    {
        $this->ItemRecipeMasterModel = new ItemRecipeMasterModel();
    }

    public function save($itemRecipeId = 0)
    {

        $itemRecipeId = (int)getKey($itemRecipeId, "Itemrecipemaster");

        if ($itemRecipeId > 0) {
            if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];



        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['itemRecipeId'] = $itemRecipeId;

        // validation Logic will go here
        $rules['itemCode'] = [
            'label'  => 'Item Code',
            'rules'  => 'max_length[100]'
        ];


        $rules['sideAWidth'] = [
            'label'  => 'Side A Width',
            'rules'  => 'required|decimal|less_than_equal_to[200]'
        ];

        $rules['sideBWidth'] = [
            'label'  => 'Side B Width',
            'rules'  => 'required|decimal|less_than_equal_to[200]'
        ];

        $rules['sideAThickness'] = [
            'label'  => 'Thickness',
            'rules'  => 'required|decimal|greater_than_equal_to[6]|less_than_equal_to[16]'
        ];

        // cutRadius
        // $rules['cutRadius'] = [
        //     'label'  => 'Cutting Punch Radius',
        //     'rules'  => 'required|decimal|greater_than[0]'
        // ];

        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }



        /******************************
         * Multiline data processing and validation Started.
         ******************************/
        // Normalize single row data into an array format
        $itemRecipeSteps = $jsonInput['itemRecipeSteps'] ?? [];
        unset($jsonInput['itemRecipeSteps']);

        foreach ($itemRecipeSteps as &$row) {
            // $row["measurementType"] = "Absolute";
            if (!is_array($row)) {
                $row = [$row];
            }
        }

        $itemRecipeSteps = $this->processChildTableData($itemRecipeSteps, "opType");

        //remove empty products
        $ordId = 1;
        foreach ($itemRecipeSteps as $k => &$row) {
            if ($row['opType'] == "") {
                unset($itemRecipeSteps[$k]);
            }
            $row['tenantId'] = $this->user->tenantId;
            $row['ordId'] = $ordId;
            $ordId++;
        }

        //verify if any marking operation has incremental value.
        foreach ($itemRecipeSteps as $k => $step) {
            if (strtolower($step['measurementType']) === 'incremental' && strtolower($step['opType']) === 'marking') {
                return $this->fail("Marking operation cannot have Incremental measurement type. Please correct it.", 400);
            }
        }

        foreach ($itemRecipeSteps as $k => &$row) {
            if ($row['opType'] == "Marking") {
                $row["yPos"] = 0;
            }
        }

        //copy as $temp
        $temp = array_map(function ($step) {
            return $step;
        }, $itemRecipeSteps);

        // process increments to find absolute positions.
        $temp = $this->processIncrements($temp);

        //find sideA and sideB max positions
        $maxLength = 0;
        foreach ($temp as $step) {
            $maxLength = max($maxLength, floatval($step['xPos']));

            // Validate for Y position limits.
            if ($step['opType'] == 'Punching') {

                // Validate minimum xPos for any side should not be < 25
                // This limit is removed by Prinse on 21-09-2025
                // if ($step['xPos'] < 25) {
                //     return $this->fail("Punching operation on Side " . $step['side'] . " should be at least 25 mm from the start.", 400);
                // }

                if ($step['side'] == 'A') {
                    if ($jsonInput['sideAWidth'] <= 50) {
                        $minY = ($step['opValue'] / 2) + $jsonInput['sideAThickness'];
                    } else {
                        $minY = 20 + $jsonInput['sideAThickness'];
                    }
                    $maxY = $jsonInput['sideAWidth'] + 5;
                } else {
                    if ($jsonInput['sideBWidth'] <= 50) {
                        $minY = ($step['opValue'] / 2) + $jsonInput['sideAThickness'];
                    } else {
                        $minY = 20 + $jsonInput['sideAThickness'];
                    }
                    $maxY = $jsonInput['sideBWidth'] + 5;
                }

                if ($step['yPos'] < $minY || $step['yPos'] > $maxY) {
                    return $this->fail("For Punching operation, Y Position should be between $minY mm and $maxY mm based on the thickness and width defined.", 400);
                }
            }
        }

        if ($jsonInput['programLength'] < $maxLength) {
            return $this->fail("Program Length should be at least " . $maxLength . " mm based on the steps defined.", 400);
        }





        //date/time/datetime conversion logic here.
        if (!empty($jsonInput['updatedAt'])) {
            $jsonInput['updatedAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['updatedAt'])));
        }
        if (!empty($jsonInput['createdAt'])) {
            $jsonInput['createdAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['createdAt'])));
        }


        $successMsg = 'Saved Successfully';
        if ($itemRecipeId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->ItemRecipeMasterModel->update($itemRecipeId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {
            $jsonInput['tenantId'] = $this->user->tenantId;


            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $itemRecipeId = $this->ItemRecipeMasterModel->insert($jsonInput);

            if (!$itemRecipeId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "itemRecipeMaster", "itemRecipeId", $itemRecipeId);
        }

        if (!empty($itemRecipeSteps)) {
            $this->syncChildTable("itemRecipeSteps", "itemRecipeStepId", "itemRecipeId", $itemRecipeId, $itemRecipeSteps);
        }
        $data = $this->ItemRecipeMasterModel->find($itemRecipeId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($itemRecipeId)
    {
        $itemRecipeId = (int)getKey($itemRecipeId, "Itemrecipemaster");

        if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->ItemRecipeMasterModel->where('tenantId', $this->user->tenantId)->find($itemRecipeId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->itemRecipeSteps = $this->getChildTableData("itemRecipeSteps", "itemRecipeId", $itemRecipeId);

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

        $defaultColumns['itemRecipeMaster_itemRecipeId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['itemRecipeMaster_itemCode'] = ['title' => 'Item Code', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        // $defaultColumns['itemRecipeMaster_description'] = ['title' => 'Description', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_sideAWidth'] = ['title' => 'Side A Width', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_sideBWidth'] = ['title' => 'Side B Width', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_sideAThickness'] = ['title' => 'Thickness', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        // $defaultColumns['itemRecipeMaster_sideBThickness'] = ['title' => 'Side B Thickness', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_material'] = ['title' => 'Material', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_programLength'] = ['title' => 'Program Length', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];

        $defaultColumns['itemRecipeMaster_isActive'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['itemRecipeMaster_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];


        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/



        $isActiveFilter = ["1" => "Active", "0" => "In Active"];
        $defaultColumns["itemRecipeMaster_isActive"]["filterType"] = 'select';
        $defaultColumns["itemRecipeMaster_isActive"]["filterOptions"] = $isActiveFilter;


        $defaultColumns['itemRecipeMaster_createdAt']['filterType'] = 'date';
        $defaultColumns['itemRecipeMaster_createdAt']['filterOptions'] = dateFilterOptions('past');


        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'itemRecipeMaster_itemRecipeId';
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
        $select = ["itemRecipeMaster.serialNo as itemRecipeMaster_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "itemRecipeMaster.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'itemRecipeMaster LEFT JOIN userMaster userMaster ON userMaster.userId = itemRecipeMaster.createdBy 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = itemRecipeMaster.tenantId 
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
        // $columnTotalsData['NS_price'] = 12345; //you can run mysql query here to fetch total of the column



        $config = config('AppConfig');
        $mobileView = [];

        foreach ($data as $k => &$row) {

            $primaryKey = $row->itemRecipeMaster_itemRecipeId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                $previewButton = "<a href='javascript:;' 
                    class='btn  btn-primary ms-1 previewProgram' 
                    data-programid='" . setKey($primaryKey, "previewProgram") . "' 
                    title='Program Preview'>
                    <i class='fa fa-image'></i>
                </a>";

                $copyButton = "<a href='javascript:;' 
                    class='btn btn-primary ms-1 apiAction' 
                    data-confirm='Are you sure to copy this program?'
                    data-endpoint='api/ItemRecipeMaster/copyProgram/" . setKey($primaryKey, "Itemrecipemaster") . "'
                    title='Copy Program'>
                    <i class='fa fa-copy'></i>
                </a>";

                $copySwipeButton = "<a href='javascript:;' 
                    class='btn btn-warning ms-1 apiAction' 
                    data-confirm='Are you sure to copy this program AB Swiped?'
                    data-endpoint='api/ItemRecipeMaster/copyProgram/" . setKey($primaryKey, "Itemrecipemaster") . "/1'
                    title='Copy AB Swiped Program'>
                    <i class='fa fa-copy'></i>
                </a>";

                //Details button
                $detailsButton = "<a href='javascript:;' 
                    class='btn btn-info ms-1 apiPopup' 
                    data-size='lg' 
                    data-title='Program  Details' 
                    data-endpoint='" . base_url("api/ItemRecipeMaster/itemRecipeDetails/" . setKey($primaryKey, "Itemrecipemaster")) . "'
                    title='Program Details'>
                    <i class='fa fa-list'></i>
                </a>";

                //edit button
                $editButton = '';
                if (UserPermissionLib::userCanDo("ItemRecipeMaster", 'edit')) {
                    $editButton = "<a href='javascript:;' data-size='xxl' data-title='Edit: " . $row->itemRecipeMaster_itemCode . "' data-stricttype='strict' data-endpoint='" . base_url("ItemRecipeMaster/editItemrecipemaster/" . setKey($primaryKey, "Itemrecipemaster")) . "' 
                      class='btn btn-warning ms-1 apiPopup' 
                      title='Edit Program'>
                      <i class='fa fa-pencil-alt'></i>
                   </a>";
                }

                $row->itemRecipeMaster_itemRecipeId = $row->itemRecipeMaster_serialNo . ' ' . $editButton . ' ' . $detailsButton . ' ' . $previewButton . ' ' . $copyButton . ' ' . $copySwipeButton;

                //display active status

                if ($row->itemRecipeMaster_isActive == 1) {
                    $row->itemRecipeMaster_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/ItemRecipeMaster/changeStatus/" . setKey($primaryKey, "Itemrecipemaster") . "'>Active</span>";
                } else {
                    $row->itemRecipeMaster_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/ItemRecipeMaster/changeStatus/" . setKey($primaryKey, "Itemrecipemaster") . "'>In Active</span>";
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
                if (($row->itemRecipeMaster_isActive == '1')) {
                    $row->itemRecipeMaster_isActive = "Active";
                } else {
                    $row->itemRecipeMaster_isActive = "In Active";
                }
            }

            /*******************************************************
                EDIT 5: general data for screen,printing,export will go here.
             *******************************************************/

            $row->itemRecipeMaster_createdAt = myDateTimeFormat($row->itemRecipeMaster_createdAt);


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

    public function changeStatus($itemRecipeId = 0)
    {
        $itemRecipeId = (int)getKey($itemRecipeId, "Itemrecipemaster");

        if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $itemDetails = $this->ItemRecipeMasterModel->where('tenantId', $this->user->tenantId)->find($itemRecipeId);

        if (!$itemDetails) {
            return $this->failNotFound('Item not found');
        }

        $status = $itemDetails->isActive == 1 ? 0 : 1;

        $this->db->query("UPDATE itemRecipeMaster SET isActive = ? WHERE itemRecipeId = ? AND tenantId = " . $this->user->tenantId . "", [$status, $itemRecipeId]);

        $response = [
            'status' => true,
            "message" => " Status updated successfully",
        ];

        return $this->respond($response, 200);
    }


    public function itemRecipeDetails($itemRecipeId = 0)
    {
        $itemRecipeId = (int)getKey($itemRecipeId, "Itemrecipemaster");

        $itemRecipeDetails = $this->db->query("SELECT IR.*, IRS.* FROM itemRecipeMaster IR 
            LEFT JOIN itemRecipeSteps IRS ON IR.itemRecipeId = IRS.itemRecipeId 
            WHERE IR.itemRecipeId = $itemRecipeId AND IR.tenantId = " . $this->user->tenantId . "
            ")->getResult();


        $data["details"] = $itemRecipeDetails;
        $data["itemRecipeId"] = $itemRecipeId;

        $view = '\Modules\Backend\ItemRecipeMaster\Views\itemRecipeDetails';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function getItemRecipeList()
    {
        $input = $this->getInputData();
        $jsonInput = $input["jsonInput"];
        $search = $jsonInput["data"]["q"];
        $pageNo = $jsonInput["data"]["page"] ?? 1;
        $length = 50;
        $offset = ($pageNo - 1) * $length;

        $lib = new SelfRefDataLib(
            "itemRecipeMaster",
            "itemRecipeMaster.itemCode",
            null,
            "tenantId = " . $this->user->tenantId . " AND isActive = 1 ",
        );

        $searchResult = $lib->search($search, $length, $offset);
        $totalCount = $lib->searchCount($search);

        $response = [
            'status' => true,
            "message" => "",
            "data" => [
                "items" => $searchResult,
                "totalCount" => $totalCount
            ]
        ];

        return $this->respond($response, 200);
    }

    public function getProgramDetails($programId = 0)
    {
        $programId = (int)getKey($programId, "previewProgram");

        $details = $this->ItemRecipeMasterModel->where('tenantId', $this->user->tenantId)->find($programId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->itemRecipeSteps = $this->getChildTableData("itemRecipeSteps", "itemRecipeId", $programId);

        $response = [
            'status' => true,
            "message" => "",
            "data" => $details,
        ];

        return $this->respond($response, 200);
    }

    public function processIncrements($programSteps)
    {
        // return $programSteps;

        $lastPosition = [];
        $lastPosition["A"] = 0;
        $lastPosition["B"] = 0;

        foreach ($programSteps as $k => $step) {
            if (strtolower($step['measurementType']) === 'incremental') {
                if ($step['side'] === 'A') {
                    $lastPosition["A"] += floatval($step['xPos']);
                    $step['xPos'] = $lastPosition["A"];
                } elseif ($step['side'] === 'B') {
                    $lastPosition["B"] += floatval($step['xPos']);
                    $step['xPos'] = $lastPosition["B"];
                }
            } else {
                // For non-increment operations, update lastPosition to current xPos
                if ($step['side'] === 'A') {
                    $lastPosition["A"] = floatval($step['xPos']);
                } elseif ($step['side'] === 'B') {
                    $lastPosition["B"] = floatval($step['xPos']);
                }
            }

            $programSteps[$k] = $step;
        }
        return $programSteps;
    }

    public function getChildTableData($tableName, $parentColumn, $parentId)
    {
        // Fetch all rows where parentColumn = parentId
        $query = $this->db->table($tableName)->where($parentColumn, $parentId)->orderBy('ordId', 'asc')->get();

        // Return as an associative array
        return $query->getResultArray();
    }

    public function copyProgram($itemId, $swapSides = false)
    {
        $itemId = (int)getKey($itemId, "Itemrecipemaster");

        if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'add')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $itemDetails = $this->ItemRecipeMasterModel->where('tenantId', $this->user->tenantId)->find($itemId);

        if (!$itemDetails) {
            return $this->failNotFound('Item not found');
        }

        $itemSteps = $this->getChildTableData("itemRecipeSteps", "itemRecipeId", $itemId);

        unset($itemDetails->itemRecipeId);
        unset($itemDetails->createdAt);
        unset($itemDetails->createdBy);
        unset($itemDetails->updatedAt);
        unset($itemDetails->updatedBy);
        unset($itemDetails->serialNo);

        $itemDetails->itemCode = $itemDetails->itemCode . " - Copy";
        $itemDetails->isActive = 0;
        $itemDetails->createdAt = timenow();
        $itemDetails->createdBy = $this->user->userId;

        $newItemId = $this->ItemRecipeMasterModel->insert((array)$itemDetails);

        if (!$newItemId) {
            return $this->fail('Failed to Save', 500);
        }

        assignSerialNumber($this->user->tenantId, "itemRecipeMaster", "itemRecipeId", $newItemId);

        if (!empty($itemSteps)) {
            foreach ($itemSteps as &$step) {

                if ($swapSides) {
                    if ($step['side'] == 'A') {
                        $step['side'] = 'B';
                    } elseif ($step['side'] == 'B') {
                        $step['side'] = 'A';
                    }
                }


                unset($step['itemRecipeStepId']);
                $step['itemRecipeId'] = $newItemId;
            }
            $this->syncChildTable("itemRecipeSteps", "itemRecipeStepId", "itemRecipeId", $newItemId, $itemSteps);
        }

        $data = $this->ItemRecipeMasterModel->find($newItemId);

        return $this->respondCreated(['status' => true, 'message' => 'Program copied successfully', 'data' => $data]);
    }
}
