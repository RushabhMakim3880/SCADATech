<?php

namespace Modules\Backend\Samples\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\Samples\Models\sampleModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;
use App\Libraries\PdfGenerator;
use App\Libraries\Auth;
use App\Libraries\SelfRefDataLib;


// use OpenApi\Annotations as OA;
class newSample extends ApiBaseController
{
    use ResponseTrait;

    protected $sampleModel;

    public function __construct()
    {
        $this->sampleModel = new sampleModel();
    }




    public function save($newSampleId = 0)
    {
        // debug("save");
        // die;

        $newSampleId = (int)getKey($newSampleId, "newSample");

        if ($newSampleId > 0) {
            if (!UserPermissionLib::userCanDo("newSample", 'edit')) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            if (!UserPermissionLib::userCanDo("newSample", 'add')) {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['newSampleId'] = $newSampleId;

        if ($newSampleId == 0) {

            $rules['newSampleName'] = [
                'label'  => 'New Sample Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
            $rules['colorCode'] = [
                'label'  => 'Color Code',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
            $rules['iconCode'] = [
                'label'  => 'Icon Code',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
        } else {

            $rules['newSampleName'] = [
                'label'  => 'New Sample Name',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
            $rules['colorCode'] = [
                'label'  => 'Color Code',
                'rules'  => 'required',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                ]
            ];
            $rules['iconCode'] = [
                'label'  => 'Icon Code',
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

        /******************************
         * Multiline data processing and validation Started.
         ******************************/

        // Normalize single row data into an array format
        $manyRows = $jsonInput['manyRows'] ?? [];
        unset($jsonInput['manyRows']);
        foreach ($manyRows as $k => $row) {
            if (!is_array($row)) {
                $manyRows[$k] = [$row];
            }
        }

        $manyRows = $this->processChildTableData($manyRows, "itemId");

        //remove empty products
        foreach ($manyRows as $k => $row) {
            if ($row['itemId'] == "") {
                unset($manyRows[$k]);
            }
        }

        //if no products, then alert
        if (empty($manyRows)) {
            return $this->fail('Please enter at least one item', 400);
        }

        //check for required fields
        foreach ($manyRows as $row) {
            if (@$row['cityId'] == "" or @$row['cityId'] == 0) {
                return $this->fail('City is required for each product', 400);
            }
        }

        /******************************
         * Multiline data processing and validation completed.
         ******************************/

        if (!empty($jsonInput['sampleDate'])) {
            $jsonInput['sampleDate'] = date("Y-m-d", strtotime(str_replace('/', '-', $jsonInput['sampleDate'])));
        }

        if (!empty($jsonInput['timepicker'])) {
            $jsonInput['timepicker'] = date('H:i:s', strtotime($jsonInput['timepicker']));
        }

        if (!empty($jsonInput['dateTime'])) {
            $jsonInput['dateTime'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $jsonInput['dateTime'])));
        }

        if (!empty($jsonInput['checkboxes']) and is_array($jsonInput['checkboxes'])) {
            $jsonInput['checkboxes'] = implode(",", $jsonInput['checkboxes']);
        }

        if (!empty($jsonInput['simpleDropdownMultiple']) and is_array($jsonInput['simpleDropdownMultiple'])) {
            $jsonInput['simpleDropdownMultiple'] = implode(",", $jsonInput['simpleDropdownMultiple']);
        }

        // Hash the password

        $successMsg = 'Data Saved successfully';
        if ($newSampleId > 0) {
            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;
            // debug($jsonInput);die;

            $successMsg = 'Data Updated successfully';

            //check if update success
            if (!$this->sampleModel->update($newSampleId, $jsonInput)) {
                return $this->fail('Failed to update user', 500);
            }
        } else {

            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;

            $newSample = $this->sampleModel->insert($jsonInput);
            $successMsg = 'Data Inserted successfully';

            $newSampleId = $this->sampleModel->getInsertID();

            if (!$newSample) {
                return $this->fail('Failed to data saving', 500);
            }
        }

        // save $manyRows
        if (!empty($manyRows)) {
            $this->syncChildTable("sampleNewDetails", "sampleNewDetailId", "newSampleId", $newSampleId, $manyRows);
        }

        //profile pic code start here...
        $pic1 = $jsonInput['profile_pic'];

        if (!is_null($pic1)) {
            $picPath = ROOTPATH . "public/uploads/sample/photo/" . md5($newSampleId) . ".png";
            if ($pic1 == "") {
                // Remove existing image
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            } elseif ($pic1 == "nochange") {
                // Do nothing
            } else {

                $picDir = ROOTPATH . "public/uploads/sample/photo/";
                //create directory recursively if does not exist.
                if (!is_dir($picDir)) {
                    mkdir($picDir, 0777, true);
                }
                base64ToImage($pic1, $picPath);
            }
        }
        //profile pic code end here..

        $sample = $this->sampleModel->find($newSampleId);

        $nextPopup = [
            "endpoint" => "api/newSample/infoPopupExample/0",
            "title" => "Edit Sample " . rand(1, 100),
            "size" => "lg",
            "modaltype" => "self",
            "stricttype" => "strict"
        ];


        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'sample' => $sample, 'nextPopup' => $nextPopup]);
    }

    public function get($newSampleId)
    {

        $newSampleId = (int)getKey($newSampleId, "newSample");

        if (!UserPermissionLib::userCanDo("newSample", 'view')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $Sample = $this->sampleModel->find($newSampleId);

        if (!$Sample) {
            return $this->failNotFound('Samples not found');
        }

        $lib = new SelfRefDataLib(
            "locationMaster",
            "locationName",
            "parentLocationId"
        );

        $Sample->locationId = $lib->getSelect2Data($Sample->locationId);
        $Sample->simpleDropdownMultiple = $lib->getSelect2Data($Sample->simpleDropdownMultiple);

        $Sample->checkboxes = explode(",", $Sample->checkboxes);

        //set profile_pic url
        if (file_exists("uploads/sample/photo/" . md5($newSampleId) . ".png")) {
            $Sample->profile_pic = base_url("uploads/sample/photo/" . md5($newSampleId) . ".png");
        } else {
            $Sample->profile_pic = userProfilePicUrl(0);
        }

        //static data for itemid field
        $staticItems = [
            1 => "Product1",
            2 => "Product2",
            3 => "Product3",
            4 => "Product4",
            5 => "Product5"
        ];

        // Process manyRows data
        $Sample->manyRows = $this->getChildTableData("sampleNewDetails", "newSampleId", $newSampleId);

        // process item for ajax based dropdown
        foreach ($Sample->manyRows as $k => $row) {
            $Sample->manyRows[$k]['districtId'] = $lib->getSelect2Data($row['districtId']);

            if (!empty($row['itemId']) && isset($staticItems[$row['itemId']])) {
                // Fix: Correctly update the manyRows array
                $Sample->manyRows[$k]['itemId'] = [
                    "id" => $row['itemId'],
                    "text" => $staticItems[$row['itemId']]
                ];
            }
        }


        return $this->respond(['status' => true, 'message' => 'Samples retrieved successfully', 'data' => $Sample]);
    }

    public function changeStatus($sampleId)
    {
        $sampleId = (int)getKey($sampleId, "newSample");

        //reset login lockout and failed login attempts
        $this->db->query("UPDATE newSampleTable SET isActive = !isActive WHERE newSampleId = ?", [$sampleId]);

        $response = [
            'status' => true,
            "message" => "Done.",
        ];

        return $this->respond($response, 200);
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
        $defaultColumns["NS_newSampleId"] = ["title" => "", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["NS_sampleDate"] = ["title" => "Date", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_newSampleName"] = ["title" => "Name", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_price"] = ["title" => "Price", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["LM_locationName"] = ["title" => "Location", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_colorCode"] = ["title" => "Color Code", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_iconCode"] = ["title" => "Icon Code", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_isActive"] = ["title" => "Active", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_category"] = ["title" => "Category", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["NS_priority"] = ["title" => "Priority", "visible" => true, "orderable" => true, "searchable" => false];



        /*****************************************
            EDIT 2: Define required columns filter and type with options here
         *****************************************/
        $isActiveFilter = ["1" => "Active", "0" => "In Active"];
        $defaultColumns["NS_isActive"]["filterType"] = 'select';
        $defaultColumns["NS_isActive"]["filterOptions"] = $isActiveFilter;

        $defaultColumns["NS_sampleDate"]["filterType"] = 'date';
        $defaultColumns["NS_sampleDate"]["filterOptions"] = dateFilterOptions("past");

        $priorityFilter = ["warm" => "Warm", "hot" => "Hot", "cold" => "Cold"];
        $defaultColumns["NS_priority"]["filterType"] = 'checkbox';
        $defaultColumns["NS_priority"]["filterOptions"] = $priorityFilter;


        /*****************************************
            EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'NS_newSampleId';
        $configData['defaultOrderDirection'] = 'asc';
        $configData['titleColumn'] = 'NS_newSampleName';


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
        $select = []; // Add the fields you need compulsary irrespective of the user settings, or leave empty []
        $where = ["NS.isDeleted = '0'"]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        // $dbTable = 'newSampleTable NS ';

        $dbTable = 'newSampleTable NS
                    LEFT JOIN locationMaster LM ON LM.locationId = NS.locationId
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
        $columnTotalsData['NS_price'] = 12345; //you can run mysql query here to fetch total of the column

        $config = config('AppConfig');

        foreach ($data as &$row) {

            if (!$isDownload) {

                $priorityFilter = ["hot" => "Hot", "cold" => "Cold", "warm" => "Warm"];
                $dropdown = switchCategoryDropdown($row->NS_newSampleId, $row->NS_priority, $priorityFilter);
                $class = "";
                if ($row->NS_priority == 'hot') {
                    $class = "btn-danger";
                } else if ($row->NS_priority == 'warm') {
                    $class = "btn-warning";
                } else {
                    $class = "btn-info";
                }
                $priorityDropdown = '<div title="Click to open for more options"  class="dropdown d-inline-block">
                      <a class="btn btn-xs ' . $class . ' dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . printable($row->NS_priority) . '</a>
                      ' . $dropdown . '
                  </div>';

                $row->NS_priority = $priorityDropdown;

                /*******************************************************
                    NOTE: for html and data made for screen only, keep inside this if condition.
                 *******************************************************/
                //dropdown code start here...
                $primaryKey = $row->NS_newSampleId;

                $dropdownItems = [];
                if (UserPermissionLib::userCanDo("newSample", 'edit')) {
                    $dropdownItems[] = ['label' => 'Edit', 'class' => 'text-warning', 'icon' => 'fa fa-edit', 'href' => base_url("samples/editSampleNew/" . setKey($primaryKey, "newSample"))];
                }

                if (UserPermissionLib::userCanDo("newSample", 'edit')) {
                    $dropdownItems[] = ['label' => 'Edit', 'href' => 'javascript:;', 'class' => 'text-warning apiPopup', 'icon' => 'fa fa-edit', 'attributes' => "data-size='lg' data-endpoint='" . ("samples/addSampleNewAjax/" . setKey($primaryKey, "newSample")) . "'"];
                }

                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'containerClass' => '',
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->NS_newSampleId = $dropdownHtml;
                //dropdown code end here...
            }

            /*******************************************************
                general data for screen,printing,export will go here.
             *******************************************************/
            if ($row->NS_isActive == 1) {
                $row->NS_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/newSample/changeStatus/" . setKey($primaryKey, "newSample") . "'>Active</span>";
            } else {
                $row->NS_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/newSample/changeStatus/" . setKey($primaryKey, "newSample") . "'>InActive</span>";
            }

            // $row->SM_isActive = $row->SM_isActive == 1 ? "Active" : "Inactive";

            // $row->UM_passwordExpiryTime = time_diff(time(), $row->UM_passwordExpiryTime);
            // $row->UM_lastLoginTime = humanTimeDifference($row->UM_lastLoginTime) . " ago";
            // $row->UM_lastActiveTime = humanTimeDifference($row->UM_lastActiveTime) . " ago";
            $row->NS_sampleDate = myDateFormat($row->NS_sampleDate);

            // if ($row->NS_priority == 'hot') {
            //     $row->NS_priority = "<img src='" . base_url('Modules/Samples/img/hotMeter.png') . "' class='priority-img' width='20px' />";
            // } elseif ($row->NS_priority == 'warm') {
            //     $row->NS_priority = "<img src='" . base_url('Modules/Samples/img/warmMeter.png') . "' class='priority-img' width='20px' />";
            // } else {
            //     $row->NS_priority = "<img src='" . base_url('Modules/Samples/img/coldMeter.png') . "' class='priority-img' width='20px' />";
            // }
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

        $totalRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable WHERE NS.newSampleId > 0")->getRow()->total;
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

    public function switchPriority($itemId, $priority)
    {
        $itemId = (int)getKey($itemId, "newSample");

        $this->db->table("newSampleTable")->where("newSampleId", $itemId)->update(["priority" => $priority]);

        $response = [
            'status' => true,
            "message" => "Priority changed successfully",
        ];

        return $this->respond($response, 200);
    }

    public function loadSimpleDropdown()
    {

        $states = $this->db->query("SELECT locationId as id, locationName as text FROM locationMaster WHERE locationType = 'state'  ORDER BY locationName")->getResult();

        $response = [
            'status' => true,
            "message" => "Test dropdown retrieved successfully",
        ];

        foreach ($states as $state) {
            $response["data"][] = [
                "id" => $state->id,
                "name" => $state->text,
                "attributes" => [
                    "price" => rand(100, 1000),
                    "category" => "Category " . rand(1, 10),
                    "description" => "Description " . rand(1, 10),
                ],
            ];
        }

        return $this->respond($response, 200);
    }

    public function loadSimpleDropdownMultiple()
    {
        $districs = $this->db->query("SELECT locationId as id, locationName as text FROM locationMaster WHERE locationType = 'district' AND parentLocationId=1506 ORDER BY locationName")->getResult();

        $response = [
            'status' => true,
            "message" => "Test dropdown retrieved successfully",
        ];

        foreach ($districs as $distric) {
            $response["data"][] = [
                "id" => $distric->id,
                "name" => $distric->text
            ];
        }

        // sleep(3);

        return $this->respond($response, 200);
    }

    public function generatePdf($download = false)
    {
        $data = [];
        $data["pageTitle"] = "Sample New11";

        $data["view"] = '\Modules\Backend\Samples\Views\pdfSample';

        $htmlContent = view('bsViewLoader', $data);
        $fileName = "SampleDocument.pdf";

        $prefixPdf = []; //array of full path of files to be added at the start of pdf
        $suffixPdf = []; //array of full path of files to be added at the end of pdf
        $letterheadPdf = WRITEPATH . "sample.pdf"; //full path of letterhead pdf file OR null
        $letterheadPdf = null;

        if ($download) {
            $pdfGenerator = new PdfGenerator();
            $pdfBinary = $pdfGenerator->generatePdf($htmlContent, $letterheadPdf, $prefixPdf, $suffixPdf, false);

            // check for error
            if (isset($pdfBinary['error'])) {
                return $this->fail([
                    'message' => 'Failed to generate PDF',
                    'details' => $pdfBinary['details'] ?? 'Unknown error'
                ], 500);
            }
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->setBody($pdfBinary);
        }


        $response = [
            'status' => true,
            "message" => "Pdf view retrived successfully",
            "data" => $htmlContent,
        ];

        return $this->respond($response, 200);
    }

    public function infoPopupExample($itemId = 0)
    {
        $itemId = getKey($itemId, "newSample");


        $data = [];
        $view = '\Modules\Backend\Samples\Views\infoPopupExample';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "Popup view retrived successfully",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function infoFormExample($itemId = 0)
    {
        $itemId = getKey($itemId, "newSample");

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        // Handle form submition
        if (!empty($jsonInput)) {
            // validate form data and do necessary processing here


            // return error
            // $response = [
            //     'status' => false,
            //     "errors" => ["error1", 'error2'],
            // ];

            // return $this->respond($response, 200);

            // return success
            $response = [
                'status' => true,
                "message" => "Data saved successfully",
                "closeTimeout" => 1, // timeout to autoclose popupbox, keep 0 to disable
                "clearForm" => true, // clear form after success
                "reloadDataTable" => true, // reload datatable after success
                "callbackFunction" => "testCallbackForPopupResponse", // call a js function after success
                "callbackData" => ["data1", "data2"], // data to be passed to callback function
                // "redirectUrl" => base_url(), // redirect to a url after success
            ];

            return $this->respond($response, 200);
        }


        $data = [];
        $view = '\Modules\Backend\Samples\Views\infoFormExample';

        $finalHtml = view($view, $data);

        $response = [
            'status' => true,
            "message" => "Popup view retrived successfully",
            "data" => $finalHtml,
        ];

        return $this->respond($response, 200);
    }

    public function getAjaxItem()
    {
        if (!Auth::check()) {
            return $this->failUnauthorized('Unauthorized');
        }

        $input = $this->getInputData();
        $jsonInput = $input["jsonInput"];
        $search = $jsonInput["data"]["q"];
        $pageNo = $jsonInput["data"]["page"] ?? 1;
        $length = 5;
        $offset = ($pageNo - 1) * $length;


        // Static Data
        $staticItems = [
            ["id" => 1, "text" => "Product1"],
            ["id" => 2, "text" => "Product2"],
            ["id" => 3, "text" => "Product3"],
            ["id" => 4, "text" => "Product4"],
            ["id" => 5, "text" => "Product5"]
        ];

        // Apply search filter (if applicable)
        if (!empty($search)) {
            $staticItems = array_filter($staticItems, function ($item) use ($search) {
                return stripos($item["text"], $search) !== false;
            });
        }

        // Paginate results
        $totalCount = count($staticItems);
        $staticItems = array_slice($staticItems, $offset, $length);
        $response = [
            'status' => true,
            "message" => "Static item data retrieved successfully",
            "data" => [
                "items" => array_values($staticItems),
                "totalCount" => $totalCount
            ]
        ];


        return $this->respond($response, 200);
    }
}
