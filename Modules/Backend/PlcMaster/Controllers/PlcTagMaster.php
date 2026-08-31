<?php

namespace Modules\Backend\PlcMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\PlcMaster\Models\PlcTagMasterModel;


use CodeIgniter\API\ResponseTrait;

class PlcTagMaster extends ApiBaseController
{
    use ResponseTrait;


    protected $PlcTagMasterModel;

    public function __construct()
    {
        $this->PlcTagMasterModel = new PlcTagMasterModel();
    }

    public function save($tagId = 0)
    {

        $tagId = (int)getKey($tagId, "Plctagmaster");

        if ($tagId > 0) {
            if (!UserPermissionLib::userCanDo("PlcTagMaster", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("PlcTagMaster", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['tagId'] = $tagId;

        // validation Logic will go here
        $rules['plcId'] = [
            'label'  => 'Plc Name',
            'rules'  => 'required'
        ];


        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }

        //date/time/datetime conversion logic here.
        if (!empty($jsonInput['createdAt'])) {
            $jsonInput['createdAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['createdAt'])));
        }
        if (!empty($jsonInput['updatedAt'])) {
            $jsonInput['updatedAt'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['updatedAt'])));
        }


        $successMsg = 'Saved Successfully';
        if ($tagId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = '';

            //check if update success
            if (!$this->PlcTagMasterModel->update($tagId, $jsonInput)) {
                return $this->fail('Failed to update', 500);
            }
        } else {

            $jsonInput['tenantId'] = $this->user->tenantId;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;

            $tagId = $this->PlcTagMasterModel->insert($jsonInput);

            if (!$tagId) {
                return $this->fail('Failed to Save', 500);
            }

            assignSerialNumber($this->user->tenantId, "plcTagMaster", "tagId", $tagId);
        }

        $data = $this->PlcTagMasterModel->find($tagId);

        return $this->respondCreated(['message' => $successMsg, 'data' => $data]);
    }

    public function get($tagId)
    {
        $tagId = (int)getKey($tagId, "Plctagmaster");

        if (!UserPermissionLib::userCanDo("PlcTagMaster", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $details = $this->PlcTagMasterModel->find($tagId);

        if (!$details) {
            return $this->failNotFound('data not found');
        }

        $details->uid = setKey($details->tagId, "Plctagmaster");

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

        $defaultColumns['plcTagMaster_tagId'] = ['title' => 'Sr.', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['plcMaster_plcName'] = ['title' => 'Plc Name', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_tagName'] = ['title' => 'Tag Name', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_tagAddress'] = ['title' => 'Tag Address', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_dataType'] = ['title' => 'Data Type', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_registerType'] = ['title' => 'Register Type', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_readWrite'] = ['title' => 'Read Write', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_scaleFactor'] = ['title' => 'Scale Factor', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_offset'] = ['title' => 'Offset', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_unit'] = ['title' => 'Unit', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_description'] = ['title' => 'Description', 'visible' => true, 'orderable' => true, 'searchable' => true, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_isActive'] = ['title' => 'Status', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['userMaster_username'] = ['title' => 'Username', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['plcTagMaster_createdAt'] = ['title' => 'Created At', 'visible' => true, 'orderable' => true, 'searchable' => false, 'visibleControl' => true];
        $defaultColumns['userMaster_username'] = ['title' => 'Username', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];
        $defaultColumns['plcTagMaster_updatedAt'] = ['title' => 'Updated At', 'visible' => false, 'orderable' => true, 'searchable' => false, 'visibleControl' => false];




        /*****************************************
                EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $temp = $this->db->query("SELECT GROUP_CONCAT(plcName SEPARATOR '__') as `filterData` FROM plcMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['plcMaster_plcName']['filterType'] = 'select';
        $defaultColumns['plcMaster_plcName']['filterOptions'] = $foreignFilters;


        $filterData = ['Boolean' => 'Boolean', 'SByte' => 'Sbyte', 'Byte' => 'Byte', 'Int16' => 'Int16', 'UInt16' => 'Uint16', 'Int32' => 'Int32', 'UInt32' => 'Uint32', 'Int64' => 'Int64', 'UInt64' => 'Uint64', 'Float' => 'Float', 'Double' => 'Double', 'String' => 'String', 'DateTime' => 'Date Time'];
        $defaultColumns['plcTagMaster_dataType']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_dataType']['filterOptions'] = $filterData;


        $filterData = ['coil' => 'Coil', 'holding' => 'Holding', 'input' => 'Input', 'discrete' => 'Discrete', 'variable' => 'Variable'];
        $defaultColumns['plcTagMaster_registerType']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_registerType']['filterOptions'] = $filterData;


        $filterData = ['read' => 'Read', 'write' => 'Write', 'readwrite' => 'Readwrite'];
        $defaultColumns['plcTagMaster_readWrite']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_readWrite']['filterOptions'] = $filterData;


        $filterData = ['1' => 'Active', '0' => 'Inactive'];
        $defaultColumns['plcTagMaster_isActive']['filterType'] = 'select';
        $defaultColumns['plcTagMaster_isActive']['filterOptions'] = $filterData;


        $temp = $this->db->query("SELECT GROUP_CONCAT(username SEPARATOR '__') as `filterData` FROM userMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['userMaster_username']['filterType'] = 'select';
        $defaultColumns['userMaster_username']['filterOptions'] = $foreignFilters;


        $defaultColumns['plcTagMaster_createdAt']['filterType'] = 'date';
        $defaultColumns['plcTagMaster_createdAt']['filterOptions'] = dateFilterOptions('past');


        $temp = $this->db->query("SELECT GROUP_CONCAT(username SEPARATOR '__') as `filterData` FROM userMaster")->getRow()->filterData;
        $foreignFilters = explode('__', $temp);
        $defaultColumns['userMaster_username']['filterType'] = 'select';
        $defaultColumns['userMaster_username']['filterOptions'] = $foreignFilters;


        $defaultColumns['plcTagMaster_updatedAt']['filterType'] = 'date';
        $defaultColumns['plcTagMaster_updatedAt']['filterOptions'] = dateFilterOptions('past');






        /*****************************************
                EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'plcTagMaster_tagId';
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
            "data" => $configData,
        ];

        return $this->respond($response, 200);
    }

    public function getDataTableData()
    {


        /*****************************************
                EDIT 1: Define default select and where conditions here  
         *****************************************/
        $select = ["plcTagMaster.serialNo as plcTagMaster_serialNo"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []

        $where = ["1"];
        $where[] = "plcTagMaster.tenantId = '" . $this->user->tenantId . "'";


        // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'plcTagMaster LEFT JOIN userMaster userMaster ON userMaster.userId = plcTagMaster.createdBy 
LEFT JOIN plcMaster plcMaster ON plcMaster.plcId = plcTagMaster.plcId 
LEFT JOIN tenantMaster tenantMaster ON tenantMaster.tenantId = plcTagMaster.tenantId 
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
        $customFilters = $jsonInput['customFilters'] ?? [];

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
                    /********************************************
                            WARNING:: DO NOT EDIT BELOW THIS LINE
                     ********************************************/
                } else {
                    $where[] = "$dbField = :" . $columnName . "_filter:";
                    $queryParameters[$columnName . '_filter'] = $filters[$columnName];
                }
            }
        }

        //start code custom filter code...
        foreach ($customFilters as $k => $v) {
            if ($k == "plcId") {
                $plcId = (int) getKey($v, 'plc');

                $where[] = "plcMaster.plcId = $plcId"; // Assuming the first status is 'New'
                // if ($v == "fresh") {
                //     $where[] = "LM.statusId = $freshStatusId"; // Assuming the first status is 'New'
                // }
                //     } else if ($v == "today") {
                //         $today = date("Y-m-d");
                //         $where[] = "date(LM.nextFoTime) = '$today' AND LM.statusId IN ($openStatusIds) AND LM.statusId != $freshStatusId"; // Assuming statusId 1 is for 'New' leads
                //     } else if ($v == "missed") {
                //         $today = date("Y-m-d");
                //         $where[] = "date(LM.nextFoTime) < '$today' AND LM.statusId IN ($openStatusIds) AND LM.statusId != $freshStatusId"; // Assuming statusId 1 is for 'New' leads
                //     } else if ($v == "next7days") {
                //         $today = date("Y-m-d");
                //         $next7Days = date("Y-m-d", strtotime("+7 days"));
                //         $where[] = "date(LM.nextFoTime) > '$today' AND date(LM.nextFoTime) <= '$next7Days' AND LM.statusId IN ($openStatusIds) AND LM.statusId != $freshStatusId"; // Assuming statusId 1 is for 'New' leads
                //     } else if ($v == "team") {
                //         $where[] = "FIND_IN_SET(" . $this->user->userId . ", LM.leadTeamId) > 0 AND LM.assignedTo != " . $this->user->userId; // Filter for leads assigned to the user's team
                //     }
            }

            // if ($k == "statusType") {
            //     if ($v == "open") {
            //         $where[] = "LM.statusId IN ($openStatusIds)";
            //     } else if ($v == "won") {
            //         $where[] = "LM.statusId IN ($wonStatusIds)";
            //     } else if ($v == "lost") {
            //         $where[] = "LM.statusId IN ($lostStatusIds)";
            //     }
            // }
        }
        //end code custom filter code...

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

            $primaryKey = $row->plcTagMaster_tagId;

            if (!$isDownload) {
                /********************************************
                    EDIT 4: Process data here only for screen view
                 ********************************************/

                $dropdownItems = [];
                //edit button
                if (UserPermissionLib::userCanDo("PlcTagMaster", 'edit')) {
                    $dropdownItems[] = ['label' => 'Edit', 'class' => 'apiPopup text-warning', 'icon' => 'fa fa-edit', 'attributes' => "data-size='xl' data-title='PlcTagMaster Form' data-endpoint='" . ("PlcTagMaster/editPlctagmaster/" . setKey($primaryKey, "Plctagmaster")) . "'"];
                }
                //delete button
                // if (UserPermissionLib::userCanDo("PlcTagMaster", 'delete')) {
                //     $dropdownItems[] = ['label' => 'Delete', 'href' => 'javascript:;', 'class' => 'text-danger apiAction', 'icon' => 'fa fa-trash', 'attributes' => "data-confirm='Are you sure to delete this Plctagmaster?' data-endpoint='" . ("api/PlcTagMaster/delete/" . setKey($primaryKey, "Plctagmaster")) . "'"];
                // }

                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->plcTagMaster_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->plcTagMaster_tagId = $dropdownHtml;



                /*******************************************************/
                //dataType: Switch Code Starts Here 
                //If not required, comment this code, uncomment last line of this code part, remove "switchDataType" function code, remove route from route file for proper cleanup.
                /*******************************************************/
                $dropdownItems = [];
                $enumList = ['Boolean', 'SByte', 'Byte', 'Int16', 'UInt16', 'Int32', 'UInt32', 'Int64', 'UInt64', 'Float', 'Double', 'String', 'DateTime'];
                foreach ($enumList as $option) {
                    if ($option == $row->plcTagMaster_dataType) {
                        continue;
                    }
                    $dropdownItems[] = ['label' => printable($option), 'href' => 'javascript:;', 'class' => 'text-dark apiAction', 'attributes' => "data-confirm='Are you sure to change " . printable('dataType') . "?' data-endpoint='" . ("api/PlcTagMaster/switchDataType/" . setKey($primaryKey, "Plctagmaster")) . "/$option'"];
                }

                $dropdown = [
                    'id' => 'actionDropdown_dataType_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-secondary align-items-center gap-1',
                    'toggleLabel' => printable($row->plcTagMaster_dataType),
                    'toggleAttributes' => 'title="Click to Change"',
                    'menuClass' => 'dropdown-menu-start',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);

                $row->plcTagMaster_dataType = $dropdownHtml;

                /*******************************************************/
                //dataType: Switch Code Starts Here 
                //if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box
                //$row->plcTagMaster_dataType = printable($row->plcTagMaster_dataType);


                /*******************************************************/
                //registerType: Switch Code Starts Here 
                //If not required, comment this code, uncomment last line of this code part, remove "switchRegisterType" function code, remove route from route file for proper cleanup.
                /*******************************************************/
                $dropdownItems = [];
                $enumList = ['coil', 'holding', 'input', 'discrete', 'variable'];
                foreach ($enumList as $option) {
                    if ($option == $row->plcTagMaster_registerType) {
                        continue;
                    }
                    $dropdownItems[] = ['label' => printable($option), 'href' => 'javascript:;', 'class' => 'text-dark apiAction', 'attributes' => "data-confirm='Are you sure to change " . printable('registerType') . "?' data-endpoint='" . ("api/PlcTagMaster/switchRegisterType/" . setKey($primaryKey, "Plctagmaster")) . "/$option'"];
                }

                $dropdown = [
                    'id' => 'actionDropdown_registerType_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-secondary align-items-center gap-1',
                    'toggleLabel' => printable($row->plcTagMaster_registerType),
                    'toggleAttributes' => 'title="Click to Change"',
                    'menuClass' => 'dropdown-menu-start',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);

                $row->plcTagMaster_registerType = $dropdownHtml;

                /*******************************************************/
                //registerType: Switch Code Starts Here 
                //if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box
                //$row->plcTagMaster_registerType = printable($row->plcTagMaster_registerType);


                /*******************************************************/
                //readWrite: Switch Code Starts Here 
                //If not required, comment this code, uncomment last line of this code part, remove "switchReadWrite" function code, remove route from route file for proper cleanup.
                /*******************************************************/
                $dropdownItems = [];
                $enumList = ['read', 'write', 'readwrite'];
                foreach ($enumList as $option) {
                    if ($option == $row->plcTagMaster_readWrite) {
                        continue;
                    }
                    $dropdownItems[] = ['label' => printable($option), 'href' => 'javascript:;', 'class' => 'text-dark apiAction', 'attributes' => "data-confirm='Are you sure to change " . printable('readWrite') . "?' data-endpoint='" . ("api/PlcTagMaster/switchReadWrite/" . setKey($primaryKey, "Plctagmaster")) . "/$option'"];
                }

                $dropdown = [
                    'id' => 'actionDropdown_readWrite_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-secondary align-items-center gap-1',
                    'toggleLabel' => printable($row->plcTagMaster_readWrite),
                    'toggleAttributes' => 'title="Click to Change"',
                    'menuClass' => 'dropdown-menu-start',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);

                $row->plcTagMaster_readWrite = $dropdownHtml;

                /*******************************************************/
                //readWrite: Switch Code Starts Here 
                //if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box
                //$row->plcTagMaster_readWrite = printable($row->plcTagMaster_readWrite);


                /***********************************************************************/
                //isActive: Toggle Code Starts 
                //If not required, comment this code, uncomment last line of this code part, remove "toggleIsActive" function code, remove route from route file for proper cleanup.
                /***********************************************************************/
                $enumList = ['1' => 'Active', '0' => 'Inactive'];

                $className = 'bg-success';
                if ($row->plcTagMaster_isActive == 0) {
                    $className = 'bg-danger';
                }
                $row->plcTagMaster_isActive = "<span title='Click to toggle " . printable('isActive') . "' class='badge $className bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/PlcTagMaster/toggleIsActive/" . setKey($primaryKey, "Plctagmaster") . "'>" . $enumList[$row->plcTagMaster_isActive] . "</span>";

                /***********************************************************************/
                //isActive: Toggle Code Ends 
                //if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box
                /***********************************************************************/
                //$enumList = ['1' => 'Active', '0' => 'Inactive'];
                //$row->plcTagMaster_isActive = $enumList[$row->plcTagMaster_isActive];


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

            $row->plcTagMaster_createdAt = myDateTimeFormat($row->plcTagMaster_createdAt);
            $row->plcTagMaster_updatedAt = myDateTimeFormat($row->plcTagMaster_updatedAt);


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

    public function delete($tagId = 0)
    {
        $tagId = (int)getKey($tagId, "Plctagmaster");

        if (!UserPermissionLib::userCanDo("PlcTagMaster", 'delete')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($tagId == 0) {
            return $this->fail('Invalid request', 400);
        }

        //set isDeleted = 1
        $this->PlcTagMasterModel->update($tagId, ['isDeleted' => 1]);

        // or put delete logic here if not using isDeleted field
        // $this->PlcTagMasterModel->delete($tagId);

        return $this->respondDeleted(['message' => 'Deleted successfully']);
    }


    public function toggleIsActive($tagId)
    {
        $tagId = (int)getKey($tagId, "Plctagmaster");

        if (!UserPermissionLib::userCanDo("PlcTagMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($tagId == 0) {
            return $this->fail('Invalid request', 400);
        }

        $this->db->table('plcTagMaster')->set('isActive', 'NOT isActive', false)->where('tagId', $tagId)->update();

        $response = [
            'status' => true,
            "message" => "",
        ];

        return $this->respond($response, 200);
    }




    public function switchDataType($tagId, $value)
    {
        $tagId = (int)getKey($tagId, "Plctagmaster");

        if (!UserPermissionLib::userCanDo("PlcTagMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($tagId == 0) {
            return $this->fail('Invalid request', 400);
        }

        $this->db->table('plcTagMaster')->set('dataType', $value)->where('tagId', $tagId)->update();

        $response = [
            'status' => true,
            "message" => "",
        ];

        return $this->respond($response, 200);
    }


    public function switchRegisterType($tagId, $value)
    {
        $tagId = (int)getKey($tagId, "Plctagmaster");

        if (!UserPermissionLib::userCanDo("PlcTagMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($tagId == 0) {
            return $this->fail('Invalid request', 400);
        }

        $this->db->table('plcTagMaster')->set('registerType', $value)->where('tagId', $tagId)->update();

        $response = [
            'status' => true,
            "message" => "",
        ];

        return $this->respond($response, 200);
    }


    public function switchReadWrite($tagId, $value)
    {
        $tagId = (int)getKey($tagId, "Plctagmaster");

        if (!UserPermissionLib::userCanDo("PlcTagMaster", 'edit')) {
            return $this->failForbidden('Insufficient permissions');
        }

        if ($tagId == 0) {
            return $this->fail('Invalid request', 400);
        }

        $this->db->table('plcTagMaster')->set('readWrite', $value)->where('tagId', $tagId)->update();

        $response = [
            'status' => true,
            "message" => "",
        ];

        return $this->respond($response, 200);
    }

    // {{CUSTOM_FUNCTIONS_FOR_VIEW_DETAILS}}
}
