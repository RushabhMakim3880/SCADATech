<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingGroupsSeeder extends Seeder
{
  public $priority = 1;

  public function run()
  {

    $seedName = static::class;
    $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
    if ($exists > 0) {
      return;
    }

    $data = [];

    // Module, to manage enable/disable modules.
    $data[] = [
      'groupKey' => "modules",
      'label' => 'Modules',
      'description' => 'To manage enabled/disabled modules.',
      'icon' => '',
      'visibility' => 'saasOnly',
      'sortOrder' => 0,
      'isActive' => 1,
    ];

    // General
    $data[] = [
      'groupKey' => "general",
      'label' => 'General',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 1,
      'isActive' => 1,
    ];

    // Theme & UI
    $data[] = [
      'groupKey' => "theme",
      'label' => 'Theme & UI',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 2,
      'isActive' => 1,
    ];

    // DataTable
    $data[] = [
      'groupKey' => "dataTable",
      'label' => 'DataTable',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 3,
      'isActive' => 1,
    ];

    // Peformance
    $data[] = [
      'groupKey' => "performance",
      'label' => 'Performance',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 4,
      'isActive' => 1,
    ];

    // Date & Time
    $data[] = [
      'groupKey' => "dateTime",
      'label' => 'Date & Time',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 5,
      'isActive' => 1,
    ];

    // Notifications
    $data[] = [
      'groupKey' => "notifications",
      'label' => 'Notifications',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 6,
      'isActive' => 1,
    ];

    // Login & Security
    $data[] = [
      'groupKey' => "loginSecurity",
      'label' => 'Login & Security',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 7,
      'isActive' => 1,
    ];

    // Company Details
    $data[] = [
      'groupKey' => "companyDetails",
      'label' => 'Company Details',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 8,
      'isActive' => 1,
    ];

    // Core System
    $data[] = [
      'groupKey' => "coreSystem",
      'label' => 'Core System',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 9,
      'isActive' => 1,
    ];

    // File Uploads
    $data[] = [
      'groupKey' => "fileUploads",
      'label' => 'File Uploads',
      'description' => '',
      'icon' => '',
      'visibility' => 'all',
      'sortOrder' => 10,
      'isActive' => 1,
    ];


    $data[] = [
      'groupKey' => "features",
      'label' => 'Features',
      'description' => '',
      'icon' => '',
      'visibility' => 'saasOnly',
      'sortOrder' => 11,
      'isActive' => 1,
    ];

    $this->db->table('settingGroups')->insertBatch($data);

    // Record this seeder in seedHistory
    $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
  }
}
