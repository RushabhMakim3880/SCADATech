<?php

namespace Modules\Backend\System\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;

use Modules\Backend\System\Models\tenantMasterModel;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\Auth;
use App\Libraries\SelfRefDataLib;

class TenantMaster extends ApiBaseController

{
    use ResponseTrait;


    protected $tenantMasterModel;

    public function __construct()
    {
        $this->tenantMasterModel = new tenantMasterModel();
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *              
     *         )
     *     )
     * )
     */

    public function changeStatus($tenantId)
    {
        $tenantId = (int)getKey($tenantId, "tenantMaster");

        //reset login lockout and failed login attempts
        $this->db->query("UPDATE tenantMaster SET isActive = !isActive WHERE tenantId = ?", [$tenantId]);

        $response = [
            'status' => true,
            "message" => "Status Updated Successfully.",
        ];

        return $this->respond($response, 200);
    }

    public function save($tenantId = 0)
    {
        // debug('hi');
        // die;
        $tenantId = (int)getKey($tenantId, "tenantMaster");

         if ((is_numeric($tenantId) and $tenantId === 0) or (is_string($tenantId) and $tenantId == "0")) {
            if (!UserPermissionLib::canAdd("tenantMaster")) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            $tenantData = $this->tenantMasterModel->find($tenantId);
            if (UserPermissionLib::canEdit($tenantData, "tenantMaster")) {
            } else {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];
        $locationId = $jsonInput['locationId'];



        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['tenantId'] = $tenantId;

        if ($tenantId == 0) {

            $rules['subDomain'] = [
                'label'  => 'Sub Domain',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        } else {
            $rules['tenantId'] = [
                'label'  => 'Customer ID',
                'rules'  => 'permit_empty|integer|is_natural',
                'errors' => [
                    'integer' => 'The {field} must be an integer.'
                ]
            ];
            $rules['subDomain'] = [
                'label'  => 'Sub Domain',
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

        $successMsg = 'Tenant Saved successfully';
        if ($tenantId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;
            if (isset($jsonInput['locationId']) && $jsonInput['locationId'] === 'null') {
                $jsonInput['locationId'] = null; // Ensure it's null, not 'null' as a string
            }
            $successMsg = 'Tenant Updated successfully';

            //check if update success
            if (!$this->tenantMasterModel->update($tenantId, $jsonInput)) {
                return $this->fail('Failed to update Customer', 500);
            }
        } else {
            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            if (empty($locationId)) {
                $jsonInput['locationId'] = null; // Set to null if the user selected "Default"

            }
            $tenantId = $this->tenantMasterModel->insert($jsonInput);

            if (!$tenantId) {
                return $this->fail('Failed to create Tenant', 500);
            }
        }
        $tenant = $this->tenantMasterModel->find($tenantId);

        return $this->respondCreated(['message' => $successMsg, 'tenant' => $tenant]);
    }

    public function get($tenantId = 0)
    {
        $tenantId = (int)getKey($tenantId, "tenantMaster");

        if (!UserPermissionLib::userCanDo("tenantMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $tenant = $this->tenantMasterModel->find($tenantId);

        if (!$tenant) {
            return $this->failNotFound('Tenant not found');
        }


        // Check if locationId is null
        if (is_null($tenant->locationId)) {
            // Set a default value or handle the null case
            $tenant->locationId = [
                "id" => null, // or set a default ID if applicable
                "text" => "Default" // Set a default text
            ];
        } else {

            $lib = new SelfRefDataLib(
                "locationMaster",
                "locationName",
                "parentLocationId"
            );

            $tenant->locationId = $lib->getSelect2Data($tenant->locationId);
        }


        return $this->respond(['status' => true, 'message' => 'Data Saved successfully', 'data' => $tenant]);
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
        $defaultColumns["TM_tenantId"] = ["title" => "", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["TM_subDomain"] = ["title" => "Sub Domain Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["TM_customDomain"] = ["title" => "Custom Domain", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["TM_tenantName"] = ["title" => "Tenant Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["TM_companyName"] = ["title" => "Company Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["TM_mobile"] = ["title" => "Mobile", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["TM_email"] = ["title" => "Email", "visible" => false, "orderable" => true];
        $defaultColumns["TM_companyAddress"] = ["title" => "Company Address", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["LM_locationName"] = ["title" => "Location Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["TM_tenantType"] = ["title" => "Type", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["TM_isActive"] = ["title" => "Status", "visible" => true, "orderable" => true, "searchable" => true];


        /*****************************************
            EDIT 2: Define required columns filter and type with options here
         *****************************************/
        //Profession type
        $temp = $this->db->query("SELECT GROUP_CONCAT(subDomain SEPARATOR '__') as `filterData` FROM tenantMaster")->getRow()->filterData;
        $subDomainFilters = explode("__", $temp);
        $subDomainFilters = array_map('printable', $subDomainFilters);

        $defaultColumns["TM_subDomain"]["filterType"] = 'select';
        $defaultColumns["TM_subDomain"]["filterOptions"] = $subDomainFilters;

        //status filter
        $isActiveFilter = ["1" => "Active", "0" => "Inactive"];
        $defaultColumns["TM_isActive"]["filterType"] = 'select';
        $defaultColumns["TM_isActive"]["filterOptions"] = $isActiveFilter;

        $tenantTypeFilter = ["clientLive" => "Client Live", "clientTrial" => "Client Trial", "clientFree" => "Client Free", "personalLive" => "Personal Live", "personalDemo" => "Personal Demo"];
        $defaultColumns["TM_tenantType"]["filterType"] = 'select';
        $defaultColumns["TM_tenantType"]["filterOptions"] = $tenantTypeFilter;


        /*****************************************
            EDIT 3: Define default columns here  
         *****************************************/

        $configData['defaultOrderColumn'] = 'TM_tenantId';
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

        $ex = $this->db->query("SELECT `value` FROM userSettings WHERE userId = $userId AND `key` = '$module'")->getRow();

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
        // $where = ["UM.groupId IS NOT NULL"]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        $select = []; // Add the fields you need compulsary irrespective of the user settings, or leave empty []
        $where = ["1"]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        $dbTable = 'tenantMaster TM 
                    LEFT JOIN locationMaster LM ON LM.locationId = TM.locationId';

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
        // $columnTotalsData['UM_serialNumber'] = 100; //you can run mysql query here to fetch total of the column


        foreach ($data as &$row) {

            if (!$isDownload) {
                /*******************************************************
                    NOTE: for html and data made for screen only, keep inside this if condition.
                 *******************************************************/
                // $action = "<a href='" . base_url("customer/editCustomer/" . setKey($row->CM_customerId, "customer")) . "'><i class='fa fa-edit'></i></a>";

                // $row->CM_customerId = $row->CM_customerId . " " . $action;
                $tenantId = $row->TM_tenantId;
                $dropdown = tenantMasterDropdown($tenantId);

                $tenantMasterDropdown = '<div title="Click to open for more options"  class="dropdown d-inline-block">
                      <a class="btn btn-xs btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . manageScreenId($tenantId) . '</a>
                      ' . $dropdown .  '
                  </div>';
                if ($row->TM_isActive == 1) {
                    $row->TM_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/tenantMaster/changeStatus/" . setKey($tenantId, "tenantMaster") . "'>Active</span>";
                } else {
                    $row->TM_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/tenantMaster/changeStatus/" . setKey($tenantId, "tenantMaster") . "'>InActive</span>";
                }
                $row->TM_tenantId = $tenantMasterDropdown;
            } else {
                /*******************************************************
                specific data for printing,export will go here.
                 *******************************************************/
                if (($row->TM_isActive == '1')) {
                    $row->TM_isActive = "Active";
                } else {
                    $row->TM_isActive = "In Active";
                }
            }

            /*******************************************************
                general data for screen,printing,export will go here.
             *******************************************************/

            $row->TM_subDomain = printable($row->TM_subDomain);
            $row->TM_customDomain = printable($row->TM_customDomain);
            $row->TM_tenantName = printable($row->TM_tenantName);
            // $row->LM_locationName = printable($row->LM_locationName);
            $row->LM_locationName = is_null($row->LM_locationName) ? "Default" : printable($row->LM_locationName);

            $row->TM_companyName = printable($row->TM_companyName);
            $row->TM_tenantType = printable($row->TM_tenantType);

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
            // 'sql' => $sql
        ];

        return $this->respond($response, 200);
    }

    public function tenantDropdown()
    {
        if (!UserPermissionLib::userCanDo("tenantMaster", 'viewAll')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $data = $this->db->query("SELECT tenantId as id, tenantName as name FROM tenantMaster WHERE isActive = 1")->getResult();

        $response = [
            'status' => true,
            // "message" => "Tenant retrieved successfully",
            "data" => $data
        ];

        return $this->respond($response, 200);
    }
}
