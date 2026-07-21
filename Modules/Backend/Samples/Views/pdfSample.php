    <style>
        html,
        body {
            background: none !important;
            box-shadow: none !important;
        }

        @page {
            background: none !important;
        }

        body {
            font-family: 'Arial', sans-serif;
            /* background-color: rgba(255, 0, 0, 0.1); */
            margin: 20px;
        }

        .invoice-container {
            /* max-width: 800px; */
            /* background: #fff; */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .invoice-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }

        .company-logo {
            max-width: 120px;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .invoice-details {
            margin-top: 20px;
        }

        .invoice-details th {
            width: 30%;
            font-weight: bold;
            text-align: left;
        }

        .invoice-table th {
            background: #007bff;
            color: #fff;
        }

        .invoice-table td,
        .invoice-table th {
            text-align: center;
            vertical-align: middle;
        }

        .invoice-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        .btn-print,
        .btn-download {
            margin-top: 10px;
        }

        @media print {

            .btn-print,
            .btn-download {
                display: none;
            }
        }
    </style>

    <div class="invoice-container">
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <img src="<?= getLogoUrl(); ?>" alt="Company Logo" class="company-logo">
            <div class="invoice-title">INVOICE</div>
        </div>

        <div class="invoice-details row mt-4">
            <div class="col-md-6">
                <h6>From:</h6>
                <p><strong>XYZ Pvt. Ltd.</strong><br>123 Business St, City, Country<br>Email: info@xyz.com<br>Phone: +123-456-7890</p>
            </div>
            <div class="col-md-6 text-end">
                <h6>To:</h6>
                <p><strong>Client Name</strong><br>456 Client St, City, Country<br>Email: client@example.com<br>Phone: +987-654-3210</p>
            </div>
        </div>

        <div class="invoice-info mt-3">
            <table class="table">
                <tr>
                    <th>Invoice No:</th>
                    <td>#INV-00123</td>
                    <th>Date:</th>
                    <td>March 15, 2024</td>
                </tr>
                <tr>
                    <th>Due Date:</th>
                    <td>March 25, 2024</td>
                    <th>Status:</th>
                    <td><span class="badge bg-success">Paid</span></td>
                </tr>
            </table>
        </div>

        <div class="invoice-items mt-3">
            <table class="table table-bordered invoice-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $services = [
                        "Web Development Services",
                        "SEO Optimization",
                        "Content Writing",
                        "Graphic Design",
                        "Digital Marketing",
                        "Social Media Management",
                        "Email Marketing",
                        "App Development",
                        "Consulting Services",
                        "Technical Support"
                    ];

                    for ($i = 1; $i <= 20; $i++) {
                        $description = $services[array_rand($services)];
                        $qty = rand(1, 5);
                        $unitPrice = number_format(rand(100, 1000), 2);
                        $total = number_format($qty * $unitPrice, 2);
                        echo "<tr>
                                <td>{$i}</td>
                                <td>{$description}</td>
                                <td>{$qty}</td>
                                <td>\${$unitPrice}</td>
                                <td>\${$total}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Subtotal:</th>
                        <td>$1,500.00</td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Tax (5%):</th>
                        <td>$75.00</td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end">Grand Total:</th>
                        <td><strong>$1,575.00</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="invoice-footer">
            <p>Thank you for your business!</p>
        </div>

        <div class="text-center">
            <button class="btn btn-primary btn-print" onclick="window.print()">Print</button>
        </div>
    </div>