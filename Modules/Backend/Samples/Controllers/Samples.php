<?php

namespace Modules\Backend\Samples\Controllers;

use App\Controllers\ApiBaseController;
use CodeIgniter\API\ResponseTrait;


// use OpenApi\Annotations as OA;

class Samples extends ApiBaseController
{
    use ResponseTrait;

    public function index()
    {

        $userData = $this->getUserData();

        if (!hasPermission($userData->roleId, 'view_user')) {
            return $this->failForbidden('Insufficient permissions');
        }

        $users = $this->userModel->findAll();

        // Remove passwords from the response
        foreach ($users as &$user) {
            unset($user->password);
        }

        return $this->respond(['users' => $users], 200);
    }

    public function dashboard()
    {
        $data = [
            'totalVisitors' => 5000000,
            'totalInq' => 10000.259,
            'totalSales' => "<u>123</u>",
            'totalOrders' => 10,
            'h1text' => 'Hello World ' . userNameInitial($this->user->userId),
            'h2text' => 'Welcome to the Jungle',
            'h3text' => 'This is a test',
            'h4text' => 'This is a test',
            'h5text' => 'This is a test',
            'h6text' => 'This is a test',
            'text1' => 'This line of text is meant to be treated as fine print.',
            'text2' => 'rendered as italicized text',
            'text3' => 'rendered as semi bold text',
            'text4' => 'rendered as bold text',
            'para1' => 'This is a paragraph. It is meant to be multiple lines of text that are combined into one. It is meant to be read by the user and is not meant for any other purpose.',
        ];

        $response = [
            "status" => true,
            "message" => "Data fetched successfully",
            "data" => $data,
        ];

        return $this->respond($response, 200);
    }

    public function sampleData()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];


        $data = [
            "userTable" => [
                [1, "Chirag", "Patel", "admin", "asdf@asdf.com"],
                [2, "John", "Doe", "user", "alsdkfj@asdlf.com"],
                [3, "Jane", "Doe", "user", "alskdjf@aklsdf.com"],
            ],
        ];

        $data['lineChart'] = [
            "config" => [
                "title" => "Sales & Revenue",
                "xAxis" => "month",
                "xAxisLabel" => "Month",
            ],
            "data" => [
                ["month" => "January", "Revenue" => 65],
                ["month" => "February", "Revenue" => 59],
                ["month" => "March", "Revenue" => 80],
                ["month" => "April", "Revenue" => 81],
                // ["month" => "May", "Revenue" => 56],
                ["month" => "June", "Revenue" => 55],
                ["month" => "July", "Revenue" => 40],
                ["month" => "August", "Revenue" => 63],
                ["month" => "September", "Revenue" => 70],
                ["month" => "October", "Revenue" => 80],
                ["month" => "November", "Revenue" => 81],
                ["month" => "December", "Revenue" => 56],
            ]
        ];

        $data['lineChart2'] = [
            "config" => [
                "title" => "Sales & Revenue",
                "xAxis" => "month",
                "xAxisLabel" => "Month",
                "series" => ["Sales", "Revenue"],
            ],
            "data" => [
                ["month" => "January", "Sales" => 20, "Revenue" => 65, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "February", "Sales" => 30, "Revenue" => 59, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "March", "Sales" => 40, "Revenue" => 80, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "April", "Sales" => 50, "Revenue" => 81, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "May", "Sales" => 60, "Revenue" => 56, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "June", "Sales" => 70, "Revenue" => 55, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "July", "Sales" => 80, "Revenue" => 40, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "August", "Sales" => 90, "Revenue" => 63, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "September", "Sales" => 100, "Revenue" => 70, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "October", "Sales" => 110, "Revenue" => 80, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "November", "Sales" => 1000, "Revenue" => 81, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
                ["month" => "December", "Sales" => 1000, "Revenue" => 56, "Inquiry" => rand(10, 100), "Profit" => rand(1000, 5000), "Loss" => rand(100, 500)],
            ]
        ];

        $data["guageCharts"] = [
            "data" => [
                "name" => "CPU Usage",
                "unit" => "K/h",
                "value" => 75,
                "max" => 100,
                "min" => 0,
                // "thresholds" => [
                //     ["value" => 60, "color" => "green"],
                //     ["value" => 80, "color" => "yellow"],
                //     ["value" => 100, "color" => "red"]
                // ]
            ],
            "label" => "System Performance"
        ];

        $year = $jsonInput["filterData"]["year"] ?? date("Y");

        // calenderChart
        $data['calenderChart'] = [
            "config" => [
                "title" => "Sales & Revenue",
                "year" => $year,

            ],
            "data" => []
        ];

        $startDate = new \DateTime("$year-01-01");
        $endDate = new \DateTime("$year-12-31");
        while ($startDate <= $endDate) {
            $date = $startDate->format('Y-m-d'); // Format as YYYY-MM-DD
            $value = rand(10, 30); // Generate random value
            $data['calenderChart']['data'][] = [$date, $value]; // Add to the data array
            $startDate->modify('+1 day'); // Move to the next day
        }


        $data["sunBurstChart"] = [
            "config" => [
                "title" => "Revenue Breakdown"
            ],
            "data" => [
                [
                    "name" => "Region A",
                    "children" => [
                        ["name" => "Product 1", "value" => 400],
                        ["name" => "Product 2", "value" => 600]
                    ]
                ],
                [
                    "name" => "Region B",
                    "children" => [
                        [
                            "name" => "Product 3",
                            "children" => [
                                ["name" => "Variant A", "value" => 500],
                                ["name" => "Variant B", "value" => 300]
                            ]
                        ],
                        ["name" => "Product 4", "value" => 400]
                    ]
                ],
                [
                    "name" => "Region C",
                    "children" => [
                        ["name" => "Product 5", "value" => 700],
                        ["name" => "Product 6", "value" => 200]
                    ]
                ],
                [
                    "name" => "Region D",
                    "children" => [
                        [
                            "name" => "Product 7",
                            "children" => [
                                [
                                    "name" => "SubProduct 1",
                                    "value" => 200,
                                ]
                            ]
                        ],
                        ["name" => "Product 8", "value" => 500]
                    ],
                ],
                [
                    "name" => "Region E",
                    "children" => [
                        ["name" => "Product 9", "value" => 300],
                        ["name" => "Product 10", "value" => 400]
                    ]
                ]
            ]
        ];


        $response = [
            "status" => true,
            "message" => "",
            "data" => $data,
        ];

        return $this->respond($response, 200);
    }
}
