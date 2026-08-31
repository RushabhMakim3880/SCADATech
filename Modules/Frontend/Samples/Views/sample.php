<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => 'Sample Dashboard']) ?>

<div class="row">

    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/widgetStats', [
            'bgClass' => 'bg-blue',
            'icon'    => 'fa fa-desktop',
            'title'   => 'TOTAL VISITORS',
            'subtitle' => 'Today',
            'value'   => '<b><span class="counter apiAutoLoad" data-tagid="totalVisitors" data-endpoint="api/samples/dashboard" data-format="number">-</span></b>',
            'link'    => 'javascript:;',
        ]); ?>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/widgetStats', [
            'bgClass' => 'bg-red',
            'icon'    => 'fa fa-desktop',
            'title'   => 'TOTAL INQUIRIES',
            'subtitle' => 'Today',
            'value'   => '<b><span class="counter apiAutoLoad" data-tagid="totalInq" data-endpoint="api/samples/dashboard" data-format="currency">-</span></b>',
            'link'    => 'javascript:;',
        ]); ?>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/widgetStats', [
            'bgClass' => 'bg-green',
            'icon'    => 'fa fa-desktop',
            'title'   => 'TOTAL SALES',
            'subtitle' => 'Today',
            'value'   => '<b><span class="counter apiAutoLoad" data-tagid="totalSales" data-endpoint="api/samples/dashboard" data-format="html">-</span></b>',
            'link'    => 'javascript:;',
        ]); ?>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/widgetStats', [
            'bgClass' => 'bg-orange',
            'icon'    => 'fa fa-desktop',
            'title'   => 'TOTAL Orders',
            'subtitle' => 'Today',
            'value'   => '<b><span class="counter apiAutoLoad" data-tagid="totalOrders" data-endpoint="api/samples/dashboard">-</span></b>',
            'link'    => 'javascript:;',
        ]); ?>
    </div>
    <!-- END col-3 -->
</div>

<div class="row">
    <div class="col-md-6">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Various Text Examples']]) ?>

        <h1><span class="apiAutoLoad" data-tagid="h1text" data-endpoint="api/samples/dashboard"></span></h1>
        <h2><span class="apiAutoLoad" data-tagid="h2text" data-endpoint="api/samples/dashboard">-</span></h2>
        <h3><span class="apiAutoLoad" data-tagid="h3text" data-endpoint="api/samples/dashboard">-</span></h3>
        <h4><span class="apiAutoLoad" data-tagid="h4text" data-endpoint="api/samples/dashboard">-</span></h4>
        <h5><span class="apiAutoLoad" data-tagid="h5text" data-endpoint="api/samples/dashboard">-</span></h5>
        <h6><span class="apiAutoLoad" data-tagid="h6text" data-endpoint="api/samples/dashboard">-</span></h6>

        <p>
            <small><span class="apiAutoLoad" data-tagid="text1" data-endpoint="api/samples/dashboard">-</span></small><br>
            <em><span class="apiAutoLoad" data-tagid="text2" data-endpoint="api/samples/dashboard">-</span></em><br>
            <span class="semi-bold"><span class="apiAutoLoad" data-tagid="text3" data-endpoint="api/samples/dashboard">-</span></span><br>
            <strong><span class="apiAutoLoad" data-tagid="text4" data-endpoint="api/samples/dashboard">-</span></strong>
        </p>


        <p class="apiAutoLoad" data-tagid="para1" data-endpoint="api/samples/dashboard">-</p>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

    <div class="col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Simple Table Example']]) ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover apiAutoLoad" data-tagid="usersTable" data-endpoint="api/users/getList">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Role</th>
                        <th data-format="datetime">Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Line Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='lineChart' data-tagid="lineChart" data-chartzoom="true" data-chartsave='true' data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>
<div class="row">

    <div class="col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Bar Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='barChart' data-tagid="lineChart2" data-chartzoom="true" data-chartsave='true' data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

    <div class="col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Pie Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='pieChart' data-tagid="lineChart" data-chartzoom="true" data-chartsave='true' data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Pareto Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='paretoChart' data-tagid="lineChart2" data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

    <div class="col-md-6">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Guage Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='guageChart' data-startangle='210' data-endangle='-30' data-tagid="guageCharts" data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<div class="row">

    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Cartesian Heatmap Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='cartesianChart' data-tagid="lineChart2" data-rangetype='piecewise' data-split='5' data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Calendar Heatmap Chart Example']]) ?>

        <!--------------------------------------------------------
        NOTE: to submit any form data together with auto api, set data-endpoint adn data-group attribute to form element as below.
        all form data will be submitted as post data to the endpoint.
        use different group name for multiple form keeping same endpoint.
        Do not keep submit button into form to prevent normal form submission. instead use a button with class reloadView.
        use class reloadViewOnChange to reload the chart on change event of any input.
        set data-endpoint attribute to reloadView or reloadViewOnChange element to reload that particular endpoint only.
        --------------------------------------------------------->


        <form data-endpoint="api/samples/sampleData" data-group="filterData">
            <select id="year" class="form-select mb-3 reloadViewOnChange" name="year" data-endpoint="api/samples/sampleData">
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024" selected>2024</option>
                <option value="2025">2025</option>
            </select>
        </form>

        <!--------------------------------------------------------
        NOTE: to refresh the chart, add class reloadView to any button or link.
        --------------------------------------------------------->
        <button class="btn btn-primary reloadView" id="refreshCalender" data-endpoint="api/samples/sampleData">Refresh</button>



        <div class="apiAutoLoad" data-tagtype='calenderCartesianChart' data-rangetype='continuous' data-split='5' data-tagid="calenderChart" data-endpoint="api/samples/sampleData" style="height: 300px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Sunburst Chart Example']]) ?>

        <div class="apiAutoLoad" data-tagtype='sunBurstChart' data-tagid="sunBurstChart" data-endpoint="api/samples/sampleData" style="height: 1000px;"></div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>

<?php
/**********************************************************************************
 * Chat Application Function example code below still work in progress
 * ********************************************************************************/
?>

<style>
    /* Sidebar for users list */
    #userSidebar {
        width: 250px;
        position: fixed;
        right: 0;
        top: 0;
        bottom: 0;
        background: #f8f9fa;
        border-left: 1px solid #ccc;
        overflow-y: auto;
        padding: 10px;
        z-index: 9999;
    }

    .user-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #ddd;
    }

    .user-item.online {
        color: green;
    }

    .user-item.offline {
        color: red;
    }

    /* Chat window */
    .chat-window {
        width: 300px;
        position: fixed;
        bottom: 0;
        right: 260px;
        background: #fff;
        border: 1px solid #ccc;
        display: none;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
    }

    .chat-header {
        padding: 10px;
        background: #007bff;
        color: #fff;
        display: flex;
        justify-content: space-between;
        cursor: pointer;
    }

    .chat-body {
        height: 250px;
        overflow-y: auto;
        padding: 10px;
    }

    .chat-footer {
        padding: 10px;
        border-top: 1px solid #ccc;
    }
</style>
<!-- <div id="userSidebar">
    <h5>Users</h5>
    <div class="user-item online" data-user="John">John (Online)</div>
    <div class="user-item offline" data-user="Jane">Jane (Offline)</div>
    <div class="user-item online" data-user="Alice">Alice (Online)</div>
</div> -->

<!-- Chat Window -->
<!-- <div id="chatWindow" class="chat-window">
    <div class="chat-header">
        <span id="chatUser">Chat</span>
        <span>
            <button class="btn btn-sm btn-light minimize-chat">_</button>
            <button class="btn btn-sm btn-danger close-chat">X</button>
        </span>
    </div>
    <div class="chat-body">
        <div class="chat-messages"></div>
    </div>
    <div class="chat-footer">
        <input type="text" id="chatInput" class="form-control" placeholder="Type a message...">
    </div>
</div> -->