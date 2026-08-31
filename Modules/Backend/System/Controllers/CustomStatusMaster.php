<?php

namespace Modules\Backend\System\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;

use Modules\Backend\System\Models\customStatusFieldsModel;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\Auth;


class CustomStatusMaster extends ApiBaseController
{
    use ResponseTrait;

    protected $customStatusFieldsModel;

    public function __construct()
    {
        $this->customStatusFieldsModel = new customStatusFieldsModel();
    }

    public function getStatusData()
    {
        $statusData = $this->db->query("SELECT statusId as id, statusName as `name` 
    FROM statusMaster 
    WHERE isDeleted = 0 
    AND isActive = 1 
    AND tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
            ->getResult();

        $response = [
            'status' => true,
            "message" => "Status retrieved successfully",
            "data" => $statusData
        ];

        return $this->respond($response, 200);
    }





    public function save($fieldId = 0)
    {
        // debug("save");
        // die;    
        $fieldId = (int)getKey($fieldId, "field");

        if ((is_numeric($fieldId) and $fieldId === 0) or (is_string($fieldId) and $fieldId == "0")) {
            if (!UserPermissionLib::canAdd("customStatusFields")) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            $customData = $this->customStatusFieldsModel->find($fieldId);
            if (UserPermissionLib::canEdit($customData, "customStatusFields")) {
            } else {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['fieldId'] = $fieldId;

        if ($fieldId == 0) {

            $rules['fieldName'] = [
                'label'  => 'Field Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];

            $rules['fieldType'] = [
                'label'  => 'Status Type',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        } else {
            $rules['fieldName'] = [
                'label'  => 'Field Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
            $rules['fieldType'] = [
                'label'  => 'Status Type',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        }

        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }


        $successMsg = 'Status Field created successfully';
        if ($fieldId > 0) {

            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;
            $jsonInput['tenantId'] = 1;

            $successMsg = 'Status Field updated successfully';

            //check if update success
            if (!$this->customStatusFieldsModel->update($fieldId, $jsonInput)) {
                return $this->fail('Failed to update status', 500);
            }
        } else {
            // if (!empty($jsonInput['fieldOptions'])) {
            //     $jsonInput['fieldOptions'] = json_encode($jsonInput['fieldOptions']);
            // } else {
            //     $jsonInput['fieldOptions'] = json_encode([]); // Save as an empty JSON array
            // }

            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            $jsonInput['tenantId'] = 1;


            $fieldId = $this->customStatusFieldsModel->insert($jsonInput);

            if (!$fieldId) {
                return $this->fail('Failed to create status', 500);
            }
        }
        $status = $this->customStatusFieldsModel
            ->where('tenantId', 1) // Adding tenantId condition with default value 1
            ->find($fieldId);
        // debug($status);
        // die;
        return $this->respondCreated(['message' => $successMsg, 'status' => $status]);
    }

    public function get($fieldId = 0)
    {
        // debug("get");
        // die;    
        $fieldId = (int)getKey($fieldId, "field");

        if ($fieldId > 0) {
            if (!UserPermissionLib::userCanDo("customStatusFields", 'view')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $status = $this->customStatusFieldsModel
            ->where('tenantId', 1) // Adding tenantId condition with default value 1
            ->where('fieldId', $fieldId)
            ->first(); // Use first() to fetch the single result

        if (!$status) {
            return $this->failNotFound('Status not found');
        }

        return $this->respond(['status' => true, 'message' => 'Status retrieved successfully', 'data' => $status]);
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
        $defaultColumns["CSF_fieldId"] = ["title" => "", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["CSF_fieldName"] = ["title" => "Field Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["SM_statusName"] = ["title" => "Status Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["CSF_fieldType"] = ["title" => "Field Type", "visible" => true, "orderable" => true, "searchable" => true];
        // $defaultColumns["CSF_fieldOptions"] = ["title" => "Field Options	", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["CSF_isRequired"] = ["title" => "Is Required", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["CSF_isActive"] = ["title" => "Is Active", "visible" => true, "orderable" => true, "searchable" => true];




        /*****************************************
        EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $isActiveFilter = ["1" => "Active", "0" => "In Active"];
        $defaultColumns["CSF_isActive"]["filterType"] = 'select';
        $defaultColumns["CSF_isActive"]["filterOptions"] = $isActiveFilter;

        $isRequiredFilter = ["1" => "Yes", "0" => "No"];
        $defaultColumns["CSF_isRequired"]["filterType"] = 'select';
        $defaultColumns["CSF_isRequired"]["filterOptions"] = $isRequiredFilter;


        $temp = $this->db->query("SELECT GROUP_CONCAT(statusName SEPARATOR '__') as `filterData` 
    FROM statusMaster 
    WHERE tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
            ->getRow()
            ->filterData;
        $statusFilters = explode("__", $temp);
        $statusFilters = array_map('printable', $statusFilters);

        $defaultColumns["SM_statusName"]["filterType"] = 'select';
        $defaultColumns["SM_statusName"]["filterOptions"] = $statusFilters;


        /*****************************************
        EDIT 3: Define default columns here  
         ****************************************/
        $configData['defaultOrderColumn'] = 'CSF_fieldId';
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

        $ex = $this->db->query("SELECT `value` 
    FROM userSettings 
    WHERE userId = $userId 
    AND `key` = '$module' 
    AND tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
            ->getRow();

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
        $select = []; // Add the fields you need compulsary irrespective of the user settings, or leave empty []
        $where = ["CSF.isDeleted = '0' "]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.

        $dbTable = 'customStatusFields CSF
    LEFT JOIN statusMaster SM ON CSF.statusId = SM.statusId
    '; // Adding tenantId condition in the main table (customStatusFields)

        $where[] = "CSF.tenantId = " . $this->user->tenantId;
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
        $seachWhere = [];

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
                $seachWhere[] = $dbField . " LIKE :searchTerm:";
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

        $data = $this->db->query($sql, $queryParameters)->getResult();

        /****************************************************************
            EDIT 3: Add column totals data here if required, else leave empty
         ****************************************************************/
        $columnTotalsData = [];
        // $columnTotalsData['UM_serialNumber'] = 100; //you can run mysql query here to fetch total of the column

        foreach ($data as &$row) {

            if (!$isDownload) {
                /*******************************************************
                    NOTE: for html and data made for screen only, keep inside this if condition.
                 *******************************************************/
                $action = "<a href='" . base_url("customStatusMaster/editCustomStatus/" . setKey($row->CSF_fieldId, "field")) . "'><i class='fa fa-edit'></i></a>";

                $row->CSF_fieldId = $row->CSF_fieldId . " " . $action;



                ////display active status

                if ($row->CSF_isActive == 1) {
                    $row->CSF_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/customStatusMaster/changeStatus/" . setKey($row->CSF_fieldId, "field") . "'>Active</span>";
                } else {
                    $row->CSF_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/customStatusMaster/changeStatus/" . setKey($row->CSF_fieldId, "field") . "'>InActive</span>";
                }

                if ($row->CSF_isRequired == 1) {
                    $row->CSF_isRequired = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/customStatusMaster/changePrimaryStatus/" . setKey($row->CSF_fieldId, "field") . "'>Yes</span>";
                } else {
                    $row->CSF_isRequired = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/customStatusMaster/changePrimaryStatus/" . setKey($row->CSF_fieldId, "field") . "'>No</span>";
                }
            } else {
                /*******************************************************
                 specific data for printing,export will go here.
                 *******************************************************/
                if (($row->CSF_isActive == '1')) {
                    $row->CSF_isActive = "Active";
                } else {
                    $row->CSF_isActive = "In Active";
                }

                if (($row->CSF_isRequired == '1')) {
                    $row->CSF_isRequired = "Yes";
                } else {
                    $row->CSF_isRequired = "No";
                }
            }



            /*******************************************************
            general data for screen,printing,export will go here.
             *******************************************************/

            $row->SM_statusName = printable($row->SM_statusName);
            $row->CSF_fieldName = printable($row->CSF_fieldName);
            $row->CSF_fieldType = printable($row->CSF_fieldType);



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

        $totalRecords = $this->db->query("SELECT COUNT(*) as total 
    FROM $dbTable 
    WHERE CSF.isDeleted = 0 
    AND CSF.tenantId = " . $this->user->tenantId) // Adding tenantId condition with default value 1
            ->getRow()->total;
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
            // 'sql' => $sql
        ];

        return $this->respond($response, 200);
    }

    public function changeStatus($fieldId)
    {
        $fieldId = (int)getKey($fieldId, "field");
        $this->db->query("UPDATE customStatusFields 
    SET isActive = IF(isActive = 1, 0, 1) 
    WHERE fieldId = ? 
    AND tenantId = " . $this->user->tenantId, [$fieldId]); // Adding tenantId condition with default value 1
        $response = [
            'status' => true,
            "message" => "Status Reset Done.",
        ];
        return $this->respond($response, 200);
    }
    public function changePrimaryStatus($fieldId)
    {
        $fieldId = (int)getKey($fieldId, "field");
        $this->db->query("UPDATE customStatusFields 
    SET isRequired = IF(isRequired = 1, 0, 1) 
    WHERE fieldId = ? 
    AND tenantId = " . $this->user->tenantId, [$fieldId]); // Adding tenantId condition with default value 1
        $response = [
            'status' => true,
            "message" => "Status Reset Done.",
        ];
        return $this->respond($response, 200);
    }
}
