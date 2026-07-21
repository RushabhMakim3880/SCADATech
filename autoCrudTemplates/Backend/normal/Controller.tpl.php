<?php

namespace Modules\Backend\{{MODULE_NAME}}\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\{{MODULE_NAME}}\Models\{{MODULE_NAME}}Model;


use CodeIgniter\API\ResponseTrait;

class {{MODULE_NAME}} extends ApiBaseController
{
    use ResponseTrait;


    protected ${{MODULE_NAME}}Model;

    public function __construct()
    {
        $this->{{MODULE_NAME}}Model = new {{MODULE_NAME}}Model();
        
    }

    public function save(${{PRIMARY_FIELD}} = 0)
    {

        ${{PRIMARY_FIELD}} = (int)getKey(${{PRIMARY_FIELD}}, "{{ITEM_NAME}}");

        if (${{PRIMARY_FIELD}} > 0) {
            if (!UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['{{PRIMARY_FIELD}}'] = ${{PRIMARY_FIELD}};

// validation Logic will go here
{{VALIDATION_RULES}}

$validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }

        //date/time/datetime conversion logic here.
        {{DATE_TIME_CONVERSION}}

        $successMsg = 'Saved Successfully';
        if (${{PRIMARY_FIELD}} > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'Updated successfully';

            //check if update success
            if (!$this->{{MODULE_NAME}}Model->update(${{PRIMARY_FIELD}}, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {
            {{AUTO_SAVE_TENANT_ID}}

            // $jsonInput['userId'] = $this->user->id;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;
            ${{PRIMARY_FIELD}} = $this->{{MODULE_NAME}}Model->insert($jsonInput);

            if (!${{PRIMARY_FIELD}}) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "{{TABLE_NAME}}", "{{PRIMARY_FIELD}}", ${{PRIMARY_FIELD}});
        }
        
        $data = $this->{{MODULE_NAME}}Model->find(${{PRIMARY_FIELD}});

        return $this->respondCreated(['message' => $successMsg, 'data' => $data]);
    }

    public function get(${{PRIMARY_FIELD}})
    {
        ${{PRIMARY_FIELD}} = (int)getKey(${{PRIMARY_FIELD}}, "{{ITEM_NAME}}");

        if (!UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->{{MODULE_NAME}}Model->find(${{PRIMARY_FIELD}});

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->uid = setKey($details->{{PRIMARY_FIELD}}, "{{ITEM_NAME}}");

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

        {{DATATABLE_COLUMNS}}

        

        /*****************************************
                EDIT 2: Define required columns filter and type with options here
        *****************************************/

        {{DATATABLE_FILTERS}}

        

        /*****************************************
                EDIT 3: Define default columns here  
        *****************************************/
        $configData['defaultOrderColumn'] = '{{TABLE_NAME}}_{{PRIMARY_FIELD}}';
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

        $ex = $this->db->query("SELECT `value` FROM userSettings WHERE userId = $userId AND tenantId='".$this->user->tenantId."' AND `key` = '$module'")->getRow();

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
        $select = ["{{TABLE_NAME}}.serialNo as {{TABLE_NAME}}_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        {{DELETED_FILETER}} // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        {{DB_TABLE}}

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
        {{COLUMN_TOTALS}}


    $config = config('AppConfig');
    $mobileView = [];

        foreach ($data as $k=>&$row) {

            $primaryKey = $row->{{TABLE_NAME}}_{{PRIMARY_FIELD}};

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                ********************************************/            
                $dropdownItems = [];
                //edit button
                if (UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'edit')) {
                    $dropdownItems[] = ['label' => 'Edit', 'class' => 'text-warning', 'icon' => 'fa fa-edit', 'href' => base_url("{{MODULE_NAME}}/edit{{ITEM_NAME}}/" . setKey($primaryKey, "{{ITEM_NAME}}"))];
                }
                //delete button
                if (UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'delete')) {
                    $dropdownItems[] = ['label' => 'Delete', 'href' => 'javascript:;', 'class' => 'text-danger apiAction', 'icon' => 'fa fa-trash', 'attributes' => "data-confirm='Are you sure to delete this {{ITEM_NAME}}?' data-endpoint='" . ("api/{{MODULE_NAME}}/delete/" . setKey($primaryKey, "{{ITEM_NAME}}")) . "'"];
                }

                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->{{TABLE_NAME}}_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->{{TABLE_NAME}}_{{PRIMARY_FIELD}} = $dropdownHtml;

                {{SCREEN_DATA_PROCESSING}}

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

            {{GENERAL_DATA_PROCESSING}} 
            
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

    public function delete(${{PRIMARY_FIELD}}=0)
    {
        ${{PRIMARY_FIELD}} = (int)getKey(${{PRIMARY_FIELD}}, "{{ITEM_NAME}}");

        if (!UserPermissionLib::userCanDo("{{MODULE_NAME}}", 'delete')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if (${{PRIMARY_FIELD}} == 0) {
            return $this->fail('Invalid request', 400);
        }

        //set isDeleted = 1
        $this->{{MODULE_NAME}}Model->update(${{PRIMARY_FIELD}}, ['isDeleted' => 1]);

        // or put delete logic here if not using isDeleted field
        // $this->{{MODULE_NAME}}Model->delete(${{PRIMARY_FIELD}});

        return $this->respondDeleted(['message' => 'Deleted successfully']);
    }

    {{CUSTOM_FUNCTIONS_FOR_TOGGLE}}

    {{CUSTOM_FUNCTIONS_FOR_SWITCH}}
}
