<?php

namespace Modules\Backend\jobCards\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\jobCards\Models\jobCardsModel;
use App\Libraries\SelfRefDataLib;

use CodeIgniter\API\ResponseTrait;

class jobCards extends ApiBaseController
{
    use ResponseTrait;


    protected $jobCardsModel;

    public function __construct()
    {
        $this->jobCardsModel = new jobCardsModel();
    }

    public function save($jobId = 0)
    {

        $jobId = (int)getKey($jobId, "Jobcard");

        if ($jobId > 0) {
            if (!UserPermissionLib::userCanDo("jobCards", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("jobCards", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['jobId'] = $jobId;

        // validation Logic will go here
        $rules['itemRecipeId'] = [
            'label'  => 'Item Recipe Id',
            'rules'  => 'required|integer|is_natural'
        ];
        $rules['requiredQuantity'] = [
            'label'  => 'Required Quantity',
            'rules'  => 'required|integer|is_natural'
        ];


        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }

        $successMsg = 'Saved Successfully';
        if ($jobId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->jobCardsModel->update($jobId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {
            // Check if an uncompleted jobcard already exists for this itemRecipeId
            $existingJob = $this->db->table('productionJobCards')
                ->where('tenantId', $this->user->tenantId)
                ->where('itemRecipeId', $jsonInput['itemRecipeId'])
                ->whereIn('status', ['waiting', 'started', 'partiallyCompleted'])
                ->get()
                ->getRow();

            if ($existingJob) {
                // Increment requiredQuantity on existing uncompleted jobcard
                $newRequiredQty = (int)$existingJob->requiredQuantity + (int)$jsonInput['requiredQuantity'];
                $updateData = [
                    'requiredQuantity' => $newRequiredQty,
                    'updatedAt'        => timenow(),
                    'updatedBy'        => $this->user->userId
                ];

                $this->db->table('productionJobCards')
                    ->where('jobId', $existingJob->jobId)
                    ->update($updateData);

                $jobId = $existingJob->jobId;
                $successMsg = 'Required quantity added to existing pending Jobcard';
            } else {
                $jsonInput['tenantId'] = $this->user->tenantId;
                $jsonInput['createdAt'] = timenow();
                $jsonInput['createdBy'] = $this->user->userId;
                $jsonInput['status']    = 'waiting';
                $jobId = $this->jobCardsModel->insert($jsonInput);

                if (!$jobId) {
                    return $this->fail('Failed to Save', 500);
                }

                assignSerialNumber($this->user->tenantId, "productionJobCards", "jobId", $jobId);
            }
        }

        $data = $this->jobCardsModel->find($jobId);

        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'data' => $data]);
    }

    public function get($jobId)
    {
        $jobId = (int)getKey($jobId, "Jobcard");

        if (!UserPermissionLib::userCanDo("jobCards", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->jobCardsModel->find($jobId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->uid = setKey($details->jobId, "Jobcard");


        $lib = new SelfRefDataLib(
            "itemRecipeMaster",
            "itemRecipeMaster.itemCode",
        );

        $details->itemRecipeId = $lib->getSelect2Data($details->itemRecipeId);

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

        $defaultColumns['productionJobCards_jobId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['itemRecipeMaster_itemCode'] = ['title' => 'Item Code', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['productionJobCards_requiredQuantity'] = ['title' => 'Required Quantity', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_completedQuantity'] = ['title' => 'Completed Quantity', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_status'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_startedAt'] = ['title' => 'Started At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_completedAt'] = ['title' => 'Completed At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_updatedAt'] = ['title' => 'Updated At', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['userMaster_username'] = ['title' => 'Username', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['productionJobCards_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['userMaster_username'] = ['title' => 'Username', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];




        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        // $temp = $this->db->query("SELECT GROUP_CONCAT(itemCode SEPARATOR '__') as `filterData` FROM itemRecipeMaster")->getRow()->filterData;
        // $foreignFilters = explode('__', $temp);
        // $defaultColumns['itemRecipeMaster_itemCode']['filterType'] = 'select';
        // $defaultColumns['itemRecipeMaster_itemCode']['filterOptions'] = $foreignFilters;


        $filterData = ['waiting' => 'Waiting', 'started' => 'Started', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'partiallyCompleted' => 'Partially Completed'];
        $defaultColumns['productionJobCards_status']['filterType'] = 'select';
        $defaultColumns['productionJobCards_status']['filterOptions'] = $filterData;


        $defaultColumns['productionJobCards_startedAt']['filterType'] = 'date';
        $defaultColumns['productionJobCards_startedAt']['filterOptions'] = dateFilterOptions('past');


        $defaultColumns['productionJobCards_completedAt']['filterType'] = 'date';
        $defaultColumns['productionJobCards_completedAt']['filterOptions'] = dateFilterOptions('past');


        $defaultColumns['productionJobCards_updatedAt']['filterType'] = 'date';
        $defaultColumns['productionJobCards_updatedAt']['filterOptions'] = dateFilterOptions('past');


        $temp = $this->db->query("SELECT GROUP_CONCAT(username SEPARATOR '__') as `filterData` FROM userMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['userMaster_username']['filterType'] = 'select';
        $defaultColumns['userMaster_username']['filterOptions'] = $foreignFilters;


        $defaultColumns['productionJobCards_createdAt']['filterType'] = 'date';
        $defaultColumns['productionJobCards_createdAt']['filterOptions'] = dateFilterOptions('past');


        $temp = $this->db->query("SELECT GROUP_CONCAT(username SEPARATOR '__') as `filterData` FROM userMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['userMaster_username']['filterType'] = 'select';
        $defaultColumns['userMaster_username']['filterOptions'] = $foreignFilters;






        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'productionJobCards_jobId';
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
        $select = ["productionJobCards.serialNo as productionJobCards_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "productionJobCards.tenantId = '" . $this->user->tenantId . "'";
        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'productionJobCards LEFT JOIN userMaster userMaster ON userMaster.userId = productionJobCards.createdBy 
LEFT JOIN itemRecipeMaster itemRecipeMaster ON itemRecipeMaster.itemRecipeId = productionJobCards.itemRecipeId 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = productionJobCards.tenantId 
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

            $primaryKey = $row->productionJobCards_jobId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                //cancel button
                $cancelButton = "<a href='javascript:;' class='btn btn-xs btn-danger ms-2 apiAction' data-confirm='Are you sure you want to cancel this Jobcard?' 
                data-endpoint='" . base_url("api/jobCards/cancellStatus/" . setKey($primaryKey, "Jobcard")) . "'title='Cancel Jobcard'>
                <i class='fa fa-times-circle'></i> </a>";

                // //edit button
                $editButton = '';
                if (UserPermissionLib::userCanDo("jobCards", 'edit')) {
                    $editButton = "<a href='#'class='btn btn-xs btn-warning ms-2 apiPopup'
                      data-size='xl'data-title='Jobcard Form'
                      data-endpoint='" . base_url("jobCards/editJobcard/" . setKey($primaryKey, "Jobcard")) . "'
                      title='Edit Jobcard Form'>
                      <i class='fa fa-pencil-alt'></i>
                   </a>";
                }

                $row->productionJobCards_jobId = $row->productionJobCards_serialNo . ' ' . $editButton . ' ' . $cancelButton;


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

            $row->productionJobCards_status = printable($row->productionJobCards_status);
            $row->productionJobCards_startedAt = myDateTimeFormat($row->productionJobCards_startedAt);
            $row->productionJobCards_completedAt = myDateTimeFormat($row->productionJobCards_completedAt);
            $row->productionJobCards_updatedAt = myDateTimeFormat($row->productionJobCards_updatedAt);
            $row->productionJobCards_createdAt = myDateTimeFormat($row->productionJobCards_createdAt);


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

    // public function delete($jobId = 0)
    // {
    //     $jobId = (int)getKey($jobId, "Jobcard");

    //     if (!UserPermissionLib::userCanDo("jobCards", 'delete')) {
    //         return $this->failForbidden('Insufficient permissions');
    //     }

    //     if ($jobId == 0) {
    //         return $this->fail('Invalid request', 400);
    //     }

    //     //set isDeleted = 1
    //     $this->jobCardsModel->update($jobId, ['status' => 1]);

    //     // or put delete logic here if not using isDeleted field
    //     // $this->jobCardsModel->delete($jobId);

    //     return $this->respondDeleted(['message' => 'Deleted successfully']);
    // }

    public function cancellStatus($jobId = 0)
    {
        $jobId = (int)getKey($jobId, "Jobcard");

        if (!UserPermissionLib::userCanDo("jobCards", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        // Load job by jobId and tenantId
        $job = $this->db->table('productionJobCards')
            ->where('tenantId', $this->user->tenantId)
            ->where('jobId', $jobId)
            ->get()
            ->getRow();

        if (!$job) {
            return $this->failNotFound('Job not found');
        }

        // Set status to "cancelled" — use numeric or text depending on your system
        $cancelledStatus = 'cancelled'; // or e.g., 3 if using status codes

        $this->db->table('productionJobCards')
            ->where('tenantId', $this->user->tenantId)
            ->where('jobId', $jobId)
            ->update(['status' => $cancelledStatus]);

        return $this->respond([
            'status' => true,
            'message' => 'Status updated to Cancelled successfully'
        ], 200);
    }






    // {{CUSTOM_FUNCTIONS_FOR_VIEW_DETAILS}}
}
