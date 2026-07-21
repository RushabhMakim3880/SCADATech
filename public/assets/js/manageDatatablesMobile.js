// Mobile Card DataTable Engine
$(document).ready(function () {

    jQuery("body").on("change", ".reloadDataTable", function () {
        target = jQuery(this).data('target');
        if (target) {
            $(`.manageDataTable[data-module="${target}"]`).data('reload')?.();
        }
        else {
            $(`.manageDataTable`).data('reload')?.();
        }

    });

    $('.manageDataTable').each(function (index, element) {
        const $container = $(element);
        const module = $container.data('module');
        const rawConfigEndpoint = $container.data('configendpoint');
        const configEndpoint = rawConfigEndpoint.replace(/\/$/, '') + '/' + module;
        const dataEndpoint = $container.data('endpoint');
        const addEndpoint = $container.data('addendpoint');
        const addType = $container.data('addtype') || 'popup';
        const features = $container.data('features') || {};
        const uniqueId = 'mdt_' + index + '_' + Math.floor(Math.random() * 100000);

        let config = {};
        let filters = {};
        let searchValue = '';
        let orderColumn = 0;
        let orderDir = 'asc';
        let pageSize = parseInt(appSettings.manageTablePageSize || 10);
        let currentPage = 0;
        let totalRecords = 0;
        let filteredRecords = 0;
        let isLoading = false;
        let totalLoaded = 0;
        let columns = [];

        // Attach reload method to the container
        $container.data('reload', function () {
            resetAndLoad();
        });

        const pageSizeList = (appSettings.manageTablePageSizeList || '10,25,50').split(',').map(x => parseInt(x.trim()));
        const pageSizeOptions = pageSizeList.map(val => `<option value="${val}" ${val === pageSize ? 'selected' : ''}>${val}</option>`).join('');

        $downloadControl = `<div class="btn-group dtExtraOptions">
            <a href="javascript:;" class="btn btn-outline-secondary btn-success dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-download fa-lg"></i></a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddCopyBtn text-decoration-none d-block"><i class="fa fa-copy"></i> Copy Data</a></li>
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddDownLoadCsvBtn text-decoration-none d-block"><i class="fa fa-file-csv"></i> CSV</a></li>
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddDownLoadExcelBtn text-decoration-none d-block"><i class="fa fa-file-excel"></i> EXCEL</a></li>
            </ul>
        </div>`;

        $addButton = '';
        if (addEndpoint) {
            if (addType === 'popup') {
                $addButton = `<a href='javascript:;' data-stricttype='strict' data-endpoint="${addEndpoint}" data-title='Add New Item' class="btn btn-outline-success mdt-add-btn apiPopup"><i class="fa fa-plus-circle fa-lg"></i></a>`;
            } else if (addType === 'normal') {
                $addButton = `<a href="${base_url + addEndpoint}" class="btn btn-outline-success mdt-add-btn"><i class="fa fa-plus-circle fa-lg"></i></a>`;
            }
        }

        $container.html(`
        <div class="mdt-toolbar d-flex gap-2 flex-wrap mb-2 justify-content-center align-items-center">
          <input type="text" class="form-control mdt-search" placeholder="Search..." />
          <button class="btn btn-outline-secondary mdt-filter-btn"><i class="fa fa-filter fa-lg"></i></button>
          <button class="btn btn-outline-secondary mdt-sort-btn"><i class="fa fa-sort fa-lg"></i></button>
          <select class="form-select w-auto mdt-pagesize">${pageSizeOptions}</select>
          <button class="btn btn-outline-danger mdt-reset-btn"><i class='fa fa-refresh fa-lg'></i></button>
          ${$addButton}
          ${$downloadControl}
        </div>
        <div class="mdt-summary small text-muted mb-2"></div>
        <div class="mdt-cards"></div>
        <div class="mdt-pagination d-flex justify-content-center mt-3"></div>
        <div class="mdt-loader text-center py-3" style="display:none;"><div class="spinner-border"></div></div>
      `);

        const $paginationContainer = $container.find('.mdt-pagination');
        const $cards = $container.find('.mdt-cards');
        const $loader = $container.find('.mdt-loader');
        const $filterBtn = $container.find('.mdt-filter-btn');
        const $sortBtn = $container.find('.mdt-sort-btn');
        const $summary = $container.find('.mdt-summary');

        skipPreloader = true;
        apiCall('GET', configEndpoint, {}, false).then(res => {
            skipPreloader = false;
            if (!res.status) return;
            config = res.data;
            orderColumn = config.defaultOrderColumn || '';
            orderDir = config.defaultOrderDirection || 'asc';

            columns = (config.columns || []).map(col => {
                const newCol = { ...col };
                newCol.data = col.name;
                newCol.name = col.title;
                return newCol;
            });

            buildFilterModal();
            buildSortModal();
            prepareDownloadBtns();
            loadData();
        });

        function buildFilterModal() {

            const modalId = uniqueId + '_filter';

            // remove existing filter modal if any
            const existingModal = $(`#${modalId}`);
            if (existingModal.length) {
                existingModal.remove();
            }


            let body = '';
            (config.columns || []).forEach(col => {
                if (col.filterType === 'checkbox') {
                    const opts = Object.entries(col.filterOptions).map(([val, label]) => `<option value="${val}">${label}</option>`).join('');
                    body += `<div class='mb-2'><label>${col.title}</label><select class='form-select select2' name='${col.name}[]' multiple>${opts}</select></div>`;
                } else if (col.filterType) {
                    let opts = '';
                    if (Array.isArray(col.filterOptions)) {
                        opts = col.filterOptions.map(val => `<option value="${val}">${val}</option>`).join('');
                    } else if (typeof col.filterOptions === 'object') {
                        opts = Object.entries(col.filterOptions).map(([val, label]) => `<option value="${val}">${label}</option>`).join('');
                    }
                    body += `<div class='mb-2'><label>${col.title}</label><select class='form-select' name='${col.name}'><option value=''>-- All --</option>${opts}</select></div>`;
                }
            });

            $('body').append(`
          <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Filter</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><form id="${modalId}_form">${body}</form></div>
                <div class="modal-footer">
                  <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Cancel</button>
                  <button class="btn btn-primary" type="button" id="${modalId}_apply">Apply</button>
                </div>
              </div>
            </div>
          </div>
        `);

            applyUiLibrary();

            $filterBtn.attr('data-bs-toggle', 'modal').attr('data-bs-target', `#${modalId}`);
            $(document).on('click', `#${modalId}_apply`, function () {
                filters = {};
                const formData = new FormData(document.getElementById(`${modalId}_form`));
                formData.forEach((value, key) => {
                    if (key.endsWith('[]')) {
                        key = key.replace('[]', '');
                        if (!filters[key]) filters[key] = [];
                        filters[key].push(value);
                    } else {
                        if (value) filters[key] = value;
                    }
                });
                $filterBtn.toggleClass('btn-warning', Object.keys(filters).length > 0);
                resetAndLoad();
                $(`#${modalId}`).modal('hide');
            });
        }

        function buildSortModal() {
            const modalId = uniqueId + '_sort';

            // remove existing sort modal if any
            const existingModal = $(`#${modalId}`);
            if (existingModal.length) {
                existingModal.remove();
            }

            const sortOptions = (config.columns || []).filter(c => c.orderable).map(c => `<option value="${c.name}">${c.title}</option>`).join('');
            $('body').append(`
          <div class="modal fade" id="${modalId}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Sort</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                  <select class='form-select mb-2' id='${modalId}_col'>${sortOptions}</select>
                  <select class='form-select' id='${modalId}_dir'>
                    <option value='asc'>Ascending</option>
                    <option value='desc'>Descending</option>
                  </select>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Cancel</button>
                  <button class="btn btn-primary" type="button" id="${modalId}_apply">Apply</button>
                </div>
              </div>
            </div>
          </div>
        `);

            $sortBtn.attr('data-bs-toggle', 'modal').attr('data-bs-target', `#${modalId}`);
            $(document).on('click', `#${modalId}_apply`, function () {
                orderColumn = $(`#${modalId}_col`).val();
                orderDir = $(`#${modalId}_dir`).val();
                $sortBtn.toggleClass('btn-warning', true);
                pageSize = parseInt($container.find('.mdt-pagesize').val());
                resetAndLoad();
                $(`#${modalId}`).modal('hide');
            });
        }

        $container.find('.mdt-search').on('input', function () {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                searchValue = $(this).val();
                resetAndLoad();
            }, 300);
        });

        $container.find('.mdt-pagesize').on('change', function () {
            pageSize = parseInt($(this).val());
            resetAndLoad();
        });

        $container.find('.mdt-reset-btn').on('click', function () {
            filters = {};
            searchValue = '';
            orderColumn = config.defaultOrderColumn;
            orderDir = config.defaultOrderDirection;
            currentPage = 0;
            totalLoaded = 0;
            $container.find('.mdt-search').val('');
            $filterBtn.removeClass('btn-warning');
            $sortBtn.removeClass('btn-warning');

            buildFilterModal();
            buildSortModal();
            resetAndLoad();
        });

        function loadData() {
            skipPreloader = true;
            if (isLoading) return;
            isLoading = true;
            $loader.show();

            const ordColumnIndex = columns.findIndex(obj => obj.data === orderColumn) || 0;

            const postData = {
                draw: 1,
                columns: columns,
                order: [{ column: ordColumnIndex, dir: orderDir }],
                start: currentPage * pageSize,
                length: pageSize,
                search: { value: searchValue, regex: false, fixed: [] },
                module: module,
                filters: filters,
                customFilters: collectCustomFilters(module)
            };

            apiCall('POST', dataEndpoint, postData, false).then(res => {
                skipPreloader = false;
                totalRecords = parseInt(res.recordsTotal || 0);
                filteredRecords = parseInt(res.recordsFiltered || 0);
                const data = res.data || [];

                totalLoaded += data.length;

                let start = currentPage * pageSize + 1;
                let end = (features.pagination || 'auto') === 'auto' ? totalLoaded : start + data.length - 1;
                if (data.length === 0) {
                    start = 0;
                    end = 0;
                }

                if ((features.pagination || 'auto') === 'auto') {
                    if (filteredRecords < totalRecords) {
                        $summary.text(`Showing ${start} to ${end} of ${filteredRecords} entries (filtered from ${totalRecords} total)`);
                    } else {
                        $summary.text(`Showing 1 to ${end} of ${filteredRecords} entries`);
                    }
                }
                else {
                    if (filteredRecords < totalRecords) {
                        $summary.text(`Showing ${start} to ${end} of ${filteredRecords} entries (filtered from ${totalRecords} total)`);
                    }
                    else {
                        $summary.text(`Showing ${start} to ${end} of ${totalRecords} entries`);
                    }
                }

                if (data.length === 0 && currentPage === 0) {
                    $cards.html(`<div class="alert alert-warning">No records found.</div>`);
                    $paginationContainer.html('');
                    return;
                }

                data.forEach((row, i) => {

                    let mobileView = res.mobileView || [];
                    $cards.append(updateCardView(mobileView, i, row, currentPage * pageSize + i));
                });


                if (res.extraData) {
                    processViewData(res.extraData, null, true);
                }

                if ((features.pagination || 'auto') === 'manual') buildPagination();
                currentPage++;
            }).finally(() => {
                isLoading = false;
                $loader.hide();
            });
        }

        function buildPagination() {
            const totalPages = Math.ceil(filteredRecords / pageSize);
            $paginationContainer.html('');

            const buildBtn = (label, page, disabled = false) => {
                return $(`<button class="btn btn-sm mx-1 ${disabled ? 'btn-secondary disabled' : 'btn-outline-primary'}">${label}</button>`).on('click', function () {
                    if (!disabled) {
                        currentPage = page;
                        $cards.empty();
                        window.scrollTo({ top: $container.offset().top, behavior: 'smooth' });
                        loadData();
                    }
                });
            };

            const firstPage = 0;
            const lastPage = totalPages - 1;

            $paginationContainer.append(buildBtn('First', firstPage, currentPage === 0));
            $paginationContainer.append(buildBtn('Prev', currentPage - 1, currentPage === 0));

            const center = $(`<select class="form-select form-select-sm w-auto d-inline-block mx-1">
          ${Array.from({ length: totalPages }, (_, i) => `<option value="${i}" ${i === currentPage ? 'selected' : ''}>Page ${i + 1}</option>`).join('')}
        </select>`);
            center.on('change', function () {
                currentPage = parseInt($(this).val());
                $cards.empty();
                window.scrollTo({ top: $container.offset().top, behavior: 'smooth' });
                loadData();
            });
            $paginationContainer.append(center);

            $paginationContainer.append(buildBtn('Next', currentPage + 1, currentPage >= totalPages - 1));
            $paginationContainer.append(buildBtn('Last', lastPage, currentPage >= totalPages - 1));
        }

        function resetAndLoad() {
            currentPage = 0;
            totalLoaded = 0;
            $cards.empty();
            loadData();
        }

        if ((features.pagination || 'auto') === 'auto') {
            $(window).on('scroll.' + uniqueId, function () {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100 && !isLoading && totalLoaded < filteredRecords) {
                    loadData();
                }
            });
        }

        function prepareDownloadBtns() {
            const $copyBtn = $container.find('.ddCopyBtn');
            const $csvBtn = $container.find('.ddDownLoadCsvBtn');
            const $excelBtn = $container.find('.ddDownLoadExcelBtn');

            $copyBtn.off('click').on('click', function () {
                downloadData('copy');
            });

            $csvBtn.off('click').on('click', function () {
                downloadData('csv');
            });

            $excelBtn.off('click').on('click', function () {
                downloadData('excel');
            });
        }

        function updateCardView(mobileView, i, row, index) {
            const cardId = uniqueId + '_card_' + index;

            let cardBody = '';
            let actionButtons = '';
            let rowBgColor = null;
            let rowTextColour = null;

            let titleBox1 = Object.values(row)[1] || '';
            let descriptionBox1 = Object.values(row)[2] || '';
            let titleBox2 = "";
            let descriptionBox2 = "";
            let actionBox = "";
            let statusBox = "";
            let dateBox = "";


            if (mobileView && mobileView[i]) {
                if (mobileView[i].titleBox1?.trim()) {
                    titleBox1 = mobileView[i].titleBox1;
                }
                if (mobileView[i].descriptionBox1?.trim()) {
                    descriptionBox1 = mobileView[i].descriptionBox1;
                }
                if (mobileView[i].titleBox2?.trim()) {
                    titleBox2 = mobileView[i].titleBox2;
                }
                if (mobileView[i].descriptionBox2?.trim()) {
                    descriptionBox2 = mobileView[i].descriptionBox2;
                }
                if (mobileView[i].actionBox?.trim()) {
                    actionBox = mobileView[i].actionBox;
                }

                if (mobileView[i].statusBox?.trim()) {
                    statusBox = mobileView[i].statusBox;
                }
                if (mobileView[i].dateBox?.trim()) {
                    dateBox = mobileView[i].dateBox;
                }
            }


            Object.entries(row).forEach(([k, v]) => {
                const col = columns.find(c => c.data === k);
                const label = col ? col.title : k;
                cardBody += `<tr><th>${label}</th><td>${v}</td></tr>`;

                // Extract and parse dropdown if exists
                if (typeof v === 'string' && v.includes('manageScreenActionDropdown')) {
                    const div = document.createElement('div');
                    div.innerHTML = v;
                    const dropdown = div.querySelector('ul.manageScreenActionDropdown');
                    if (dropdown) {
                        dropdown.querySelectorAll('li > a').forEach(a => {
                            a.classList.add('btn', 'btn-sm', 'btn-outline-secondary', 'me-1', 'mb-1');
                            a.classList.remove('dropdown-item');
                            actionButtons += a.outerHTML + ' ';
                        });
                    }
                }

                if (typeof v === 'string' && v.includes('data-rowbgcolor')) {
                    const div = document.createElement('div');
                    div.innerHTML = v;

                    let span = div.querySelector('span[data-rowbgcolor]');
                    if (span) {
                        rowBgColor = span.getAttribute('data-rowbgcolor');
                    }

                    span = div.querySelector('span[data-rowtextcolor]');
                    if (span) {
                        rowTextColour = span.getAttribute('data-rowtextcolor');
                    }
                }
            });

            let style = '';
            if (rowBgColor) {
                style += `background-color: ${rowBgColor};`;
            }
            if (rowTextColour) {
                style += `color: ${rowTextColour};`;
            }

            return `
            <div class="card shadow-sm mb-2">
                <div class="card-header px-2" style="${style}" data-bs-toggle="collapse" data-bs-target="#${cardId}" aria-expanded="false">
                    <div>
                        <span class="float-end ms-2">${actionBox}</span>
                        <div class="fw-bold text-truncate" style="font-size: 1.2em;">    
                        ${titleBox1}
                        </div>
                        <div class="text-muted small">
                        ${descriptionBox1}
                        </div>
                        <div class="fw-bold text-truncate">
                        ${titleBox2}
                        </div>
                        <div class="text-muted small">
                        ${descriptionBox2}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="mt-2">
                        <span class="float-end ms-2">${dateBox}</span>    
                        ${statusBox}
                    </div>
                </div>
                <div id="${cardId}" class="collapse">
                    <div class="card-body p-2">
                    <table class="table table-sm mb-2">
                        <tbody>${cardBody}</tbody>
                    </table>
                    ${actionButtons ? `<div class="d-flex flex-wrap">${actionButtons}</div>` : ''}
                    </div>
                </div>
            </div>`;
        }


        function downloadData(downloadType = 'csv') {

            const ordColumnIndex = columns.findIndex(obj => obj.data === orderColumn) || 0;

            const postData = {
                draw: 1,
                columns: columns,
                order: [{ column: ordColumnIndex, dir: orderDir }],
                start: currentPage * pageSize,
                length: pageSize,
                search: { value: searchValue, regex: false, fixed: [] },
                module: module,
                filters: filters,
                customFilters: collectCustomFilters(module)
            };

            postData.start = 0;
            postData.length = window.appSettings.dataExportLimit;
            postData.downloadType = downloadType;

            apiCall('POST', dataEndpoint, postData).then(response => {
                if (response.data && response.data.length > 0) {

                    if (downloadType == "excel") {
                        // Generate Excel XML content
                        const excelContent = generateExcelContent(response.header, response.data);

                        // Create a Blob and trigger download
                        const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = `${module}.xlsx`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                    else if (downloadType == "csv") {
                        // Convert to CSV
                        const csvData = convertToCsv(response.header, response.data);

                        // Trigger download
                        triggerCsvDownload(csvData, `${module}.csv`);
                    }
                    else if (downloadType == "copy") {
                        copyToClipboard(response.header, response.data);
                    }

                } else {
                    mtplAlerts.show("warning", "No data found for download", "Warning");
                }
            }).catch(error => {
                console.error('Error fetching data for CSV export:', error);
            });
        }
    });
});


// Function to generate Excel XML content
function generateExcelContent(headers, data) {
    // Define the XML structure
    let xml = `<?xml version="1.0"?>
        <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                  xmlns:x="urn:schemas-microsoft-com:office:excel"
                  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
        <Styles>
            <Style ss:ID="Header">
                <Font ss:Bold="1" ss:Color="#FFFFFF" />
                <Interior ss:Color="#4F81BD" ss:Pattern="Solid" />
            </Style>
        </Styles>
        <Worksheet ss:Name="Sheet1">
        <Table>`;

    // Add headers (assumes keys of the first object as headers)

    xml += '<Row>';
    headers.forEach(key => {
        xml += `<Cell ss:StyleID="Header"><Data ss:Type="String">${key}</Data></Cell>`;
    });
    xml += '</Row>';


    // Add rows
    data.forEach(row => {
        xml += '<Row>';
        Object.values(row).forEach(value => {
            xml += `<Cell><Data ss:Type="String">${value || ''}</Data></Cell>`;
        });
        xml += '</Row>';
    });

    // Close the XML tags
    xml += `</Table>
        </Worksheet>
        </Workbook>`;

    return xml;
}

function copyToClipboard(headers, data) {
    // Combine headers and data into tabular format
    const rows = [headers, ...data];
    const tabularData = rows.map(row => Object.values(row).join("\t")).join("\n");

    // Copy to clipboard using the Clipboard API
    navigator.clipboard.writeText(tabularData)
        .then(() => {
            // alert("Data copied to clipboard!");
            mtplAlerts.show("success", "Data copied to clipboard", "Success");
        })
        .catch(err => {
            console.error("Error copying to clipboard: ", err);
        });
}

function convertToCsv(headers, data) {
    const indexes = Object.keys(data[0]); // Extract keys as headers
    const rows = data.map(row => indexes.map(header => `"${row[header] || ''}"`).join(',')); // Map rows to CSV

    // Join headers and rows
    return [headers.join(','), ...rows].join('\n');
}

function triggerCsvDownload(csvContent, fileName) {
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', fileName);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);

    link.click(); // Trigger download
    document.body.removeChild(link); // Clean up
}


function collectCustomFilters(module) {
    // collect all custom filters
    let customFilters = {};
    jQuery('.dataTableCustomFilter').each(function () {
        target = jQuery(this).data('target');
        if (target == module) {

            var input = $(this); // Current input element
            var filterValue = input.val(); // Value of the element
            var fieldName = input.attr('name'); // Name attribute of the element

            if (input.is(":checkbox") || input.is(":radio")) {
                // For checkboxes and radios, only add if they are checked
                if (input.is(":checked")) {
                    customFilters[fieldName] = filterValue;
                }
            } else {
                // For other inputs, directly add the name and value
                customFilters[fieldName] = filterValue;
            }
        }
    });
    return customFilters;
}