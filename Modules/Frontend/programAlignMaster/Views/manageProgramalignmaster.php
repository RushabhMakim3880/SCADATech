<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-inverse" data-sortable="false">
            <div class="panel-body">

        <div class="row mb-3">
            <div class="col-xl-3 col-md-6">
                <div class="widget-stats bg-blue">
                    <div class="stats-icon"><i class="fa fa-clock"></i></div>
                    <div class="stats-info">
                        <h4>Total Cycle Time</h4>
                        <p><b><span class="counter apiAutoLoad" data-tagid="totalCycleTime" data-endpoint="api/programAlignMaster/getKpiData" data-format="number">-</span></b></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="widget-stats bg-green">
                    <div class="stats-icon"><i class="fa fa-list"></i></div>
                    <div class="stats-info">
                        <h4>Total Items</h4>
                        <p><b><span class="counter apiAutoLoad" data-tagid="totalItems" data-endpoint="api/programAlignMaster/getKpiData" data-format="number">-</span></b></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="widget-stats bg-orange">
                    <div class="stats-icon"><i class="fa fa-tools"></i></div>
                    <div class="stats-info">
                        <h4>Total Punches</h4>
                        <p><b><span class="counter apiAutoLoad" data-tagid="totalPunches" data-endpoint="api/programAlignMaster/getKpiData" data-format="number">-</span></b></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="widget-stats bg-red">
                    <div class="stats-icon"><i class="fa fa-marker"></i></div>
                    <div class="stats-info">
                        <h4>Total Marking</h4>
                        <p><b><span class="counter apiAutoLoad" data-tagid="totalMarking" data-endpoint="api/programAlignMaster/getKpiData" data-format="number">-</span></b></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="manageDataTable"
            data-module="programAlignMaster"
            data-configendpoint="api/programAlignMaster/getDataTableColumns"
            data-endpoint="api/programAlignMaster/getDataTableData"
            data-features='{"columnControls": true,"export": true,"pagination":"manual"}'>
        </div>

            </div>
        </div>
    </div>

</div>

<script>
    (function () {
        const moduleName = 'programAlignMaster';
        const kpiEndpoint = 'api/programAlignMaster/getKpiData';
        const dataEndpoint = 'api/programAlignMaster/getDataTableData';
        let lastKpiFilterKey = null;
        let latestTableFilters = {};

        function normalizeFilters(filters) {
            const normalizedFilters = {};

            Object.keys(filters || {}).forEach(function (fieldName) {
                const filterValue = filters[fieldName];

                if (Array.isArray(filterValue)) {
                    const selectedValues = filterValue.filter(function (value) {
                        const stringValue = String(value).trim();
                        return stringValue !== '' && stringValue.toLowerCase() !== 'all';
                    });

                    if (selectedValues.length) {
                        normalizedFilters[fieldName] = selectedValues;
                    }

                    return;
                }

                if (filterValue === null || typeof filterValue === 'undefined') {
                    return;
                }

                const stringValue = String(filterValue).trim();
                if (stringValue === '' || stringValue.toLowerCase() === 'all') {
                    return;
                }

                normalizedFilters[fieldName] = filterValue;
            });

            return normalizedFilters;
        }

        function refreshProgramAlignKpis(filters) {
            const kpiFilters = normalizeFilters(filters);
            const filterKey = JSON.stringify(kpiFilters);

            if (filterKey === lastKpiFilterKey || typeof window.apiCall !== 'function') {
                return;
            }

            lastKpiFilterKey = filterKey;
            window.apiCall('POST', kpiEndpoint, { filters: kpiFilters }).catch(function (error) {
                console.error('Error fetching program align KPI data:', error);
            });
        }

        window.addEventListener('apiSuccess', function (event) {
            const detail = event.detail || {};
            const requestData = detail.data || {};

            if (detail.endpoint === dataEndpoint && detail.method === 'POST' && requestData.module === moduleName) {
                latestTableFilters = normalizeFilters(requestData.filters || {});
                refreshProgramAlignKpis(latestTableFilters);
                return;
            }

            if (detail.endpoint === kpiEndpoint && detail.method === 'GET' && Object.keys(latestTableFilters).length) {
                lastKpiFilterKey = null;
                refreshProgramAlignKpis(latestTableFilters);
            }
        });
    })();
</script>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>
