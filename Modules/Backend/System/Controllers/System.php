<?php

namespace Modules\Backend\System\Controllers;

use App\Controllers\ApiBaseController;
use Modules\Backend\System\Models\UserModel;
use Modules\Backend\System\Models\AppConfigModel;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;
use App\Libraries\PdfGenerator;
use App\Libraries\Auth;
use App\Libraries\SelfRefDataLib;


// use OpenApi\Annotations as OA;
class System extends ApiBaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function dashboardTemplate($templateId = 0)
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'dynamicDashboard')) {
            return $this->failUnauthorized('Unauthorized');
        }

        //if GET method
        if ($this->request->getMethod() == 'GET') {
            return $this->getDashboardTemplate($templateId);
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $ex = $this->db->table("dashboardTemplates")
            ->where("templateId", $templateId)
            ->where("tenantId", 1) // Adding tenantId condition with default value 1
            ->get()
            ->getRowArray();

        if ($ex) {
            $this->db->table("dashboardTemplates")
                ->where("templateId", $templateId)
                ->where("tenantId", 1) // Adding tenantId condition with default value 1
                ->update([
                    "widgetName" => $jsonInput['widgetName'],
                    "htmlTemplate" => $jsonInput['htmlTemplate'],
                    "dataSource" => $jsonInput['dataSource'],
                    "updatedAt" => timenow(),
                ]);


            $response = [
                'status' => true,
                "message" => "Dashboard Template updated successfully",
                'templateId' => $ex['templateId']
            ];

            return $this->respond($response, 200);
        } else {
            $template = [
                "widgetName" => $jsonInput['widgetName'],
                "htmlTemplate" => $jsonInput['htmlTemplate'],
                "dataSource" => $jsonInput['dataSource'],
                "updatedAt" => timenow(),
                "createdAt" => timenow(),
                "createdBy" => $this->user->userId,
                "tenantId" => 1, // Adding tenantId with default value 1
            ];

            $this->db->table("dashboardTemplates")->insert($template);


            $templateId = $this->db->insertID();

            $response = [
                'status' => true,
                "message" => "Dashboard Template saved successfully",
                'templateId' => $templateId
            ];
        }

        return $this->respond($response, 200);
    }

    public function dashboardTemplates()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'dynamicDashboard')) {
            return $this->failUnauthorized('Unauthorized');
        }

        $templates = $this->db->table("dashboardTemplates")
            ->where("tenantId", 1) // Adding tenantId condition with default value 1
            ->get()
            ->getResultArray();

        $response = [
            'status' => true,
            "message" => "Dashboard Templates retrieved successfully",
            'templates' => $templates
        ];

        return $this->respond($response, 200);
    }

    private function getDashboardTemplate($templateId)
    {
        $template = $this->db->table("dashboardTemplates")
            ->where("templateId", $templateId)
            ->where("tenantId", 1) // Adding tenantId condition with default value 1
            ->get()
            ->getRowArray();

        if (!$template) {
            return $this->failNotFound('Template not found');
        }

        $response = [
            'status' => true,
            "message" => "User retrieved successfully",
            'template' => $template
        ];

        return $this->respond($response, 200);
    }

    public function dashboardLayout($dashboardId = 0)
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'dynamicDashboard')) {
            return $this->failUnauthorized('Unauthorized');
        }

        //if GET method
        if ($this->request->getMethod() == 'GET') {
            return $this->getDashboardLayout($dashboardId);
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $ex = $this->db->table("dashboardLayouts")
            ->where("uid", $dashboardId)
            ->where("tenantId", 1)
            ->get()
            ->getRowArray();

        if ($ex) {
            $this->db->table("dashboardLayouts")
                ->where("uid", $dashboardId)
                ->where("tenantId", 1)
                ->update([
                    "dashboardName" => $jsonInput['dashboardName'],
                    "layout" => json_encode($jsonInput['layout']),
                    "updatedAt" => timenow(),
                ]);


            $response = [
                'status' => true,
                "message" => "Dashboard Layout updated successfully",
                'dashboardId' => $ex['dashboardId']
            ];

            return $this->respond($response, 200);
        } else {
            $layout = [
                "uid" => $dashboardId,
                "dashboardName" => $jsonInput['dashboardName'],
                "layout" => json_encode($jsonInput['layout']),
                "updatedAt" => timenow(),
                "createdAt" => timenow(),
                "createdBy" => $this->user->userId,
                "tenantId" => 1,
            ];


            $this->db->table("dashboardLayouts")->insert($layout);

            $dashboardId = $this->db->insertID();

            $response = [
                'status' => true,
                "message" => "Dashboard Layout saved successfully",
                'layoutId' => $dashboardId
            ];
        }

        return $this->respond($response, 200);
    }

    private function getDashboardLayout($dashboardId)
    {
        $layout = $this->db->table("dashboardLayouts")
            ->where("uid", $dashboardId)
            ->where("tenantId", 1)
            ->get()
            ->getRowArray();

        if (!$layout) {
            return $this->failNotFound('Layout not found');
        }

        $response = [
            'status' => true,
            "message" => "User retrieved successfully",
            'layout' => $layout
        ];

        return $this->respond($response, 200);
    }

    public function dashboardData()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $widgets = $jsonInput['widgets'];

        if (!is_array($widgets) or count($widgets) == 0) {
            return $this->fail('No Widgets Found', 400);
        }
        $layout = $this->db->query("SELECT dataSource FROM dashboardTemplates 
    WHERE templateId IN (" . implode(",", $widgets) . ") 
    AND tenantId = " . $this->user->tenantId)
            ->getResult();

        $preparedData = [];

        // debug($layout);
        // die();

        $myGroup = UserPermissionLib::getUser()->groupName;

        foreach ($layout as $l) {
            $dataSource = json_decode($l->dataSource, true);

            foreach ($dataSource as $ds) {
                $tag = $ds['tagId'];
                $sql = false;
                if (isset($ds['queries'][$myGroup])) {
                    $sql = $ds['queries'][$myGroup];
                } else if (isset($ds['queries']["all"])) {
                    $sql = $ds['queries']["all"];
                }

                if ($sql) {
                    $data = $this->db->query($sql)->getResultArray();
                    if (count($data) == 1) {
                        $preparedData = array_merge($preparedData, $data[0]);
                    } else {
                        $preparedData[$tag]['data'] = $data;
                    }
                }
            }
        }

        $response = [
            'status' => true,
            "message" => "Dashboard data retrieved successfully",
            'data' => $preparedData
        ];

        return $this->respond($response, 200);
    }


    public function getAppConfig()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'dynamicDashboard')) {
            return $this->failUnauthorized('Unauthorized');
        }

        $appConfigModel = new AppConfigModel();
        $settings = $appConfigModel->where("tenantId", 1) // Adding tenantId condition with default value 1
            ->findAll();

        // Transform the settings into an associative array for easier access on the frontend
        $settingsArray = [];
        foreach ($settings as $setting) {
            // Use object notation to access properties
            $settingsArray[$setting->fieldId] = $setting->fieldValue;
        }

        return $this->respond(['status' => true, 'message' => 'Settings retrieved successfully', 'data' => $settingsArray]);
    }

    public function saveAppConfig()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'dynamicDashboard')) {
            return $this->failUnauthorized('Unauthorized');
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $validation = \Config\Services::validation();

        $rules = [
            'appName' => [
                'label' => 'App Name',
                'rules' => 'required',
                'errors' => [
                    'required' => 'The {field} cannot be empty.'
                ]
            ],
        ];

        $validation->setRules($rules);

        if (!$validation->run($jsonInput)) {
            return $this->fail($validation->getErrors(), 400);
        }

        foreach ($jsonInput as $key => $value) {
            $existingSetting = $this->db->table('appConfig')
                ->where('fieldId', $key)
                ->where('tenantId', 1) // Adding tenantId condition with default value 1
                ->get()
                ->getRow();


            if (is_null($existingSetting)) {
                $this->db->table('appConfig')->insert([
                    'fieldId'   => $key,
                    'fieldValue' => $value,
                    'tenantId' => 1 // Adding tenantId in the insert
                ]);
            } else {
                $this->db->table('appConfig')->update([
                    'fieldValue' => $value
                ], [
                    'fieldId' => $key,
                    'tenantId' => 1
                ]);
            }
        }

        // Clear the cache
        service('cache')->delete('1_appConfig');

        return $this->respondCreated(['message' => 'Data saved successfully']);
    }

    /*****************************  Start getLogoAndBg Screen Code ***************************/
    public function getLogoAndBg()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'manageBranding')) {
            return $this->failUnauthorized('Unauthorized');
        }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        /************************ Dark Logo ***********************/
        $pic1 = $jsonInput['darkBg'];
        if (!is_null($pic1)) {
            $uploadDir = ROOTPATH . "public/uploads/branding/1/";
            $picPath = $uploadDir . "darkAppLogo.png";
            if ($pic1 == "") {
                // Remove existing image
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            } elseif ($pic1 == "nochange") {

                // Do nothing
            } else {
                //create directory recursively if does not exist.
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                base64ToImage($pic1, $picPath);

                //insert image attachment table code start here...
                $fileName = 'darkAppLogo.png';
                $relativePath = str_replace(ROOTPATH . 'public', '', $picPath);

                // Check if the attachment already exists for the tenant with the same name
                $existingAttachment = $this->db->table('mAttachments')
                    ->where('tenantId', 1)
                    ->where('name', $fileName)
                    ->where('isDeleted', 0)
                    ->get()
                    ->getRow();

                $attachmentData = [
                    'filePath'      => $relativePath,
                    'size'          => file_exists($picPath) ? filesize($picPath) : 0,
                    'parentType'    => 'Company Setting : Dark Logo',
                    'parentId'      => 0,
                    'childId'       => 0,
                    'isDeleted'     => 0,
                    'extension'     => 'png',
                    'documentType'  => 'image',
                    'createdBy'     => $this->user->userId ?? null,
                    'uploadTime'    => timenow(),
                    'deletedTime'   => timenow(),
                ];

                if ($existingAttachment) {
                    // Update existing record
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $existingAttachment->attachmentId)
                        ->update($attachmentData);

                    $attachmentId = $existingAttachment->attachmentId;
                } else {
                    // Insert new record
                    $attachmentData['tenantId'] = 1;
                    $attachmentData['serialNo'] = '';
                    $attachmentData['name']     = $fileName;

                    $this->db->table('mAttachments')->insert($attachmentData);
                    $attachmentId = $this->db->insertID();

                    $serialNo = assignSerialNumber(1, "mAttachments", "attachmentId", $attachmentId);

                    // Update serial number
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $attachmentId)
                        ->update(['serialNo' => $serialNo]);
                    //insert image attachment table code end here...

                }
            }
        }
        /************************ Dark Logo ***********************/

        /*************************** Favicon ************************/
        $pic2 = $jsonInput['favicon'];

        if (!is_null($pic2)) {
            $uploadDir = ROOTPATH . "public/uploads/branding/1/";
            $picPath = $uploadDir . "appFavicon.png";
            if ($pic2 == "") {
                // Remove existing image
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            } elseif ($pic2 == "nochange") {
                // Do nothing
            } else {
                //create directory recursively if does not exist.
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                base64ToImage($pic2, $picPath);

                //insert image attachment table code start here...
                $fileName = 'appFavicon.png';
                $relativePath = str_replace(ROOTPATH . 'public', '', $picPath);

                // Check if an attachment with the same tenantId and name already exists
                $existingAttachment = $this->db->table('mAttachments')
                    ->where('tenantId', 1)
                    ->where('name', $fileName)
                    ->where('isDeleted', 0)
                    ->get()
                    ->getRow();

                $attachmentData = [
                    'filePath'      => $relativePath,
                    'size'          => file_exists($picPath) ? filesize($picPath) : 0,
                    'parentType'    => 'Company Setting : AppFavicon Logo',
                    'parentId'      => 0,
                    'childId'       => 0,
                    'isDeleted'     => 0,
                    'extension'     => 'png',
                    'documentType'  => 'image',
                    'createdBy'     => $this->user->userId ?? null,
                    'uploadTime'    => timenow(),
                    'deletedTime'   => timenow(),
                ];

                if ($existingAttachment) {
                    // Overwrite existing DB record
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $existingAttachment->attachmentId)
                        ->update($attachmentData);

                    $attachmentId = $existingAttachment->attachmentId;
                } else {
                    // Insert new record
                    $attachmentData['tenantId'] = 1;
                    $attachmentData['serialNo'] = '';
                    $attachmentData['name']     = $fileName;

                    $this->db->table('mAttachments')->insert($attachmentData);
                    $attachmentId = $this->db->insertID();

                    $serialNo = assignSerialNumber(1, "mAttachments", "attachmentId", $attachmentId);

                    // Update serial number
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $attachmentId)
                        ->update(['serialNo' => $serialNo]);
                }

                //insert image attachment table code end here...
            }
        }
        /*************************** Favicon ************************/

        /*********** Background image (Login Background ) ***********/
        $pic3 = $jsonInput['loginBg'];
        if (!is_null($pic3)) {
            $uploadDir = ROOTPATH . "public/uploads/branding/1/";
            $picPath = $uploadDir . "appLoginBg.jpg";
            if ($pic3 == "") {
                // Remove existing image
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            } elseif ($pic3 == "nochange") {
                // Do nothing
            } else {
                //create directory recursively if does not exist.
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                base64ToImage($pic3, $picPath);

                //insert image attachment table code start here...
                $fileName = 'appLoginBg.png';
                $relativePath = str_replace(ROOTPATH . 'public', '', $picPath);

                // Check if the attachment already exists for this tenant and filename
                $existingAttachment = $this->db->table('mAttachments')
                    ->where('tenantId', 1)
                    ->where('name', $fileName)
                    ->where('isDeleted', 0)
                    ->get()
                    ->getRow();

                // Prepare data for insert or update
                $attachmentData = [
                    'filePath'      => $relativePath,
                    'size'          => file_exists($picPath) ? filesize($picPath) : 0,
                    'parentType'    => 'Company Setting : AppLoginBg Logo',
                    'parentId'      => 0,
                    'childId'       => 0,
                    'isDeleted'     => 0,
                    'extension'     => 'png',
                    'documentType'  => 'image',
                    'createdBy'     => $this->user->userId ?? null,
                    'uploadTime'    => timenow(),
                    'deletedTime'   => timenow(),
                ];

                if ($existingAttachment) {
                    // Update existing record
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $existingAttachment->attachmentId)
                        ->update($attachmentData);

                    $attachmentId = $existingAttachment->attachmentId;
                } else {
                    // Insert new record
                    $attachmentData['tenantId'] = 1;
                    $attachmentData['serialNo'] = ''; // temporary placeholder
                    $attachmentData['name']     = $fileName;

                    $this->db->table('mAttachments')->insert($attachmentData);
                    $attachmentId = $this->db->insertID();

                    $serialNo = assignSerialNumber(1, "mAttachments", "attachmentId", $attachmentId);

                    // Update serialNo
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $attachmentId)
                        ->update(['serialNo' => $serialNo]);
                }

                //insert image attachment table code end here...
            }
        }
        /*********** Background image (Login Background ) ***********/

        /************************ Light Logo ***********************/
        $pic4 = $jsonInput['lightBg'];
        if (!is_null($pic4)) {
            $uploadDir = ROOTPATH . "public/uploads/branding/1/";
            $picPath = $uploadDir . "lightAppLogo.png";
            if ($pic4 == "") {
                // Remove existing image
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            } elseif ($pic4 == "nochange") {
                // Do nothing
            } else {
                //create directory recursively if does not exist.
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                base64ToImage($pic4, $picPath);

                //insert image attachment table code start here...

                $fileName = 'lightAppLogo.png';
                $relativePath = str_replace(ROOTPATH . 'public', '', $picPath);

                // Check if the attachment already exists for this tenant and filename
                $existingAttachment = $this->db->table('mAttachments')
                    ->where('tenantId', 1)
                    ->where('name', $fileName)
                    ->where('isDeleted', 0)
                    ->get()
                    ->getRow();

                // // Prepare data for insert or update
                $attachmentData = [
                    'filePath'      => $relativePath,
                    'size'          => file_exists($picPath) ? filesize($picPath) : 0,
                    'parentType'    => 'Company Setting : lightAppLogo Logo',
                    'parentId'      => 0,
                    'childId'       => 0,
                    'isDeleted'     => 0,
                    'extension'     => 'png',
                    'documentType'  => 'image',
                    'createdBy'     => $this->user->userId ?? null,
                    'uploadTime'    => timenow(),
                    'deletedTime'   => timenow(),
                ];

                if ($existingAttachment) {
                    // Update existing record
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $existingAttachment->attachmentId)
                        ->update($attachmentData);

                    $attachmentId = $existingAttachment->attachmentId;
                } else {
                    //     // Insert new record
                    $attachmentData['tenantId'] = 1;
                    $attachmentData['serialNo'] = '';
                    $attachmentData['name']     = $fileName;

                    $this->db->table('mAttachments')->insert($attachmentData);
                    $attachmentId = $this->db->insertID();

                    $serialNo = assignSerialNumber(1, "mAttachments", "attachmentId", $attachmentId);

                    // Update serialNo
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $attachmentId)
                        ->update(['serialNo' => $serialNo]);
                    //insert image attachment table code end here...

                }
            }
        }
        /************************ Light Logo ***********************/

        /************************ Print Logo ***********************/
        $pic5 = $jsonInput['printLg'];
        if (!is_null($pic5)) {
            $uploadDir = ROOTPATH . "public/uploads/branding/1/";
            $picPath = $uploadDir . "printAppLogo.png";
            if ($pic5 == "") {
                // Remove existing image
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            } elseif ($pic5 == "nochange") {
                // Do nothing
            } else {
                //create directory recursively if does not exist.
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                base64ToImage($pic5, $picPath);

                //insert image attachment table code start here...
                $fileName = 'printAppLogo.png';
                $relativePath = str_replace(ROOTPATH . 'public', '', $picPath);

                // Check if this logo already exists for the tenant
                $existingAttachment = $this->db->table('mAttachments')
                    ->where('tenantId', 1)
                    ->where('name', $fileName)
                    ->where('isDeleted', 0)
                    ->get()
                    ->getRow();

                $attachmentData = [
                    'filePath'      => $relativePath,
                    'size'          => file_exists($picPath) ? filesize($picPath) : 0,
                    'parentType'    => 'Company Setting : PrintAppLogo Logo',
                    'parentId'      => 0,
                    'childId'       => 0,
                    'isDeleted'     => 0,
                    'extension'     => 'png',
                    'documentType'  => 'image',
                    'createdBy'     => $this->user->userId ?? null,
                    'uploadTime'    => timenow(),
                    'deletedTime'   => timenow(),
                ];

                if ($existingAttachment) {
                    // Update existing DB record
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $existingAttachment->attachmentId)
                        ->update($attachmentData);

                    $attachmentId = $existingAttachment->attachmentId;
                } else {
                    // Insert new record
                    $attachmentData['tenantId'] = 1;
                    $attachmentData['serialNo'] = ''; // placeholder
                    $attachmentData['name']     = $fileName;

                    $this->db->table('mAttachments')->insert($attachmentData);
                    $attachmentId = $this->db->insertID();

                    $serialNo = assignSerialNumber(1, "mAttachments", "attachmentId", $attachmentId);

                    // Update serial number
                    $this->db->table('mAttachments')
                        ->where('attachmentId', $attachmentId)
                        ->update(['serialNo' => $serialNo]);
                }

                //insert image attachment table code end here...
            }
        }
        /************************ Print Logo ***********************/

        return $this->respondCreated(['message' => 'Image save successfully !']);
    }
    /*****************************  End getLogoAndBg Screen Code ***************************/

    public function resetManageTableColumnSettings($module)
    {
        $this->db->table('userSettings')->where('key', $module . "_columns")->where('userId', $this->user->userId)->delete();
        $response = [
            'status' => true,
            "message" => "Reset done!",
        ];
        return $this->respond($response, 200);
    }


    /*****************************  Start uploadapk Screen Code ***************************/
    public function uploadapk()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'uploadapk')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $input = $this->getInputData();
        $uploadedFiles = $input['uploadedFiles'];

        if (!empty($uploadedFiles)) {

            foreach ($uploadedFiles as $apkFile => $file) {

                if ($apkFile == "apkFile") {
                    $uploadPath = ROOTPATH . 'public/uploads/app';
                    $fileName = 'launchpad.apk';
                    $filePath = $uploadPath . '/' . $fileName;

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $this->uploadFile($file, $uploadPath, $fileName, false, false);
                }
            }
        }
        return $this->respondCreated(['message' => 'APK Uploaded!']);
    }
    /*****************************  End uploadapk Screen Code ***************************/

    public function loginCheck()
    {
        return $this->respond([
            'status' => true,
            'message' => 'User is logged in',
        ]);
    }
    public function testApi($mobile, $sim)
    {
        // Simulate mobile match - replace with real DB check as needed
        // Match mobile with specific numbers directly in if condition
        if ($mobile === '9409150015' || $mobile === '8888877777') {
            $response = [
                'status' => true,
                'message' => 'Mobile matched',
                'data' => [
                    'details' => [
                        'name' => 'John Doe',
                        'mobile' => $mobile,
                        'sim' => $sim,
                        'email' => 'john@example.com',
                        'location' => 'Rajkot',
                    ],
                    'view_more_button' => [
                        'text' => 'View More',
                        'url' => base_url('lead/details/' . $mobile)
                    ]
                ]
            ];
        } else {
            $response = [
                'status' => true,
                'unknown contact' => '',
                'message' => 'Add new lead',
                'data' => [
                    'add_new_url' => 'api/QuickLead/addQuickLead'
                ]
            ];
        }

        return $this->respond($response, 200);
    }

    public function getProjectName()
    {
        // if (!$this->isAuthenticated()) {
        //     return $this->failUnauthorized('Unauthorized');
        // }

        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        // Check if jsonInput is empty
        if (empty($jsonInput)) {
            return $this->respond(['status' => false, 'message' => 'No input provided']);
        }

        // Assuming jsonInput contains projectName
        $projectName = isset($jsonInput['projectName']) ? trim(strtolower($jsonInput['projectName'])) : '';

        // Prepare the response based on projectName
        if ($projectName === 'launchpad') {
            $data = [
                'status' => true,
                'apiUrl' => 'https://launchpad.devapp.co.in/',
            ];
        } elseif ($projectName === 'saarthierp') {
            $data = [
                'status' => true,
                'apiUrl' => 'https://saarthierp.devapp.co.in/',
            ];
        } elseif ($projectName === 'embicon') {
            $data = [
                'status' => true,
                'apiUrl' => 'https://embicon.devapp.co.in/',
            ];
        } elseif ($projectName === 'vimox') {
            $data = [
                'status' => true,
                'apiUrl' => 'https://vimox2.mindstien.com/',
            ];
        } elseif ($projectName === 'envitro') {
            $data = [
                'status' => true,
                'apiUrl' => 'https://envitro.devapp.co.in/',
            ];
        } else {
            // Handle other project names or return a default response
            $data = [
                'status' => false,
                'message' => 'Project not found',
            ];
        }

        // return $this->respond($response, 200);
        return $this->response->setJSON($data);
    }
}
