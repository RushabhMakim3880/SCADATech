<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FeatureRegistrySeeder extends Seeder
{
  public $priority = 2;
  // public string $type = "default";

  public function run()
  {

    $seedName = static::class;
    $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
    if ($exists > 0) {
      return;
    }

    $data = [];

    // Feature Registry
    $data[] = [
      'featureKey' => "appShortName",
      'groupKey' => "general",
      'label' => 'App Short Name',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'MyApp',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "appName",
      'groupKey' => "general",
      'label' => 'App Name',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'My Application',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "appTagline",
      'groupKey' => "general",
      'label' => 'App Tagline',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'Making Life Easier',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "theme",
      'groupKey' => "theme",
      'label' => 'Theme',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '1',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

      $data[] = [
      'featureKey' => "subTheme",
      'groupKey' => "theme",
      'label' => 'Sub Theme',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '1',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "chartTheme",
      'groupKey' => "theme",
      'label' => 'Chart Theme',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["default", "azul", "bee-inspired", "blue", "caravan", "carp", "cool", "dark-blue", "dark-bold", "dark-digerati", "dark-fresh-cut", "dark-mushroom", "dark", "eduardo", "forest", "fresh-cut", "fruit", "gray", "green", "helianthus", "infographic", "inspired", "jazz", "london", "macarons", "macarons2", "mint", "red-velvet", "red", "roma", "royal", "sakura", "shine", "tech-blue", "vintage"]),
      'defaultValue' => 'default',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // manageTablePageSize
    $data[] = [
      'featureKey' => "manageTablePageSize",
      'groupKey' => "dataTable",
      'label' => 'Manage Table Page Size',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '10',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // manageTablePageSizeList
    $data[] = [
      'featureKey' => "manageTablePageSizeList",
      'groupKey' => "dataTable",
      'label' => 'Manage Table Page Size List',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => '5, 10, 25, 50, 100, 250, 500, 1000, 2000',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // dataExportLimit
    $data[] = [
      'featureKey' => "dataExportLimit",
      'groupKey' => "dataTable",
      'label' => 'Data Export Limit',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '2000',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // manageScreenIdType
    $data[] = [
      'featureKey' => "manageScreenIdType",
      'groupKey' => "dataTable",
      'label' => 'Manage Screen ID Type',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["idOnly", "idWithIcon", "iconWithId", "iconOnly"]),
      'defaultValue' => 'idWithIcon',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // manageScreenIdIcon
    $data[] = [
      'featureKey' => "manageScreenIdIcon",
      'groupKey' => "dataTable",
      'label' => 'Manage Screen ID Icon',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'fa fa-bars',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // cdnToLocal
    $data[] = [
      'featureKey' => "cdnToLocal",
      'groupKey' => "performance",
      'label' => 'CDN to Local',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // combinedAssets
    $data[] = [
      'featureKey' => "combinedAssets",
      'groupKey' => "performance",
      'label' => 'Combined Assets',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // dateFormat
    $data[] = [
      'featureKey' => "dateFormat",
      'groupKey' => "dateTime",
      'label' => 'Date Format',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'd/m/Y',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // timeFormat
    $data[] = [
      'featureKey' => "timeFormat",
      'groupKey' => "dateTime",
      'label' => 'Time Format',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'g:i A',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // dateTimeFormat
    $data[] = [
      'featureKey' => "dateTimeFormat",
      'groupKey' => "dateTime",
      'label' => 'Date & Time Format',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'd/m/Y, g:i A',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // webPushNotification
    $data[] = [
      'featureKey' => "webPushNotification",
      'groupKey' => "notifications",
      'label' => 'Web Push Notification',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isJsSide' => 1,
      'isVisible' => 0,
      'sortOrder' => 0,
    ];

    // notificationLibrary
    $data[] = [
      'featureKey' => "notificationLibrary",
      'groupKey' => "notifications",
      'label' => 'Notification Library',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["Toastr", "Notyf", "SweetAlert2"]),
      'defaultValue' => 'Toastr',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // notificationPositionX
    $data[] = [
      'featureKey' => "notificationPositionX",
      'groupKey' => "notifications",
      'label' => 'Notification Position Horizontal',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["left", "right", "center"]),
      'defaultValue' => 'center',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // notificationPositionY
    $data[] = [
      'featureKey' => "notificationPositionY",
      'groupKey' => "notifications",
      'label' => 'Notification Position Verticle',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["top", "center", "bottom"]),
      'defaultValue' => 'top',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // notificationDelay
    $data[] = [
      'featureKey' => "notificationDelay",
      'groupKey' => "notifications",
      'label' => 'Notification Timeout',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '3000',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // notificationCloseButton
    $data[] = [
      'featureKey' => "notificationCloseButton",
      'groupKey' => "notifications",
      'label' => 'Notification Close Button',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // notificationProgressBar
    $data[] = [
      'featureKey' => "notificationProgressBar",
      'groupKey' => "notifications",
      'label' => 'Notification Progress Bar',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '1',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // notificationPlaySound
    $data[] = [
      'featureKey' => "notificationPlaySound",
      'groupKey' => "notifications",
      'label' => 'Notification Sound',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // twoFactorAuth
    $data[] = [
      'featureKey' => "twoFactorAuth",
      'groupKey' => "loginSecurity",
      'label' => 'TOTP',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // simpleCaptcha
    $data[] = [
      'featureKey' => "simpleCaptcha",
      'groupKey' => "loginSecurity",
      'label' => 'Login Captcha',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // singleSignOn
    $data[] = [
      'featureKey' => "singleSignOn",
      'groupKey' => "loginSecurity",
      'label' => 'Single Login Only',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // maxLoginAttempts
    $data[] = [
      'featureKey' => "maxLoginAttempts",
      'groupKey' => "loginSecurity",
      'label' => 'Max Failed Login Attempts',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '3',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // lockoutTime
    $data[] = [
      'featureKey' => "lockoutTime",
      'groupKey' => "loginSecurity",
      'label' => 'Lockout Time In Minutes',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '30',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // passwordExpiryDays
    $data[] = [
      'featureKey' => "passwordExpiryDays",
      'groupKey' => "loginSecurity",
      'label' => 'Force Password Change Days',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '90',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // passwordHistory
    $data[] = [
      'featureKey' => "passwordHistory",
      'groupKey' => "loginSecurity",
      'label' => 'Cant Use Last X Passwords',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '3',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // minPasswordLength
    $data[] = [
      'featureKey' => "minPasswordLength",
      'groupKey' => "loginSecurity",
      'label' => 'Min Password Length',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '8',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // passwordStrength
    $data[] = [
      'featureKey' => "passwordStrength",
      'groupKey' => "loginSecurity",
      'label' => 'Password Strength',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["low", "medium", "high"]),
      'defaultValue' => 'medium',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // contactEmail
    $data[] = [
      'featureKey' => "contactEmail",
      'groupKey' => "companyDetails",
      'label' => 'Contact Email',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'admin@scadatech.local',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // ownerCompanyName
    $data[] = [
      'featureKey' => "ownerCompanyName",
      'groupKey' => "companyDetails",
      'label' => 'Company Name',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'SCADATech',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // websiteUrl
    $data[] = [
      'featureKey' => "websiteUrl",
      'groupKey' => "companyDetails",
      'label' => 'Website URL',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'https://github.com/RushabhMakim3880/SCADATech',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // websiteText
    $data[] = [
      'featureKey' => "websiteText",
      'groupKey' => "companyDetails",
      'label' => 'Website Name',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 'SCADATech',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // emailTemplate
    $data[] = [
      'featureKey' => "emailTemplate",
      'groupKey' => "coreSystem",
      'label' => 'Email Template',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '1',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // apiDocumentation
    $data[] = [
      'featureKey' => "apiDocumentation",
      'groupKey' => "coreSystem",
      'label' => 'Api Documentation',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // pdfGenerator
    $data[] = [
      'featureKey' => "pdfGenerator",
      'groupKey' => "coreSystem",
      'label' => 'PDF Generator',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["wkhtmltopdf", "dompdf", "mpdf"]),
      'defaultValue' => 'wkhtmltopdf',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    //defaultLanguage
    $data[] = [
      'featureKey' => "defaultLanguage",
      'groupKey' => "coreSystem",
      'label' => 'Language',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["en"]),
      'defaultValue' => 'en',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    //maxFileSizeMB
    $data[] = [
      'featureKey' => "maxFileSizeMB",
      'groupKey' => "fileUploads",
      'label' => 'Single File Upload Size Limit (MB)',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '5',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    //maxTotalFileSizeMB
    $data[] = [
      'featureKey' => "maxTotalFileSizeMB",
      'groupKey' => "fileUploads",
      'label' => 'Total Bulk File Upload Size Limit (MB)',
      'description' => '',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => '50',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    //allowedFileTypes
    $data[] = [
      'featureKey' => "allowedFileTypes",
      'groupKey' => "fileUploads",
      'label' => 'Allowed File Extensions',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => "jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt, zip, rar, apk",
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 1,
      'sortOrder' => 0,
    ];

    // customcss
    $data[] = [
      'featureKey' => "customCss",
      'groupKey' => "theme",
      'label' => 'Custom CSS',
      'description' => '',
      'dataType' => 'text',
      'inputType' => 'textarea',
      'options' => null,
      'defaultValue' => '',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // customejs
    $data[] = [
      'featureKey' => "customJs",
      'groupKey' => "theme",
      'label' => 'Custom JS',
      'description' => '',
      'dataType' => 'text',
      'inputType' => 'textarea',
      'options' => null,
      'defaultValue' => '',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // pwaEnabled
    $data[] = [
      'featureKey' => "pwaEnabled",
      'groupKey' => "notifications",
      'label' => 'PWA Enabled',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // loginLogoType
    $data[] = [
      'featureKey' => "loginLogoType",
      'groupKey' => "theme",
      'label' => 'Login Logo Type',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["dark", "light"]),
      'defaultValue' => 'dark',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    // sidebarLogoType
    $data[] = [
      'featureKey' => "sidebarLogoType",
      'groupKey' => "theme",
      'label' => 'Sidebar Logo Type',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["dark", "light"]),
      'defaultValue' => 'dark',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "limitLoginToTrustedDevices",
      'groupKey' => "loginSecurity",
      'label' => 'Limit login to trusted devices',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => '0',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "lastUsedSince",
      'groupKey' => "loginSecurity",
      'label' => 'Limit login to trusted devices last used since',
      'description' => 'numbers of days to auto untrust devices',
      'dataType' => 'int',
      'inputType' => 'number',
      'options' => null,
      'defaultValue' => 30,
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $data[] = [
      'featureKey' => "chatModuleEnabled",
      'groupKey' => "features",
      'label' => 'Allow live chat module',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'switch',
      'options' => null,
      'defaultValue' => 0,
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];
    $data[] = [
      'featureKey' => "menuPosition",
      'groupKey' => "theme",
      'label' => 'Set Menu Position',
      'description' => '',
      'dataType' => 'string',
      'inputType' => 'select',
      'options' => json_encode(["Top", "Left", "Right"]),
      'defaultValue' => 'Left',
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    //globalSearch
    $data[] = [
      'featureKey' => "isGlobalSearch",
      'groupKey' => "features",
      'label' => 'Allow to show global search',
      'description' => '',
      'dataType' => 'bool',
      'inputType' => 'text',
      'options' => null,
      'defaultValue' => 0,
      'isCustomizable' => 0,
      'isVisible' => 0,
      'isJsSide' => 0,
      'sortOrder' => 0,
    ];

    $this->db->table('featureRegistry')->insertBatch($data);

    // Record this seeder in seedHistory
    $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
  }
}
