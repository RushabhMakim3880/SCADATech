<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\System\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;
use DateTime;
use App\Libraries\Auth;
use PhpParser\Node\Expr\FuncCall;

// use OpenApi\Annotations as OA;
class Users extends ApiBaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->userModel = new UserModel();
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
    public function getList()
    {
        if (!UserPermissionLib::userCanDo("userMaster", 'viewAll')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $query = "SELECT UM.*, UG.groupName 
                    FROM userMaster UM 
                    LEFT JOIN userGroups UG ON UM.groupId = UG.groupId
                    WHERE UM.userId != 1
                    ORDER BY UM.userId ASC";

        $users = $this->db->query($query)->getResult();

        $data = [];
        // Remove passwords from the response
        foreach ($users as $user) {

            $action = "<a href='" . base_url("users/editUser/" . setKey($user->userId, "userMaster")) . "'><i class='fa fa-edit'></i></a>";

            $data[] = [
                $user->userId,
                $user->username,
                $user->firstName . " " . $user->lastName,
                $user->email,
                $user->mobile,
                $user->groupName,
                $user->createdAt,
                $action
            ];
        }

        $response = [
            'status' => true,
            "message" => "User retrieved successfully",
            "data" => [
                "usersTable" => $data,
            ]
        ];

        return $this->respond($response, 200);
    }


    public function save($userId = 0)
    {

        $userId = (int)getKey($userId, "userMaster");
        if ((is_numeric($userId) and $userId === 0) or (is_string($userId) and $userId == "0")) {
            if (!UserPermissionLib::canAdd("userMaster")) {
                return $this->failForbidden('Insufficient permissions');
            }
        } else {
            $userData = $this->userModel->find($userId);

            // Allow if logged-in user is editing their own record
            if ($this->user->userId == $userId || UserPermissionLib::canEdit($userData, "userMaster")) {

                // User can edit their own profile or has edit permission
            } else {
                return $this->failForbidden('Insufficient permissions');
            }
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];
        $uploadedFiles = $input['uploadedFiles'];

        if (!empty($uploadedFiles)) {
            //handel file upload here.
            foreach ($uploadedFiles as $fieldName => $file) {
                if (is_array($file)) {
                    // Multiple files uploaded for the same field
                    foreach ($file as $singleFile) {
                        $this->uploadFile($singleFile, WRITEPATH . 'uploads', 'test', true, true);
                    }
                } else {
                    // Single file uploaded
                    $this->uploadFile($file, WRITEPATH . 'uploads', 'test.pdf', true, false);
                }
            }
        }


        // Validate input (similar to Auth::register)
        $validation = \Config\Services::validation();

        $rules = [];

        $jsonInput['userId'] = $userId;
        $jsonInput['password'] = $jsonInput['password999'] ?? "";
        unset($jsonInput['password999']);


        // ✅ Don't allow user to change their own group or active status
        if ($userId > 0 && $this->user->userId == $userId) {
            unset($jsonInput['groupId']);
            unset($jsonInput['isActive']);
        }

        if ($userId == 0) {

            $rules['username'] = [
                'label'  => 'Username',
                'rules'  => 'required|min_length[3]|max_length[20]|is_unique[userMaster.username]',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                    'min_length' => 'The {field} must be at least 3 characters long.',
                    'max_length' => 'The {field} cannot be more than 20 characters long.'
                ]
            ];

            // $rules['email'] = [
            //     'label'  => 'Email Address',
            //     'rules'  => 'required|valid_email|is_unique[userMaster.email]',
            //     'errors' => [
            //         'required'    => 'We need your {field} to contact you.',
            //         'valid_email' => 'The {field} provided is not valid.'
            //     ]
            // ];

            $rules['password'] = [
                'label'  => 'Password',
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                    'min_length' => 'The {field} must be at least 6 characters long.'
                ]
            ];

            $rules['groupId'] = [
                'label'  => 'Group',
                'rules'  => 'required|integer|is_natural_no_zero',
                'errors' => [
                    'required' => 'The {field} cannot be empty.',
                    'integer'  => 'The {field} must be an integer.'
                ]
            ];
        } else {

            $rules['userId'] = [
                'label'  => 'User ID',
                'rules'  => 'permit_empty|integer|is_natural',
                'errors' => [
                    'integer' => 'The {field} must be an integer.'
                ]
            ];

            $rules['username'] = [
                'label'  => 'Username',
                'rules'  => 'required|min_length[3]|max_length[20]|is_unique[userMaster.username,userId,{userId}]',
                'errors' => [
                    'required'   => 'The {field} cannot be empty.',
                    'min_length' => 'The {field} must be at least 3 characters long.',
                    'max_length' => 'The {field} cannot be more than 20 characters long.'
                ]
            ];

            // $rules['email'] = [
            //     'label'  => 'Email Address',
            //     'rules'  => 'required|valid_email|is_unique[userMaster.email,userId,{userId}]',
            //     'errors' => [
            //         'required'    => 'We need your {field} to contact you.',
            //         'valid_email' => 'The {field} provided is not valid.'
            //     ]
            // ];

            if (isset($jsonInput['password']) and $jsonInput['password'] != "") {
                $rules['password'] = [
                    'label'  => 'Password',
                    'rules'  => 'min_length[6]',
                    'errors' => [
                        'min_length' => 'The {field} must be at least 6 characters long.'
                    ]
                ];
            }
        }

        if (!empty($jsonInput['groupId']) and isset($jsonInput['groupId'])) {
            $rules['groupId'] = [
                'label'  => 'Group',
                'rules'  => 'required|integer|is_natural_no_zero',
                'errors' => [
                    'required' => 'The {field} cannot be empty.',
                    'integer'  => 'The {field} must be an integer.'
                ]
            ];
        }

        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {

            return $this->fail($validation->getErrors(), 400);
        }

        // Hash the password
        if (isset($jsonInput['password']))
            $jsonInput['password'] = password_hash($jsonInput['password'], PASSWORD_DEFAULT);

        // Insert user
        $successMsg = 'User created successfully';
        if ($userId > 0) {

            $jsonInput['updatedAt'] = timenow();
            $jsonInput['updatedBy'] = $this->user->userId;

            $successMsg = 'User updated successfully';

            //check if update success
            if (!$this->userModel->update($userId, $jsonInput)) {
                return $this->fail('Failed to update user', 500);
            }

            // if password is changed, reset the lockout and failed attempts and clear refreshTokens
            if (isset($jsonInput['password']) && $jsonInput['password'] != "") {
                $this->db->query("UPDATE userMaster SET lockoutUntil = NULL, failedAttempts = 0 WHERE userId = ?", [$userId]);
                $this->db->query("DELETE FROM refreshTokens WHERE userId = ?", [$userId]);
            }
        } else {
            $jsonInput['tenantId'] = $this->user->tenantId;
            $jsonInput['createdAt'] = timenow();
            $jsonInput['createdBy'] = $this->user->userId;

            // passwordExpiryTime
            $config = config('AppConfig');
            $passwordExpiryDays = $config->passwordExpiryDays;
            $jsonInput['passwordExpiryTime'] = date("Y-m-d H:i:s", strtotime("+$passwordExpiryDays days"));

            $userId = $this->userModel->insert($jsonInput);

            if (!$userId) {
                return $this->fail('Failed to create user', 500);
            }

            assignSerialNumber($this->user->tenantId, "userMaster", "userId", $userId);
        }

        // handle file uploads here.
        if (!empty($uploadedFiles)) {
            //handel file upload here.
            foreach ($uploadedFiles as $fieldName => $file) {
                if (is_array($file)) {
                    // Multiple files uploaded for the same field
                    foreach ($file as $singleFile) {
                        $this->uploadFile($singleFile, WRITEPATH . 'uploads', 'test', true, true);
                    }
                } else {
                    // Single file uploaded
                    $this->uploadFile($file, WRITEPATH . 'uploads', 'test', true, true);
                }
            }
        }

        //profile pic code start here...
        // $pic1 = $jsonInput['profile_pic'];

        // if (!is_null($pic1)) {
        //     $picPath = ROOTPATH . "public/uploads/users/photo/" . md5($userId) . ".png";
        //     if ($pic1 == "") {
        //         // Remove existing image
        //         if (file_exists($picPath)) {
        //             unlink($picPath);
        //         }
        //     } elseif ($pic1 == "nochange") {
        //         // Do nothing
        //     } else {

        //         $picDir = ROOTPATH . "public/uploads/users/photo/";
        //         //create directory recursively if does not exist.
        //         if (!is_dir($picDir)) {
        //             mkdir($picDir, 0777, true);
        //         }
        //         base64ToImage($pic1, $picPath);
        //     }
        // }
        //profile pic code end here..

        // Fetch the created user
        $user = $this->userModel->find($userId);
        // Remove password from response
        unset($user->password);
        return $this->respondCreated(['status' => true, 'message' => $successMsg, 'user' => $user]);
    }

    public function get($userId)
    {
        $userId = (int)getKey($userId, "userMaster");

        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $user->uid = setKey($user->userId, "userMaster");
        // Remove password from response
        unset($user->password);

        // $user->profile_pic = userProfilePicUrl($userId);

        return $this->respond(['status' => true, 'message' => 'User retrieved successfully', 'data' => $user]);
    }

    public function groups()
    {

        if ($this->user->group->isAdmin) {
            $groups = $this->db->query("SELECT groupId as id, groupName as `name` FROM userGroups WHERE tenantId = " . $this->user->tenantId)->getResult();
        } else {
            $groups = $this->db->query("SELECT groupId as id, groupName as `name` FROM userGroups WHERE isAdmin=0 AND tenantId = " . $this->user->tenantId)->getResult();
        }



        //set test attributes
        foreach ($groups as &$g) {
            $g->attributes = [
                "price" => rand(100, 1000),
                "category" => "Category " . rand(1, 10)
            ];
        }

        $response = [
            'status' => true,
            "message" => "",
            "data" => $groups
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
        $defaultColumns["UM_userId"] = ["title" => "Sr.", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["UM_username"] = ["title" => "Username", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["UM_firstName"] = ["title" => "First Name", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["UM_lastName"] = ["title" => "Last Name", "visible" => true, "orderable" => true, "searchable" => true];
        // $defaultColumns["UM_email"] = ["title" => "Email", "visible" => true, "orderable" => true, "searchable" => true];
        // $defaultColumns["UM_mobile"] = ["title" => "Mobile", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["UM_isActive"] = ["title" => "Status", "visible" => true, "orderable" => true, "searchable" => true, "visibleControl" => false];
        $defaultColumns["UG_groupName"] = ["title" => "Group", "visible" => true, "orderable" => true, "searchable" => true];
        $defaultColumns["UM_lockoutUntil"] = ["title" => "Lockout", "visible" => true, "orderable" => true, "searchable" => false, "visibleControl" => false];
        $defaultColumns["UM_failedAttempts"] = ["title" => "Failed Attempts", "visible" => true, "orderable" => true, "searchable" => false];
        // $defaultColumns["UM_passwordExpiryTime"] = ["title" => "Password Expiry", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["UM_lastLoginTime"] = ["title" => "Last Login", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["UM_lastActiveTime"] = ["title" => "Last Active", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["UM_createdAt"] = ["title" => "Created At", "visible" => true, "orderable" => true, "searchable" => false];
        $defaultColumns["UM_createdBy"] = ["title" => "Created By", "visible" => true, "orderable" => true, "searchable" => true, "visibleControl" => false];


        /*****************************************
            EDIT 2: Define required columns filter and type with options here
         *****************************************/
        $temp = $this->db->query("SELECT GROUP_CONCAT(groupName SEPARATOR '__') as `filterData` FROM userGroups")->getRow()->filterData;
        $groupFilters = explode("__", $temp);

        $defaultColumns["UG_groupName"]["filterType"] = 'select';
        $defaultColumns["UG_groupName"]["filterOptions"] = $groupFilters;

        $isActiveFilter = ["1" => "Active", "0" => "Inactive"];
        $defaultColumns["UM_isActive"]["filterType"] = 'select';
        $defaultColumns["UM_isActive"]["filterOptions"] = $isActiveFilter;

        $lockOutFilter = ["1" => "Locked", "0" => "Unlocked"];
        $defaultColumns["UM_lockoutUntil"]["filterType"] = 'custom';
        $defaultColumns["UM_lockoutUntil"]["filterOptions"] = $lockOutFilter;

        $defaultColumns["UM_failedAttempts"]["filterType"] = 'numberRange';
        $defaultColumns["UM_failedAttempts"]["filterOptions"] = ["0-5" => "0 to 5", "6-10" => "6 to 10", "11-15" => "11 to 15", "16-20" => "16 to 20", "21-25" => "21 to 25", "26-30" => "26 to 30", "31-35" => "31 to 35", "36-40" => "36 to 40", "41-45" => "41 to 45", "46-50" => "46 to 50"];

        // $defaultColumns["UM_passwordExpiryTime"]["filterType"] = 'date';
        // $defaultColumns["UM_passwordExpiryTime"]["filterOptions"] = dateFilterOptions("all");

        $defaultColumns["UM_lastLoginTime"]["filterType"] = 'date';
        $defaultColumns["UM_lastLoginTime"]["filterOptions"] = dateFilterOptions("past");

        $defaultColumns["UM_lastActiveTime"]["filterType"] = 'date';
        $defaultColumns["UM_lastActiveTime"]["filterOptions"] = dateFilterOptions("past");

        $defaultColumns["UM_createdAt"]["filterType"] = 'date';
        $defaultColumns["UM_createdAt"]["filterOptions"] = dateFilterOptions("past");

        /*****************************************
            EDIT 3: Define default columns here  
         *****************************************/
        $configData['defaultOrderColumn'] = 'UM_userId';
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
        $select = ["UM.serialNo as UM_serialNo", "UM.userId as UM_userId", "UM.2FaToken as UM_2FaToken"]; // Add the fields you need compulsary irrespective of the user settings, or leave empty []
        $where = ["UM.groupId IS NOT NULL", "UM.tenantId = " . $this->user->tenantId, "UM.serialNo > 0"]; // Add the where clause you need compulsary irrespective of the user settings, or leave [1]

        // prepare db join table part.
        $dbTable = 'userMaster UM LEFT JOIN 
                    userGroups UG ON UM.groupId = UG.groupId';

        /************************************************
         * User permission code start here
         ************************************************/
        if (UserPermissionLib::userCanDo("userMaster", 'viewOwn') && !UserPermissionLib::userCanDo("userMaster", 'viewAll')) {
            $where[] = "UM.createdBy = " . $this->user->userId;
        } else if (!UserPermissionLib::userCanDo("userMaster", 'viewAll')) {
            $where[] = "0 = 1";
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

            $primaryKey = $row->UM_userId;
            $lockoutUntil = $row->UM_lockoutUntil;
            $twoFaToken = $row->UM_2FaToken;
            if (!$isDownload) {
                /*******************************************************
                    NOTE: for html and data made for screen only, keep inside this if condition.
                 *******************************************************/
                //dropdown code start here...
                $dropdownItems = [];
                //edit button
                if (UserPermissionLib::canEdit($row, "userMaster")) {
                    $dropdownItems[] = ['label' => 'Edit', 'class' => 'text-warning', 'icon' => 'fa fa-edit', 'href' => base_url("users/editUser/" . setKey($primaryKey, "userMaster"))];
                }
                //single sign on
                // $dropdownItems[] = ['label' => 'Single Signon', 'href' => 'javascript:;', 'class' => 'text-primary apiAction', 'icon' => 'fa fa-key', 'attributes' => "data-endpoint='" . ("api/users/singleSignonToken/" . setKey($primaryKey, "userMaster")) . "'"];

                //lockuntil
                if (!is_null($lockoutUntil) && strtotime($lockoutUntil) > time()) {
                    $dropdownItems[] = ['label' => 'Lock Until', 'href' => 'javascript:;', 'class' => 'text-primary apiAction', 'icon' => 'fa fa-lock', 'attributes' => "data-endpoint='" . ("api/users/resetLock/" . setKey($primaryKey, "userMaster")) . "'"];
                }

                //2FaToken
                if (!is_null($twoFaToken)) {
                    // $dropdownItems[] = ['label' => '2FaToken', 'href' => 'javascript:;', 'class' => 'text-primary apiAction', 'icon' => 'fa fa-shield-alt', 'attributes' => "data-endpoint='" . ("api/users/twoFaToken/" . setKey($primaryKey, "userMaster")) . "'"];
                }

                //reset password token
                if (!is_null($lockoutUntil) && strtotime($lockoutUntil) > time()) {
                    $dropdownItems[] = ['label' => 'Reset Password Token', 'href' => 'javascript:;', 'class' => 'text-primary apiAction', 'icon' => 'fa fa-redo-alt', 'attributes' => "data-endpoint='" . ("api/users/resetLock/" . setKey($primaryKey, "userMaster")) . "'"];
                }


                $dropdown = [
                    'id' => 'actionDropdown_' . $primaryKey,
                    'toggleClass' => 'd-inline-flex btn-info align-items-center gap-1',
                    'toggleLabel' => manageScreenId($primaryKey, $row->UM_serialNo),
                    'toggleAttributes' => '',
                    'menuClass' => 'dropdown-menu-start manageScreenActionDropdown',
                    'menuAttributes' => '',
                    'items' => $dropdownItems,
                ];

                $dropdownHtml = view('templates/' . $config->theme . '/components/dropdown', ['dropdown' => $dropdown]);
                $row->UM_userId = $dropdownHtml;
                //dropdown code end here...

                // $profile = "<img src='" . userProfilePicUrl($primaryKey) . "' class='' style='height:50px;' /> ";

                $username = $row->UM_username;
                // $row->UM_username = $profile  . " " .  $row->UM_username;

                //lockout
                if (!is_null($row->UM_lockoutUntil) && strtotime($row->UM_lockoutUntil) > time()) {
                    $row->UM_lockoutUntil = "<span title='Click to reset' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/users/resetLock/" . setKey($primaryKey, "userMaster") . "'>Locked</span>";
                } else {
                    $row->UM_lockoutUntil = "<span class='badge bg-success'>Unlocked</span>";
                }

                $row->UM_firstName = "<span data-rowbgcolor='#ffffff'>$row->UM_firstName</span>";

                //status
                if ($row->UM_isActive == 1) {
                    $row->UM_isActive = "<span title='Click to Change Status' class='badge bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/users/changeUserStatus/" . setKey($primaryKey, "userMaster") . "'>Active</span>";
                } else {
                    $row->UM_isActive = "<span title='Click to Change Status' class='badge bg-danger cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/users/changeUserStatus/" . setKey($primaryKey, "userMaster") . "'>InActive</span>";
                }

                // $mobileActionsBtns = "";
                // if ($row->UM_mobile != "" && $row->UM_mobile != "0") {
                //     $mobileActionsBtns .= '<a href="tel:' . $row->UM_mobile . '" class="btn btn-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                //         <i class="fas fa-lg fa-phone"></i>
                //     </a>';
                // }
                // if ($row->UM_mobile != "" && $row->UM_mobile != "0") {
                //     $mobileActionsBtns .= '<br><a href="https://wa.me/' . $row->UM_mobile . '" class="btn btn-success mt-2 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #25D366; border: none;">
                //         <i class="fab fa-lg fa-whatsapp"></i>
                //     </a>';
                // }

                $mobileView[$k] = [
                    "titleBox1" => "$row->UM_firstName $row->UM_lastName",
                    // "descriptionBox1" => "$row->UM_email $row->UM_mobile",
                    "titleBox2" => "",
                    "descriptionBox2" => "$username",
                    // "actionBox" => $mobileActionsBtns,
                    "statusBox" => "$row->UM_isActive",
                    "dateBox" => "<span class='badge bg-secondary'>" . humanTimeDifference($row->UM_lastActiveTime) . "</span>",
                ];

                // $row->UM_isActive = $row->UM_isActive == 1 ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-danger'>Inactive</span>";
            } else {
                /*******************************************************
                specific data for printing,export will go here.
                 *******************************************************/
                if (!is_null($row->UM_lockoutUntil) and strtotime($row->UM_lockoutUntil) > time()) {
                    $row->UM_lockoutUntil = "Locked";
                } else {
                    $row->UM_lockoutUntil = "Unlocked";
                }

                //status
                if ($row->UM_isActive == 1) {
                    $row->UM_isActive = "Active";
                } else {
                    $row->UM_isActive = "InActive";
                }
            }

            /*******************************************************
                general data for screen,printing,export will go here.
             *******************************************************/


            // $row->UM_isActive = $row->UM_isActive == 1 ? "Active" : "Inactive";

            // $row->UM_passwordExpiryTime = time_diff(time(), $row->UM_passwordExpiryTime);
            $row->UM_lastLoginTime = $row->UM_lastLoginTime ? humanTimeDifference($row->UM_lastLoginTime) . " ago" : "Never";
            $row->UM_lastActiveTime = $row->UM_lastActiveTime ? humanTimeDifference($row->UM_lastActiveTime) . " ago" : "Never";
            $row->UM_createdAt = myDateTimeFormat($row->UM_createdAt);
            $row->UM_createdBy = username($row->UM_createdBy);




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

        $totalRecords = $this->db->query("SELECT COUNT(*) as total FROM $dbTable WHERE UM.groupId > 0 AND UM.serialNo > 0 AND UM.tenantId = " . $this->user->tenantId)->getRow()->total;
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

    public function saveUserSettings($module = "")
    {
        if ($module == "") {
            return $this->fail('Module name is required', 400);
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $userId = $this->user->userId;

        $ex = $this->db->query("SELECT userSettingsId FROM userSettings WHERE userId = $userId AND tenantId='" . $this->user->tenantId . "' AND `key` = '$module'")->getRow();

        if ($ex) {
            $this->db->query("UPDATE userSettings SET `value` = '" . json_encode($jsonInput) . "' WHERE userSettingsId = $ex->userSettingsId");
        } else {
            $this->db->query("INSERT INTO userSettings (tenantId, userId, `key`, `value`) VALUES (" . $this->user->tenantId . ",$userId, '$module', '" . json_encode($jsonInput) . "')");
        }

        $response = [
            'status' => true,
            "message" => "Settings saved successfully",
        ];

        return $this->respond($response, 200);
    }

    public function resetLock($userId)
    {
        $userId = (int)getKey($userId, "userMaster");

        //reset login lockout and failed login attempts
        $this->db->query("UPDATE userMaster SET lockoutUntil = NULL, failedAttempts = 0 WHERE userId = ?", [$userId]);

        $response = [
            'status' => true,
            "message" => "Login Lockout Reset Done.",
        ];

        return $this->respond($response, 200);
    }

    public function testDropDown()
    {
        $response = [
            'status' => true,
            "message" => "Test dropdown retrieved successfully",
            "data" => [
                "items" => [
                    ["id" => 1, "text" => "Item 1", "price" => 100, "category" => "Category 1"],
                    ["id" => 2, "text" => "Item 2", "price" => 200, "category" => "Category 2"],
                    ["id" => 3, "text" => "Item 3", "price" => 300, "category" => "Category 3"],
                    ["id" => 4, "text" => "Item 4", "price" => 400, "category" => "Category 4"],
                    ["id" => 5, "text" => "Item 5", "price" => 500, "category" => "Category 5"],
                ],
                "totalCount" => 15
            ]
        ];

        return $this->respond($response, 200);
    }

    //singleSignonToken
    public function singleSignonToken($userId)
    {
        $userId = (int)getKey($userId, "userMaster");

        $user = $this->db->table('userMaster')->select('singleSignonToken')->where('userId', $userId)->get()->getRowArray();

        if (!$user) {
            return $this->respond([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $newStatus = ($user['singleSignonToken'] == 'Yes') ? 'No' : 'Yes';

        $this->db->table('userMaster')->where('userId', $userId)->update(['singleSignonToken' => $newStatus]);

        return $this->respond([
            'status' => true,
            'message' => 'Single Signon Token Updated Successfully',
            'newStatus' => $newStatus
        ], 200);
    }

    //2FaToken
    public function twoFaToken($userId)
    {
        $userId = (int)getKey($userId, "userMaster");

        //reset 2fa token
        $this->db->query("UPDATE userMaster SET `2FaToken` = NULL WHERE userId = ?", [$userId]);

        $response = [
            'status' => true,
            "message" => "Reset 2FA Token Done",
        ];
        return $this->respond($response, 200);
    }

    //userActive
    public function changeUserStatus($userId)
    {
        $userId = (int)getKey($userId, "userMaster");

        //return if self user id
        if ($userId == $this->user->userId) {
            return $this->respond([
                'status' => false,
                'message' => 'You cannot change your own status'
            ], 403);
        }

        $user = $this->db->table('userMaster')->select('isActive')->where('userId', $userId)->get()->getRowArray();

        if (!$user) {
            return $this->respond([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        $newStatus = ($user['isActive'] == 1) ? 0 : 1;

        $this->db->table('userMaster')->where('userId', $userId)->update(['isActive' => $newStatus]);

        // If user status is being changed, reset lockout and failed attempts
        $this->db->query("UPDATE userMaster SET lockoutUntil = NULL, failedAttempts = 0 WHERE userId = ?", [$userId]);
        $this->db->query("DELETE FROM refreshTokens WHERE userId = ?", [$userId]);

        return $this->respond([
            'status' => true,
            'message' => 'User Status Updated Successfully',
            'newStatus' => $newStatus
        ], 200);
    }

    public function getGroups()
    {
        $tenantId = $this->user->tenantId;
        $groups = $this->db->query("SELECT groupId as id, groupName as `name` FROM userGroups WHERE tenantId = $tenantId AND isAdmin = 0")->getResult();

        $response = [
            'status' => true,
            "message" => "",
            "data" => $groups
        ];

        return $this->respond($response, 200);
    }

    public function loadGroupPermissions($groupId)
    {

        $permissions = $this->db->query("SELECT P.*, 
                                                IF(GP.permissionId IS NOT NULL, 1, 0) AS permissionValue
                                            FROM userPermissionMaster P
                                            LEFT JOIN (
                                                SELECT permissionId FROM userGroupPermissions WHERE groupId = $groupId
                                            ) GP ON P.permissionId = GP.permissionId
                                            WHERE P.scope = 'tenant'
                                        ")->getResult();

        $response = [
            'status' => true,
            "message" => "Permissions retrieved successfully",
            "data" => $permissions
        ];

        return $this->respond($response, 200);
    }

    public function saveGroupPermissions()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $groupId = $jsonInput['groupId'];
        $permissions = $jsonInput['permissions'];

        $existingPermissions = $this->db->table('userGroupPermissions')
            ->select('permissionId')
            ->where('groupId', $groupId)
            ->get()
            ->getResultArray();

        $existingPermissions = array_column($existingPermissions, 'permissionId');

        $toInsert = array_diff($permissions, $existingPermissions);
        $toDelete = array_diff($existingPermissions, $permissions);

        if (!empty($toDelete)) {
            $this->db->table('userGroupPermissions')
                ->where('groupId', $groupId)
                ->whereIn('permissionId', $toDelete)
                ->delete();
        }

        if (!empty($toInsert)) {
            $insertData = array_map(function ($permission) use ($groupId) {
                return ['groupId' => $groupId, 'permissionId' => $permission];
            }, $toInsert);

            $this->db->table('userGroupPermissions')->insertBatch($insertData);
        }

        $response = [
            'status' => true,
            "message" => "Permissions saved successfully",
        ];

        return $this->respond($response, 200);
    }

    //get use name
    public function getUsersList()
    {
        $users = $this->db->query("SELECT userId as id, CONCAT(firstName, ' ', lastName) as name FROM userMaster WHERE tenantId = " . $this->user->tenantId . " ORDER BY firstName")->getResult();

        $response = [
            'status' => true,
            // "message" => "User Name dropdown retrieved successfully",
            "data" => $users
        ];


        return $this->respond($response, 200);
    }

    public function getSalesUsersList()
    {
        $users = $this->db->query("SELECT userId as id, CONCAT(firstName, ' ', lastName) as name FROM userMaster WHERE tenantId = " . $this->user->tenantId . " ORDER BY firstName")->getResult();
        // foreach ($users as $k => $user) {
        //     //check permission
        //     if (!UserPermissionLib::userCanDo("leadMaster", ['viewAll', 'asignedToOnly'], $user->userId)) {
        //         unset($users[$k]);
        //         continue;
        //     }
        // }
        $response = [
            'status' => true,
            // "message" => "User Name dropdown retrieved successfully",
            "data" => $users
        ];
        return $this->respond($response, 200);
    }
}
