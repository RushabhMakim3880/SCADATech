/*************************************************************************
   Dynamic Datatable Code to manage all tables centrally throughout app.
**************************************************************************/
let reorderTimeout;
const debounceDelay = 1000; // Adjust delay (in milliseconds) as needed

let urlFilters = getQueryParams();

$(document).ready(function () {

    if ($('.manageDataTable').length) {
        showPreloader();
    }

    applyDatatable();

    // fix to move dropdown in datatable outside the responsive scrollable div, to make it fully visible
    $('body').on('show.bs.dropdown', '.manageDataTable', function (e) {
        var $dropdownMenu = $(e.target).parent().find('.dropdown-menu');

        if ($dropdownMenu.length && !$dropdownMenu.hasClass('manageDatatableFilterDD')) {
            // Append dropdown menu to body to prevent clipping
            $('body').append($dropdownMenu.detach());

            var eOffset = $(e.target).offset();

            $dropdownMenu.css({
                position: 'absolute',
                top: eOffset.top + $(e.target).outerHeight(),
                left: eOffset.left
            });
        }
    });
});

function applyDatatable() {
    // Initialize all containers with the class `manageDataTable`
    $('.manageDataTable').each(function () {
        const tableContainer = $(this);

        // Fetch endpoints from data attributes
        const configEndpoint = tableContainer.data('configendpoint');
        const dataEndpoint = tableContainer.data('endpoint');
        const features = tableContainer.data('features') ?? {};
        const addEndpoint = tableContainer.data('addendpoint') ?? null;
        const addType = tableContainer.data('addtype') || 'popup';

        // Initialize the table
        initializeDataTable(tableContainer, configEndpoint, dataEndpoint, features, addEndpoint, addType);
    });
}

function initializeDataTable(container, configEndpoint, dataEndpoint, features, addEndpoint, addType) {

    // Fetch column configuration
    const module = container.data('module') + "_columns";

    apiCall('GET', configEndpoint + '/' + module)
        // .then(response => response.json())
        .then(response => {
            const config = response.data;

            // Create dynamic table element
            const tableId = `datatable-${Math.random().toString(36).substring(2, 15)}`;

            //save column settings in container for reuse.
            container.data('columnSettings', config);

            const columns = config.columns.map(col => ({
                data: col.name,
                name: col.title,
                visible: col.visible,
                orderable: col.orderable !== false,
                searchable: col.searchable !== false
            }));

            var defaultOrderColumn = response.data.defaultOrderColumn;
            var defaultOrderDirection = response.data.defaultOrderDirection;
            var titleColumn = response.data.titleColumn;
            if (!titleColumn) {
                titleColumn = columns[1].data;
            }

            defaultOrderColumnIndex = columns.findIndex(col => col.data === defaultOrderColumn);

            defaultOrderColumn = defaultOrderColumn ? defaultOrderColumn : 0;
            defaultOrderDirection = defaultOrderDirection ? defaultOrderDirection : 'asc';

            container.html(`
                <table id="${tableId}" class="table table-striped table-bordered table-sm text-nowrap" style="width:100%" 
                    data-features='${JSON.stringify(features)}'
                    data-addendpoint='${addEndpoint}'
                    data-addtype='${addType}'
                    >
                    <thead>
                        <tr>${columns.map(col => `<th>${col.name}</th>`).join('')}</tr>
                    </thead>
                    <tfoot>
                        <tr>${columns.map(() => `<th style="text-align: right;"></th>`).join('')}</tr>
                    </tfoot>
                    <tbody></tbody>
                </table>
            `);

            // Initialize DataTable
            const table = $(`#${tableId}`).DataTable({
                // responsive: {
                //     details: {
                //         display: DataTable.Responsive.display.modal({
                //             header: function (row) {
                //                 var data = row.data();
                //                 return 'Details for: ' + data[titleColumn];
                //             }
                //         }),
                //         renderer: DataTable.Responsive.renderer.tableAll()
                //     }
                // },
                processing: true,
                serverSide: true,
                pageLength: parseInt(window.appSettings.manageTablePageSize, 10),
                lengthMenu: window.appSettings.manageTablePageSizeList.split(",").map(value => parseInt(value, 10)),
                colReorder: {
                    columns: ':not(:first-child)'
                },
                //default ordering by first column
                order: [[defaultOrderColumnIndex, defaultOrderDirection]],
                ajax: function (data, callback, settings) {
                    // Prepare the request body
                    const temp = prepareDataTablePostData(container);

                    // merge temp into d
                    $.extend(data, temp);

                    skipPreloader = true;
                    apiCall('POST', dataEndpoint, data)
                        .then(response => {
                            hidePreloader();
                            callback(response);

                            if (response.extraData) {
                                processViewData(response.extraData, null, true);
                            }
                        })
                        .catch(error => console.error('Error fetching data:', error));

                },
                // ajax: {
                //     url: base_url + dataEndpoint,
                //     type: 'POST',
                //     headers: {
                //         'Authorization': 'Bearer ' + localStorage.getItem('jwt')
                //     },
                //     contentType: 'application/json',
                //     data: function (d) {
                //         temp = prepareDataTablePostData(container);

                //         // merge temp into d
                //         $.extend(d, temp);

                //         return JSON.stringify(d);
                //     }
                // },
                columns: columns,
                initComplete: function () {
                    // Target the search input dynamically
                    const searchInput = container.find('input[type="search"]'); // Locate the search input
                    if (searchInput.length) {
                        // Unbind default behavior and bind custom Enter key behavior
                        searchInput.unbind();
                        searchInput.on('keydown', function (e) {
                            // console.log(e.key);
                            if (e.key === 'Enter') {
                                // console.log("Enter key pressed");
                                table.search(this.value).draw(); // Trigger search on Enter
                            }
                        });
                    }

                    $(table.table().node()).parent().addClass('table-responsive');

                },
                drawCallback: function () {
                    addFilterRow(table);
                    updateFooterTotals(table, columns);
                },
                // callback for each row to set background color.
                "createdRow": function (row, data, dataIndex) {

                    // //dynamically insert class to dropdown of first column, to make it compatible with bootstrap modal for more details in responsive mobile screen
                    // $(row).find("td").first().find(".dropdown").addClass('datatableActionDropdown');

                    // Find any <span> inside the row with data-rowbgcolor and data-rowtextcolor attributes
                    $(row).find('span[data-rowbgcolor]').each(function () {
                        jQuery(".manageDataTable").find("table").removeClass('table-striped');
                        var rowbgcolor = $(this).data('rowbgcolor');
                        // Apply styles with !important to override Bootstrap's table-striped
                        $(row).css({
                            'background-color': rowbgcolor,
                        });
                    });
                }
            });

            table.on('column-reorder', function (e, settings, details) {
                const columnOrder = table.colReorder.order(); // Get the new column order
                // get all column config in same order
                const reorderedColumnsConfig = columnOrder.map(index => config.columns[index]);
                // console.log(reorderedColumnsConfig);

                // save reordered columns
                // Clear the previous timeout if the event fires again within the delay
                clearTimeout(reorderTimeout);

                // Set a new timeout to call your function after the user stops dragging
                reorderTimeout = setTimeout(function () {
                    // This code runs only after user finished dragging (no new event for delay period)
                    saveUserColumnSettings(table);
                }, debounceDelay);


                initializeColumnToggle(table, reorderedColumnsConfig);

                // Add filter row initially
                addFilterRow(table);

            });

            // // Handle filter changes
            // container.find('thead:eq(1) tr .column-filter').on('change', function () {
            //     table.ajax.reload();
            // });

            initializeColumnToggle(table, config.columns);

            // Add filter row initially
            addFilterRow(table);
        })
        .catch(error => console.error('Error fetching column configuration:', error));
}

function initializeColumnToggle(table, columns) {

    const features = $(table.table().node()).data('features');
    const addEndpoint = $(table.table().node()).data('addendpoint');
    const addType = $(table.table().node()).data('addtype') || 'popup';

    const module = $(table.table().node()).closest('.manageDataTable').data('module');

    $columnControl = $(`<div class="btn-group dtExtraOptions" id="${module}_columnSettings">
            <a href="javascript:;" class="btn btn-sm btn-primary ms-1 dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-cog fa-lg"></i></a>
            <ul class="dropdown-menu dropdown-menu-end"></ul>
        </div>`);

    $addButton = null;

    if (addEndpoint) {
        if (addType === 'popup') {
            $addButton = $(`<a href='javascript:;' data-stricttype='strict' data-endpoint="${addEndpoint}" data-size="xl" data-title='Add New Item' class="btn btn-sm ms-1 btn-warning apiPopup dtExtraOptions"><i class="fa fa-plus-circle  fa-lg"></i></a>`);
        } else if (addType === 'normal') {
            $addButton = $(`<a href="${base_url + addEndpoint}" class="btn btn-sm btn-warning ms-1 dtExtraOptions"><i class="fa fa-plus-circle fa-lg"></i></a>`);
        }
    }

    $downloadControl = $(`<div class="btn-group dtExtraOptions">
            <a href="javascript:;" class="btn btn-sm btn-success ms-1 dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-download fa-lg"></i></a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddCopyBtn text-decoration-none d-block"><i class="fa fa-copy"></i> Copy Data</a></li>
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddPrintBtn text-decoration-none d-block"><i class="fa fa-print"></i> Print</a></li>
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddPrintSlimBtn text-decoration-none d-block"><i class="fa fa-print"></i> Print Slim</a></li>
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddDownLoadCsvBtn text-decoration-none d-block"><i class="fa fa-file-csv"></i> CSV</a></li>
                <li class="dropdown-item"><a href="javascript:;" style="font-size:medium;" class="ddDownLoadExcelBtn text-decoration-none d-block"><i class="fa fa-file-excel"></i> EXCEL</a></li>
                
            </ul>
        </div>`);


    // Use the DataTable object to find the search bar container
    const searchContainer = table.table().container();
    const searchBar = $(searchContainer).find('.dt-search');

    // Create and insert the custom button after the search bar
    searchBar.find('.dtExtraOptions').remove(); // Remove any existing buttons

    if ($addButton) {
        searchBar.append($addButton);
    }

    if (features.columnControls)
        searchBar.append($columnControl);

    if (features.export)
        searchBar.append($downloadControl);


    // $downloadControl.on("click", ".ddDownLoadCsvBtn", function () {
    //     downloadData(table, "CSV");
    // });

    // $downloadControl.on("click", ".ddDownLoadExcelBtn", function () {
    //     downloadData(table, "EXCEL");
    // });
    // $downloadControl.on("click", ".ddPrintBtn", function () {
    //     console.log("ddPrintBtn");
    //     downloadData(table, "PRINT");
    // });

    // // ddCopyBtn
    // $downloadControl.on("click", ".ddCopyBtn", function () {
    //     downloadData(table, "CLIPBOARD");
    // });

    //above code is commented and moved to below code to handle dropdown detached from responsive scrollable div
    jQuery("body").on("click", ".ddDownLoadCsvBtn", function () {
        downloadData(table, "CSV");
    });

    jQuery("body").on("click", ".ddDownLoadExcelBtn", function () {
        downloadData(table, "EXCEL");
    });

    jQuery("body").on("click", ".ddPrintBtn", function () {
        downloadData(table, "PRINT");
    });

    // ddPrintSlimBtn
    jQuery("body").on("click", ".ddPrintSlimBtn", function () {
        downloadData(table, "PRINTSLIM");
    });

    // ddCopyBtn
    jQuery("body").on("click", ".ddCopyBtn", function () {
        downloadData(table, "CLIPBOARD");
    });



    // console.log("initializeColumnToggle");


    const columnToggleContainer = $(`#${module}_columnSettings .dropdown-menu`);


    // Populate the toggle UI dynamically
    columnToggleContainer.empty(); // Clear any existing elements
    columns.forEach((col, index) => {

        let disabled = "";
        if (col.visibleControl === false) {
            disabled = "disabled";
        }

        if (col.visible) { // Only create toggles for initially visible columns
            columnToggleContainer.append(`
                <li class='dropdown-item'>
                <label class="d-block" role="button">
                    <input ${disabled} type="checkbox" class="toggle-column" data-column="${index}" checked> ${col.title}
                </label>
                </li>
            `);
        } else {
            columnToggleContainer.append(`
                <li class='dropdown-item'>
                <label class="d-block" role="button">
                    <input ${disabled} type="checkbox" class="toggle-column" data-column="${index}"> ${col.title}
                </label>
                </li>
            `);
        }
    });

    //add option to reset column settings
    columnToggleContainer.append(`
                <li class='dropdown-item'>
                    <a href='javascript:;' class='text-danger d-block text-decoration-none resetColumnSettings apiAction' data-reload='true' data-confirm='Are you sure to reset to default column settings for this table?' data-endpoint='api/system/resetManageTableColumnSettings/${module}'><i class="fa fa-redo-alt"></i> Reset</a>
                </li>
            `);

    // Unbind previous event listeners to avoid duplicate callbacks
    columnToggleContainer.off('change', '.toggle-column');

    // Attach event listeners to toggle column visibility
    columnToggleContainer.on('change', '.toggle-column', function () {
        const column = table.column($(this).data('column'));
        const isChecked = $(this).is(':checked');
        column.visible(isChecked); // Show or hide the column

        // Save column visibility settings
        // Clear the previous timeout if the event fires again within the delay
        clearTimeout(reorderTimeout);

        // Set a new timeout to call your function after the user stops dragging
        reorderTimeout = setTimeout(function () {
            // This code runs only after user finished dragging (no new event for delay period)
            saveUserColumnSettings(table);
        }, debounceDelay);


        // Fix column filters alignment
        table.draw(false); // Redraw the table without resetting paging
    });
}

function saveUserColumnSettings(table) {

    const module = $(table.table().node()).closest('.manageDataTable').data('module') + "_columns";

    const columnSettings = table.columns().indexes().toArray().map(index => {
        const column = table.settings()[0].aoColumns[index];
        return {
            name: column.mData,
            visible: column.bVisible,
        };
    });

    apiCall('POST', "api/users/saveUserSettings/" + module, columnSettings);
}


function addFilterRow(table) {

    const existingFilterRow = $(table.table().header()).find('tr.filter-row');
    if (existingFilterRow.length) {
        //remove existing filter row
        existingFilterRow.remove();
        // return;
    }

    const columnSettings = $(table.table().node()).closest('.manageDataTable').data('columnSettings');

    // Access the table header
    const thead = $(table.table().header()).closest('thead');

    // Remove existing filter row if it exists
    thead.find('tr.filter-row').remove();

    // Create a new header row for filters
    const filterRow = $('<tr class="filter-row d-md-table-row"></tr>');

    // Iterate over each column in the current order
    let storedFilters = $(table.table().node()).data('selectedFilters') || {};

    // console.log("storedFilters", storedFilters);
    // Append URL filters to mark filters as selected
    Object.keys(urlFilters).forEach(key => {
        //if is array
        if (Array.isArray(urlFilters[key])) {
            storedFilters[key] = urlFilters[key].join(",");
        }
        else {
            storedFilters[key] = urlFilters[key];
        }
    });

    // clean up the object to prevent repeat use
    urlFilters = {};

    table.columns().every(function () {
        const col = this;
        const colIndex = col.index();
        const colVisible = col.visible();

        // Create a table header cell for this column
        const th = $('<th></th>');

        // If the column is not visible, hide the cell to maintain column alignment
        if (!colVisible) {
            th.hide();
        } else {
            // Find corresponding column settings by matching the name
            const colName = table.settings()[0].aoColumns[colIndex].data || table.column(colIndex).dataSrc();
            const config = columnSettings.columns.find(c => c.name === colName);



            // for multi checkbox bootstrap dropdown filter
            if (config && config.filterOptions && config.filterType === 'checkbox') {
                const dd = $('<div class="btn-group column-filter" data-col="' + colName + '"><a href="javascript:;" class="btn px-2 py-1 btn-default dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-filter"></i></a><ul class="dropdown-menu dropdown-menu-end manageDatatableFilterDD"></ul></div>');
                const filterOptions = config.filterOptions;

                if (Array.isArray(filterOptions)) {
                    filterOptions.forEach(function (val) {
                        dd.find('ul').append(`<li class='dropdown-item'><label role="button" class="d-block"><input type='checkbox' name='${val}' value='${val}'></input> ${val}</label></option>`);
                    });
                } else {
                    $.each(filterOptions, function (key, val) {
                        dd.find('ul').append(`<li class='dropdown-item'><label role="button" class="d-block" ><input type='checkbox' name='${key}' value='${key}'></input> ${val}</label></option>`);
                    });
                }

                // add horizontal line with apply and reset button in dropdown
                dd.find('ul').append(`<li><hr class="dropdown-divider"></li>`);
                dd.find('ul').append(`<li class='dropdown-item'><a href="javascript:;" class="text-info d-block text-decoration-none applyFilter">Apply</a></li>`);
                dd.find('ul').append(`<li class='dropdown-item'><a href="javascript:;" class="text-warning d-block text-decoration-none resetFilter">Reset</a></li>`);

                // Restore previous selection for this column, if available
                if (storedFilters[colName]) {

                    let selectedValues = storedFilters[colName].split(",");
                    let isFilterApplied = false;
                    dd.find('input[type="checkbox"]').each(function () {
                        if (selectedValues.includes($(this).val())) {
                            $(this).prop('checked', true);
                            isFilterApplied = true;
                        }
                    });

                    if (isFilterApplied) {
                        dd.find('.btn').addClass('btn-warning');
                    }
                }

                dd.on('click', '.applyFilter', function () {
                    let selectedValues = [];
                    dd.find('input[type="checkbox"]:checked').each(function () {
                        selectedValues.push($(this).val());
                    });

                    // Retrieve existing stored filters or initialize an empty object
                    let storedFilters = $(table.table().node()).data('selectedFilters') || {};
                    // Use a consistent identifier, e.g., column name
                    storedFilters[colName] = selectedValues.join(",");
                    $(table.table().node()).data('selectedFilters', storedFilters);

                    table.ajax.reload();
                });

                dd.on('click', '.resetFilter', function () {
                    dd.find('input[type="checkbox"]').each(function () {
                        $(this).prop('checked', false);
                    });

                    // Retrieve existing stored filters or initialize an empty object
                    let storedFilters = $(table.table().node()).data('selectedFilters') || {};
                    // Use a consistent identifier, e.g., column name
                    storedFilters[colName] = "";
                    $(table.table().node()).data('selectedFilters', storedFilters);

                    table.ajax.reload();
                });

                th.append(dd);
            }
            else if (config && config.filterOptions) {
                const select = $('<select style="width:100%" data-col="' + colName + '" class="column-filter"><option value="">All</option></select>');
                const filterOptions = config.filterOptions;

                if (Array.isArray(filterOptions)) {
                    filterOptions.forEach(function (val) {
                        select.append(`<option value="${val}">${val}</option>`);
                    });
                } else {
                    $.each(filterOptions, function (key, val) {
                        select.append(`<option value="${key}">${val}</option>`);
                    });
                }

                // Restore previous selection for this column, if available
                // const storedFilters = $(table.table().node()).data('selectedFilters') || {};
                if (storedFilters[colName]) {
                    select.val(storedFilters[colName]);
                }

                select.on('change', function () {

                    // Retrieve existing stored filters or initialize an empty object
                    let storedFilters = $(table.table().node()).data('selectedFilters') || {};
                    // Use a consistent identifier, e.g., column name
                    storedFilters[colName] = $(this).val();
                    $(table.table().node()).data('selectedFilters', storedFilters);

                    table.ajax.reload();
                });

                th.append(select);

                // **New code to initialize Select2 on this select:**
                select.select2({});
            }
        }

        filterRow.append(th);
    });

    // Append the new filter row to the thead
    thead.append(filterRow);
}

// Bind the events for cusom filters , buttons and inputs
jQuery(document).ready(function () {
    jQuery("body").on("change", ".reloadDataTable", function () {
        target = jQuery(this).data('target');
        // Find the DataTable instance by the target module
        const table = $(`.manageDataTable[data-module="${target}"] table`).DataTable();
        table.ajax.reload();
    });
});


function getQueryParams(url) {
    // Use provided URL or default to current window location
    const queryString = url ? url.split('?')[1] : window.location.search.slice(1);
    const params = {};

    if (!queryString) return params;

    // Split query string into key-value pairs
    queryString.split('&').forEach(pair => {
        if (!pair) return;
        let [key, value] = pair.split('=');
        key = decodeURIComponent(key);
        key = key.replace(/\[\]$/, ''); // Remove any trailing brackets
        value = value ? decodeURIComponent(value) : '';

        // Handle multiple values for the same key as an array
        if (params[key] !== undefined) {
            if (!Array.isArray(params[key])) {
                params[key] = [params[key]];
            }
            params[key].push(value);
        } else {
            params[key] = value;
        }
    });

    return params;
}

function updateFooterTotals(table, columns) {
    const footerCells = $(table.table().footer()).find('th'); // Get footer cells
    const columnOrder = table.colReorder.order(); // Get current column order

    $(table.table().footer()).hide();

    // Clear all footer cells
    footerCells.html('');

    // Iterate through the current column order and update totals
    columnOrder.forEach((originalIndex, currentIndex) => {
        const column = columns[originalIndex]; // Map reordered index to original column
        const total = table.ajax.json()?.columnTotals?.[column.data]; // Fetch total for the column

        if (total !== undefined) {
            $(footerCells[currentIndex]).html(total); // Update the footer cell for the current column

            // show to tfoot
            $(table.table().footer()).show();
        }
    });
}



// Step 2: Function to Fetch Data and Convert to CSV
function downloadData(table, downloadType) {

    const module = $(table.table().node()).closest('.manageDataTable').data('module');
    const endpoint = $(table.table().node()).closest('.manageDataTable').data('endpoint');

    let postBody = table.ajax.params();

    // postBody = JSON.parse(postBody);
    postBody.start = 0;
    postBody.length = window.appSettings.dataExportLimit;
    postBody.downloadType = downloadType;

    // Fetch all data (adjust limit as per your API)
    apiCall('POST', endpoint, postBody).then(response => {
        if (response.data && response.data.length > 0) {

            if (downloadType == "EXCEL") {
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
            else if (downloadType == "CSV") {
                // Convert to CSV
                const csvData = convertToCsv(response.header, response.data);

                // Trigger download
                triggerCsvDownload(csvData, `${module}.csv`);
            }
            else if (downloadType == "PRINT") {
                printData(response.header, response.data);
            }
            else if (downloadType == "PRINTSLIM") {
                printData(response.header, response.data, true);
            }
            else if (downloadType == "CLIPBOARD") {
                copyToClipboard(response.header, response.data);
            }

        } else {
            mtplAlerts.show("warning", "No data found for download", "Warning");
        }
    }).catch(error => {
        console.error('Error fetching data for CSV export:', error);
    });
}

// Step 3: Utility Function to Convert JSON to CSV
function convertToCsv(headers, data) {
    const indexes = Object.keys(data[0]); // Extract keys as headers
    const rows = data.map(row => indexes.map(header => `"${row[header] || ''}"`).join(',')); // Map rows to CSV

    // Join headers and rows
    return [headers.join(','), ...rows].join('\n');
}

// Step 4: Function to Trigger CSV Download
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


function prepareDataTablePostData(container) {

    // Modify request to include filters
    let d = {};

    const module = container.closest('.manageDataTable').data('module');
    d.module = module;

    d.filters = {};

    // Append column-specific filters
    container.find('thead tr:eq(1) .column-filter').each(function (index) {

        // check if this is multi checkbox filter
        if ($(this).hasClass('btn-group')) {
            const fieldName = $(this).data('col');
            const selectedValues = $(this).find('input[type="checkbox"]:checked').map(function () {
                return $(this).val();
            }).get();

            if (selectedValues.length) {
                d.filters[fieldName] = selectedValues;
            }
        }
        else {

            const filterValue = $(this).val();
            fieldName = $(this).data('col');
            if (filterValue && fieldName) {
                d.filters[fieldName] = filterValue;
            }
        }
    });

    // merge URL filters, to send to server on initial request, for next request it will not be sent as object is getting cleared on addRowFilter function after first use.
    Object.keys(urlFilters).forEach(key => {
        d.filters[key] = urlFilters[key];
    });

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

    d.customFilters = customFilters;
    return d;
}



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


function printData(header, data, isSlim = false) {
    tableHtml = "<table><thead><tr>";
    header.forEach(col => {
        tableHtml += `<th>${col}</th>`;
    });

    tableHtml += "</tr></thead><tbody>";

    data.forEach(row => {
        tableHtml += "<tr>";
        Object.values(row).forEach(value => {
            tableHtml += `<td>${value || ''}</td>`;
        });
        tableHtml += "</tr>";
    });

    tableHtml += "</tbody></table>";

    // Open a new window
    const printWindow = window.open('', '_blank', 'width=800,height=600');

    // Write the table and a print button into the new window
    printWindow.document.open();

    if (isSlim) {
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <!--<title>Print Table</title>-->
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0px;
                    font-size:10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 1px 3px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <!--<h2>Table Print View</h2>-->
            ${tableHtml}
            <!--<button onclick="window.print()" style="margin-top: 20px; padding: 10px 20px;">Print</button>-->
            <script>
            window.print();
            </script>
        </body>
        </html>
    `);
    }
    else {
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <!--<title>Print Table</title>-->
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <!--<h2>Table Print View</h2>-->
            ${tableHtml}
            <!--<button onclick="window.print()" style="margin-top: 20px; padding: 10px 20px;">Print</button>-->
            <script>
            window.print();
            </script>
        </body>
        </html>
    `);
    }


    printWindow.document.close();
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
