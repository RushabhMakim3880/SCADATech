<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class menuFix extends Seeder
{
    public $priority = 100;

    public function run()
    {

        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        // Fix menu paths
        $this->db->table('menuConfig')->where('module', 'ItemRecipeMaster')->update(['isPopup' => 1]);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
