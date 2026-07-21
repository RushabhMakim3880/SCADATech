<?php

namespace Modules\Backend\GlobalSearchLogic\Services;



class SearchProvider
{
    public function run(string $query): string
    {
        // Placeholder for project-specific logic
        // You can query multiple tables, apply role-wise filters etc.



        // $results = [    
        //     [
        //         'title' => 'Client - John Doe',
        //         'type' => 'Client',
        //         'link' => '/clients/view/123',
        //     ],
        //     [
        //         'title' => 'Invoice #INV2025-001',
        //         'type' => 'Invoice',
        //         'link' => '/invoices/view/1234',
        //     ],
        // ];

        // $data['results'] = $results;

        $db = db_connect();

        $results = $db->query("
            SELECT 
                CONCAT(firstName, ' ', lastName) AS title,
                email,
                mobile,
                'User' AS type
            FROM userMaster
            WHERE 
                tenantId = 1 AND 
                (
                    firstName LIKE ? OR 
                    lastName LIKE ? OR 
                    email LIKE ? OR 
                    mobile LIKE ?
                )
            LIMIT 20
        ", ["%$query%", "%$query%", "%$query%", "%$query%"])->getResultArray();

        $data['results'] = $results;

        // Adjust this path based on where the view file is located
        return view('\Modules\Backend\System\Views\globalSearch', $data);
    }
}
