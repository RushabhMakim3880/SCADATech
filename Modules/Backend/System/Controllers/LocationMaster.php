<?php

namespace Modules\Backend\System\Controllers;


use App\Controllers\ApiBaseController;
use Modules\Backend\System\Models\locationMasterModel;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;
use App\Libraries\Auth;
use App\Libraries\SelfRefDataLib;

// use OpenApi\Annotations as OA;
class LocationMaster extends ApiBaseController
{
    use ResponseTrait;
    protected $locationMasterModel;

    public function __construct()
    {
        $this->locationMasterModel = new locationMasterModel();
    }

    public function get($locationId)
    {
        $locationId = (int)getKey($locationId, "locationMaster");

        if (!UserPermissionLib::userCanDo("locationMaster", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $locations = $this->locationMasterModel->find($locationId);

        if (!$locations) {
            return $this->failNotFound('Data not found');
        }
        $lib = new SelfRefDataLib(
            "locationMaster",
            "locationName",
            "parentLocationId"
        );

        $locations->parentLocationId = $lib->getSelect2Data($locations->parentLocationId);

        return $this->respond(['status' => true, 'message' => 'Data Saved successfully', 'data' => $locations]);
    }


    public function save($locationId = 0)
    {
        $locationId = (int)getKey($locationId, "locationMaster");

        if ((is_numeric($locationId) and $locationId === 0) or (is_string($locationId) and $locationId == "0")) {
            if (!UserPermissionLib::canAdd("locationMaster")) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            $locationData = $this->locationMasterModel->find($locationId);
            if (UserPermissionLib::canEdit($locationData, "locationMaster")) {
            } else {
                return $this->failForbidden('Insufficient permissions');
            }
        }


        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['locationId'] = $locationId;

        if ($locationId == 0) {

            $rules['locationName'] = [
                'label'  => 'Location Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        } else {

            $rules['locationName'] = [
                'label'  => 'Location  Name',
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

        $successMsg = 'Location Created successfully';
        if ($locationId > 0) {


            $successMsg = 'Location Updated successfully';

            //check if update success
            if (!$this->locationMasterModel->update($locationId, $jsonInput)) {
                return $this->fail('Failed to Create Location', 500);
            }
        } else {

            $locationId = $this->locationMasterModel->insert($jsonInput);

            if (!$locationId) {
                return $this->fail('Failed to create Location', 500);
            }
        }

        $location = $this->locationMasterModel->find($locationId);
        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'locationMaster' => $location]);
    }

    ///unfinished code-- edit ma select2 ma bharayelu aave te pending 6 and validation check krvanu ke type select kriye pachi location ma tenu imigiet parent j select krva de, for exa, type ma taluka 
    // select kryu hoy to location ma india na chale, gujarat na chale, only district ma j add kri sko

    public function getDataTableColumns($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        /*****************************************
        EDIT 1: Define default columns here  
         *****************************************/
        $defaultColumns = [];
        $defaultColumns["LM_locationId"] = ["title" => "Id", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["LM_locationName"] = ["title" => "Location Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["LM_locationType"] = ["title" => "Location Type", "visible" => true, "orderable" => true, "searchable" => true];


        /*****************************************
        EDIT 2: Define required columns filter and type with options here
         *****************************************/

        $defaultColumns["LM_locationType"]["filterType"] = 'select';
        $defaultColumns["LM_locationType"]["filterOptions"] = ['Country', 'State', 'District', 'Taluka', 'Village'];

        /*****************************************
        EDIT 3: Define default columns here  
         ****************************************/
        $configData['defaultOrderColumn'] = 'LM_locationId';
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
        $where = [1]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.

        $dbTable = 'locationMaster LM ';

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
                $action = "<a href='" . base_url("locationMaster/editLocationMaster/" . setKey($row->LM_locationId, "locationMaster")) . "'><i class='fa fa-edit'></i></a>";

                $row->LM_locationId = $row->LM_locationId . " " . $action;
            }

            /*******************************************************
            general data for screen,printing,export will go here.
             *******************************************************/

            $row->LM_locationName = printable($row->LM_locationName);

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

    //get location dropdown
    public function getLocations($locationType = "all")
    {
        // locationName
        $input = $this->getInputData();
        $jsonInput = $input["jsonInput"];
        $search = $jsonInput["data"]["q"];
        $pageNo = $jsonInput["data"]["page"] ?? 1;
        $length = 50;
        $offset = ($pageNo - 1) * $length;

        $locationQuery = "1";
        if ($locationType != 'all') {
            $locationQuery = " locationType = '$locationType' ";
        }

        $lib = new SelfRefDataLib(
            "locationMaster",
            "locationName",
            "parentLocationId",
            $locationQuery
        );

        $searchResult = $lib->search($search, $length, $offset);
        $totalCount = $lib->searchCount($search);
        // $searchResult = $lib->getFullPathById(1752);
        // $searchResult = $lib->getFullPathByIdReverse(1752);
        // $searchResult = $lib->getSelect2Data([1750, 1752]);
        // debug($searchResult);
        // die();

        $response = [
            'status' => true,
            // "message" => "Test dropdown retrieved successfully",
            "data" => [
                "items" => $searchResult,
                "totalCount" => $totalCount
            ]
        ];

        return $this->respond($response, 200);
    }

    public function getCurrencyMasterList()
    {

        $list = $this->db->query("SELECT currencyCode, currencySymbol, currencyName FROM countryMaster WHERE 1 GROUP BY currencyCode")->getResult();

        $result = [];
        foreach ($list as $row) {
            $result[] = [
                'id' => $row->currencyCode,
                'name' => $row->currencyCode . " || " . $row->currencyName . " (" . $row->currencySymbol . ")"
            ];
        }

        $response = [
            'status' => true,
            // "message" => "Currency list retrieved successfully",
            "data" => $result
        ];

        return $this->respond($response, 200);
    }
}
