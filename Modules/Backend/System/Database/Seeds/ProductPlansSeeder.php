<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductPlansSeeder extends Seeder
{
  public $priority = 1;

  public function run()
  {

    $seedName = static::class;
    $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
    if ($exists > 0) {
      return;
    }

    $data = [
      'name' => 'Base Plan',
      'description' => 'Base Plan',
      'basePrice' => 0,
      'isActive' => 1,
    ];

    $this->db->table('productPlans')->insert($data);

    // Record this seeder in seedHistory
    $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
  }
}
