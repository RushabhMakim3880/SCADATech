<h1 class="page-header text-center screenTitle"><i class="fa fa-file"></i> Program</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <div class="row mb-3">
        <div class="col-8">
            <div class="prepareForProgramAlign">
                <input type="text" id="jobCardSearch" class="form-control mb-2 float-end" style="max-width:300px;" placeholder="Search Job Cards...">
                <h2>Pending Job Cards</h2>

                <style>
                    .table-scroll-wrapper {
                        max-height: 700px;
                        overflow-y: auto;
                        border: 1px solid #dee2e6;
                        border-radius: 0.375rem;
                        width: 100%;
                    }

                    .table-scroll-wrapper table {
                        margin-bottom: 0;
                    }

                    .table-scroll-wrapper thead th {
                        position: sticky;
                        top: 0;
                        background-color: #fff;
                        z-index: 10;
                        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
                    }
                </style>
                <div class="table-scroll-wrapper">
                    <table class="table table-sm table-bordered" id="pendingJobcardsTable">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Width (A,B)</th>
                                <th>Thickness</th>
                                <th>Material</th>
                                <th>Program Length</th>
                                <th>Required Quantity</th>
                                <th>Completed Quantity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="pendingJobcardsTableBody">
                            <!-- Dynamic content will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="prepareForProgramAlign">
                <h2>Prepare Items Here</h2>
                <div id="jobcardDetailsBox" class="p-2 border rounded-3 shadow-sm" style="min-height: 300px;">
                    <!-- <div class="alert alert-info">Select a job card to prepare items.</div> -->

                    <table class="table table-sm align-middle m-0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th style="width:160px;">Qty</th>
                                <th style="width:100px;">Reverse?</th>
                            </tr>
                        </thead>
                        <tbody id="selectedJobsBody">
                            <!-- rows appended dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <a href="javascript:;" data-stricttype="strict" data-endpoint="jobCards/addJobcard" data-size="md" data-title="Add New Item" class="btn btn-primary apiPopup"><i class="fa fa-plus-circle fa-2x"></i><br>New Jobcard</a>
    <button class="btn btn-warning float-end" id="startJobcardButton"><i class="fas fa-arrow-alt-circle-right fa-2x"></i><br>Program Align...</button>
    <a href="javascript:;" data-stricttype="strict" data-endpoint="ItemRecipeMaster/manageItemrecipemaster" data-size="xxl" data-title="Programs" class="btn btn-primary apiPopup"><i class="fa fa-list fa-2x"></i><br>Manage Programs</a>

</div>