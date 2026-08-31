<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\ApiBaseController;
use App\Libraries\UserPermissionLib;
// use OpenApi\Annotations as OA;
class TrustedDevice extends ApiBaseController
{

    public function getDataTableColumns($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        /*****************************************
            EDIT 1: Define default columns here  
         *****************************************/
        $defaultColumns = [];
        $defaultColumns["TD_deviceId"] = ["title" => "Sr.", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["TD_userId"] = ["title" => "User ID", "visible" => true, "orderable" => true, "searchable" => true, "visibleControl" => true];
        $defaultColumns["TD_deviceToken"] = ["title" => "Device Token", "visible" => true, "orderable" => true, "searchable" => true, "visibleControl" => true];
        $defaultColumns["TD_userAgent"] = ["title" => "User Agent", "visible" => true, "orderable" => true, "searchable" => true, "visibleControl" => true];
        $defaultColumns["TD_ipAddress"] = ["title" => "Ip Address", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        $defaultColumns["TD_isApproved"] = ["title" => "Status", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        $defaultColumns["TD_expiresAt"] = ["title" => "Expires At", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        $defaultColumns["TD_approvedBy"] = ["title" => "Approved By", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        $defaultColumns["TD_approvedAt"] = ["title" => "Approved At", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        $defaultColumns["TD_lastUsedAt"] = ["title" => "Last Used At", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        $defaultColumns["TD_createdAt"] = ["title" => "Created At", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => true];
        /*****************************************
            EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $isActiveFilter = ["1" => "Approved", "0" => "Approve"];
        $defaultColumns["TD_isApproved"]["filterType"] = 'select';
        $defaultColumns["TD_isApproved"]["filterOptions"] = $isActiveFilter;

        $temp = $this->db->query("SELECT userId,firstName,lastName FROM userMaster WHERE groupId >0 AND isActive = 1 AND tenantId = " . $this->user->tenantId)->getResult();
        $userFilters = [];
        foreach ($temp as $t) {
            $userFilters[$t->userId] = "$t->firstName $t->lastName";
        }
        $defaultColumns["TD_userId"]["filterType"] = 'select';
        $defaultColumns["TD_userId"]["filterOptions"] = $userFilters;

        $defaultColumns["TD_approvedBy"]["filterType"] = 'select';
        $defaultColumns["TD_approvedBy"]["filterOptions"] = $userFilters;

        $defaultColumns["TD_approvedAt"]["filterType"] = 'date';
        $defaultColumns["TD_approvedAt"]["filterOptions"] = dateFilterOptions("past");

        $defaultColumns["TD_expiresAt"]["filterType"] = 'date';
        $defaultColumns["TD_expiresAt"]["filterOptions"] = dateFilterOptions("");

        $defaultColumns["TD_lastUsedAt"]["filterType"] = 'date';
        $defaultColumns["TD_lastUsedAt"]["filterOptions"] = dateFilterOptions("past");

        $defaultColumns["TD_createdAt"]["filterType"] = 'date';
        $defaultColumns["TD_createdAt"]["filterOptions"] = dateFilterOptions("past");

        /*****************************************
            EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'TD_deviceId';
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
        $select = ["TD.serialNo as TD_serialNo", "TD.deviceId as TD_deviceId"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []
        $where = ["TD.tenantId = " . $this->user->tenantId]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        $dbTable = 'trustedDevices TD ';

        if (!UserPermissionLib::userCanDo("userMaster", 'manageApprovedDevices')) {
            return $this->failForbidden('Insufficient permissions');
        }
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
                    if ($columnName == 'UM_lockoutUntil') {
                        if ($filters[$columnName] == 1) {
                            $where[] = "$dbField IS NOT NULL AND $dbField > :" . $columnName . "_now:";
                            $queryParameters[$columnName . '_now'] = timenow();
                        } else if ($filters[$columnName] == 0) {
                            $where[] = "$dbField IS NULL OR $dbField < :" . $columnName . "_now:";
                            $queryParameters[$columnName . '_now'] = timenow();
                        }
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
        if (!empty($jsonInput['order'])) {
            $orderIndex = $jsonInput['order'][0]["column"];
            $orderDirection = $jsonInput['order'][0]["dir"];
            $orderColumn = $columns[$orderIndex]['data'];
            $orderBy = " ORDER BY $orderColumn $orderDirection";
        } else {
            $orderBy = " ORDER BY UM_userId ASC";
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

        $config = config('AppConfig');
        $mobileView = [];

        foreach ($data as $k => &$row) {


            if (!$isDownload) {
                /*******************************************************
                    NOTE: for html and data made for screen only, keep inside this if condition.
                 *******************************************************/
                //dropdown code start here...
                $dropdownItems = [];

                $primaryKey = $row->TD_deviceId;

                $dropdownItems[] = ['label' => 'Delete', 'href' => 'javascript:;', 'class' => 'text-danger apiAction', 'icon' => 'fa fa-trash', 'attributes' => "data-confirm='Are you sure to delete this Trusted Device?' data-endpoint='" . ("api/trustedDevice/deleteTrustedDevice/" . setKey($primaryKey, "trustedDevice")) . "'"];

                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->TD_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->TD_deviceId = $dropdownHtml;
                // //dropdown code end here...

                //status
                if ($row->TD_isApproved == 1) {
                    $row->TD_isApproved = "<span class='badge bg-success'>Approved</span>";
                } else {
                    $row->TD_isApproved = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/trustedDevice/changeTrustedDeviceStatus/" . setKey($primaryKey, "trustedDevice") . "'>Approve</span>";
                }

                $mobileView[$k] = [
                    "titleBox1" => username($row->TD_userId),
                    "descriptionBox1" => "$row->TD_deviceToken",
                    "titleBox2" => "",
                    "descriptionBox2" => "$row->TD_expiresAt",
                    "actionBox" => '',
                    "statusBox" => "$row->TD_isApproved",
                    "dateBox" => "<span class='badge bg-secondary'>" . humanTimeDifference($row->TD_createdAt) . "</span>",
                ];
            } else {
                /*******************************************************
                specific data for printing,export will go here.
                 *******************************************************/
                //status
                if ($row->TD_isApproved == 1) {
                    $row->TD_isApproved = "Approved";
                } else {
                    $row->TD_isApproved = "Approve";
                }
            }

            /*******************************************************
                general data for screen,printing,export will go here.
             *******************************************************/

            $row->TD_userId = username($row->TD_userId);
            $row->TD_expiresAt = empty($row->TD_expiresAt) ? 'N/A' : myDateTimeFormat($row->TD_expiresAt);
            $row->TD_approvedBy = username($row->TD_approvedBy);
            $row->TD_approvedAt = empty($row->TD_approvedAt) ? 'N/A' : myDateTimeFormat($row->TD_approvedAt);
            $row->TD_lastUsedAt = empty($row->TD_lastUsedAt) ? 'N/A' : myDateTimeFormat($row->TD_lastUsedAt);
            $row->TD_createdAt = myDateTimeFormat($row->TD_createdAt);

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

        $totalRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable WHERE TD.tenantId = " . $this->user->tenantId)->getRow()->total;
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
            'extraData' => [],
            // 'sql' => $sql
        ];

        return $this->respond($response, 200);
    }

    //delete Trusted Device
    public function deleteTrustedDevice($deviceId)
    {
        $config = config('AppConfig');
        if (!$config->limitLoginToTrustedDevices == 1) {
            return redirect()->to('');
        }

        $deviceId = getkey($deviceId, "trustedDevice");

        $this->db->query("DELETE FROM trustedDevices WHERE deviceId = ? AND tenantId = ?", [$deviceId, $this->user->tenantId]);
        $response = [
            'status' => true,
            "message" => "Data Delete Successfully",
        ];

        return $this->respond($response, 200);
    }

    public function changeTrustedDeviceStatus($deviceId)
    {
        $config = config('AppConfig');
        if (!$config->limitLoginToTrustedDevices == 1) {
            return redirect()->to('');
        }

        $deviceId = getkey($deviceId, "trustedDevice");

        // Fetch the trusted device by deviceId and tenantId
        $trustedDevice = $this->db->query(
            "SELECT * FROM trustedDevices WHERE deviceId = ? AND tenantId = ?",
            [$deviceId, $this->user->tenantId]
        )->getRow();

        if (!$trustedDevice) {
            return $this->failNotFound('Trusted Device not found');
        }

        // Toggle the status
        $status = $trustedDevice->isApproved == 1 ? 0 : 1;

        // Update the status in the database
        $updateQuery = $this->db->table('trustedDevices')->update(
            [
                'isApproved' => $status,
                'expiresAt' => date('Y-m-d H:i:s', strtotime('+1 year')),
                'approvedBy' => $this->user->userId,
                'approvedAt' => timenow(),
                'lastUsedAt' => timenow(),
                'updatedAt' => timenow()
            ],
            [
                'deviceId' => $deviceId,
                'tenantId' => $this->user->tenantId
            ]
        );

        if (!$updateQuery) {
            return $this->fail('Failed to update Brand status', 500);
        }

        return $this->respond(['status' => true, 'message' => 'Brand status updated successfully']);
    }
}
