<?php

namespace Modules\Backend\System\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DashBoardSamples extends Seeder
{
    public $priority = 6;

    public function run()
    {
        $seedName = static::class;
        $exists = $this->db->table('seedHistory')->where('seedName', $seedName)->countAllResults();
        if ($exists > 0) {
            return;
        }

        // run seeder here
        $data = [];
        $data[] = [
            "tenantId" => 1,
            "widgetName" => "KPI Card Sample",
            "htmlTemplate" => '<div class="widget-stats bg-info" style="height:100px">
    <div class="stats-icon"><i class="fa fa-desktop"></i></div>
    <div class="stats-info">
        <h4>TOTAL Users</h4>
        <p><b><span class="apiAutoLoad" data-tagid="totalUsers" data-format="number">-</span></b></p>
    </div>
</div>',
            "dataSource" => '[
    {
        "tagId": "totalUsers",
        "queries": {
            "all": "SELECT COUNT(*) AS totalUsers FROM userMaster WHERE 1"
        }
    }
]',
            "updatedAt" => date("Y-m-d H:i:s"),
            "createdAt" => date("Y-m-d H:i:s")
        ];

        $data[] = [
            "tenantId" => 1,
            "widgetName" => "Table View Sample",
            "htmlTemplate" => '<div class="panel panel-inverse" data-sortable="false">
    <div class="panel-heading">
        <h4 class="panel-title">User Table</h4>
    </div>
	<div class="panel-body">
		<table class="table table-striped apiAutoLoad" data-tagid="userTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>',
            "dataSource" => '[
    {
        "tagId": "userTable",
        "queries": {
            "all": "SELECT userId, firstName, lastName, username, email FROM `userMaster` WHERE 1"
        }
    }
]',
            "updatedAt" => date("Y-m-d H:i:s"),
            "createdAt" => date("Y-m-d H:i:s")
        ];

        $data[] = [
            "tenantId" => 1,
            "widgetName" => "Chart Sample",
            "htmlTemplate" => '<div class="panel panel-inverse" data-sortable="false">
    <div class="panel-heading">
        <h4 class="panel-title">Chart Example</h4>
    </div>
	<div class="panel-body">
		<div class="apiAutoLoad" data-tagtype="lineChart" data-tagid="userCreated" data-chartzoom="true" data-chartsave="true" style="height: 300px;"></div>
    </div>
</div>',
            "dataSource" => '[
    {
        "tagId": "userCreated",
        "queries": {
            "all": "SELECT date(createdAt) as date, count(*) as total FROM `userMaster` group by date(createdAt)"
        }
    }
]',
            "updatedAt" => date("Y-m-d H:i:s"),
            "createdAt" => date("Y-m-d H:i:s")
        ];


        $this->db->table("dashboardTemplates")->insertBatch($data);

        // Record this seeder in seedHistory
        $this->db->table("seedHistory")->insert(['seedName' => $seedName, 'runAt' => date('Y-m-d H:i:s')]);
    }
}
