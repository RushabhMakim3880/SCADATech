const apiReadyCallbacks = []; // Registry for ready callbacks
let apiReadyState = false; // Flag to track API readiness
let apiRequests = [];
let doingAutoCrud = true; //use to disable success notification until autocrud finishes work

const apiCallbacks = []; // Centralized callback registry
let chartOptions = {};

const timeoutMinutes = 10; // minutes to keep cache
const prefix = 'formAutoCache_';

let formsToCache = [];

let clientType = null;

function detectClientType() {
    const width = window.innerWidth;
    if (width <= 768) {
        clientType = 'mobile';
    }
    else if (width <= 1024) {
        clientType = 'tablet';
    }
    else if (width > 1024) {
        clientType = 'desktop';
    }

    document.cookie = "clientType=" + clientType + "; path=/";  // frontend PHP access
    localStorage.setItem("clientType", clientType);             // JS access

    $(".pwa-bottom-nav").hide();
    if (clientType === 'mobile') {
        $(".pwa-bottom-nav").show();
    }
}

// on window resize, detect client type
window.addEventListener('resize', detectClientType);
detectClientType(); // Initial detection

$(document).ready(function () {

    // $(window).on('apiSuccess', function (e) {
    //     const { endpoint, method, data, response } = e.originalEvent.detail;

    //     console.log("API Success: ", endpoint, method, data, response);
    // });

    window.onApiReady(loadView);
    prepareFormView();
    executeApiRequests(apiRequests);

    jQuery(".reloadView").click(function () {
        // skipPreloader = true;
        const endpoint = $(this).data('endpoint');
        loadView(endpoint);
    });

    jQuery(".reloadViewOnChange").click(function () {

        const endpoint = $(this).data('endpoint');

        loadView(endpoint);
    });

    jQuery(document).on('click', '.apiPrintPreview', function () {
        endpoint = $(this).data('endpoint');

        //if endpoint is provided, than execute
        if (endpoint) {
            apiCall('GET', endpoint).then(function (response) {
                // console.log(response);
                var popupWindow = window.open('', '_blank', 'width=800,height=600,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');
                popupWindow.document.write(response.data);
                popupWindow.document.close();
            });
        }
    });

    jQuery(document).on('click', '.apiFileDownload', function () {
        endpoint = $(this).data('endpoint');

        //if endpoint is provided, than execute
        if (endpoint) {
            apiCall('GET', endpoint);
        }
    });

    jQuery(document).on('click', '.apiPopup', function () {
        openApiPopup(
            $(this).data('endpoint'),
            $(this).data('title'),
            $(this).data('size'),
            $(this).data('modaltype'),
            $(this).data('stricttype')
        );
    });

    jQuery(document).on('click', '.apiDynamicModalSubmit', function (e) {
        e.preventDefault();
        const form = $(this).closest('.modal').find('form');

        // check if form has class autoCrudForm
        if (form.hasClass('autoCrudForm')) {
            //trigger submit and exit
            form.trigger('submit');
            return;
        }


        //get form action url
        const endpoint = form.attr('action');
        const modal = $(this).closest('.modal');
        const formData = getFormDataWithFiles(form[0]);
        const jsonData = formData.jsonData;
        const fileData = formData.fileData;
        const fileFound = formData.fileFound;

        // remove all alert boxes
        modal.find('.alert').remove();

        // add error box
        let errorBox = document.createElement("div");
        errorBox.classList.add("alert", "alert-danger", "mt-2", "d-none");
        modal.find('.modal-body').prepend(errorBox);

        // add successbox
        let successBox = document.createElement("div");
        successBox.classList.add("alert", "alert-success", "mt-2", "d-none");
        modal.find('.modal-body').prepend(successBox);

        if (fileFound) {
            apiCall('POST', endpoint, fileData, true).then(function (response) {
                if (response.status) {

                }
                else {

                }

                modal.modal('hide');
            });
        } else {
            apiCall('POST', endpoint, jsonData).then(function (response) {
                if (response.status) {

                    // mtplAlerts.show('success', response.message, 'Success');

                    successBox.innerHTML = response.message;
                    successBox.classList.remove("d-none");

                    const closeTimeout = response.closeTimeout;
                    if (closeTimeout > 0) {
                        setTimeout(function () {
                            modal.modal('hide');
                        }, closeTimeout);
                    }

                    const clearForm = response.clearForm;
                    if (clearForm) {
                        modal.find('form').remove();
                    }

                    const reloadDataTable = response.reloadDataTable;
                    if (reloadDataTable) {
                        if (jQuery(".manageDataTable").length) {
                            $(".manageDataTable table").DataTable?.()?.ajax?.reload?.(null, false);
                            $(".manageDataTable table").data('reload')?.();
                        }
                    }

                    const callbackFunction = response.callbackFunction;
                    const callbackData = response.callbackData;
                    if (callbackFunction) {
                        //first confirm if function is available.
                        // console.log(typeof callbackFunction);
                        if (typeof window[callbackFunction] === "function") {
                            window[callbackFunction](callbackData);
                        }
                        else {
                            mtplAlerts.show('error', "Callback function " + callbackFunction + " not found", 'Error');
                        }
                    }

                    const redirectUrl = response.redirectUrl;
                    if (redirectUrl) {
                        setTimeout(function () {
                            window.location.href = redirectUrl;
                        }, 2000);
                    }

                    // scroll back to success box
                    successBox.scrollIntoView({ behavior: "smooth", block: "center" });
                }
                else {
                    if (response.errors) {
                        let errorMessages = Object.values(response.errors).join("<br>");
                        errorBox.innerHTML = errorMessages;
                        errorBox.classList.remove("d-none");

                        // scroll back to error box
                        errorBox.scrollIntoView({ behavior: "smooth", block: "center" });
                    }
                }

                // modal.modal('hide');
            });
        }
    });

});

jQuery(document).ready(function () {


    registerApiCallback("/api/samples/dashboard/", function (data) {
        // console.log("Data received in callback 1", data);
    });

    registerApiCallback("/api/users/getList/", function (data) {
        // console.log("Data received in callback 2", data);
    });
});

function loadView(endpoint = null) {
    // Group elements by endpoint
    let elements;
    if (endpoint && endpoint != null && endpoint != "") {
        elements = $(`[data-endpoint="${endpoint}"].apiAutoLoad`);
    }
    else {
        elements = $('[data-endpoint].apiAutoLoad');
    }

    const endpointGroups = {};

    elements.each(function () {

        // ignroe if dont have data-tagid attribute for non form elements.
        if (!$(this).data('tagid') && !$(this).is('form')) {
            return;
        }

        const endpoint = $(this).data('endpoint');
        if (!endpointGroups[endpoint]) {
            endpointGroups[endpoint] = {};
        }

        //if element is form get its form data in key value pair and add to array.
        if ($(this).is('form')) {
            const varName = $(this).data('group');
            endpointGroups[endpoint][varName] = getFormDataObject(this);
        } else {
            // endpointGroups[endpoint].push($(this));
        }

    });

    // Fetch data for each endpoint
    $.each(endpointGroups, function (endpoint, formData) {
        // check if formData is blank/empty object
        if (Object.keys(formData).length === 0) {
            apiRequests.push(apiCall('GET', endpoint).then(function (response) {

                // if (response.message != "") {
                //     mtplAlerts.show('success', response.message, 'Success');
                // }
                // processApiResponse(endpoint, response.data);
            }));

        } else {

            apiRequests.push(apiCall('POST', endpoint, formData).then(function (response) {
                // if (response.message != "") {
                //     mtplAlerts.show('success', response.message, 'Success');
                // }
                // processApiResponse(endpoint, response.data);
            }));
        }
    });
}

// Function to register callbacks for specific endpoints
window.registerApiCallback = function (endpoint, callback) {

    // trim first and last slash from endpoint
    const myEndpoint = endpoint.replace(/^\/|\/$/g, '');

    if (!apiCallbacks[myEndpoint]) {
        apiCallbacks[myEndpoint] = [];
    }
    apiCallbacks[myEndpoint].push(callback);
};

function processApiResponse(endpoint, response) {
    // remove first / from endpoint
    const myEndpoint = endpoint.replace(/^\/|\/$/g, '');
    // Execute callbacks only for the registered endpoint
    Object.keys(apiCallbacks).forEach(function (registeredEndpoint) {
        if (myEndpoint.startsWith(registeredEndpoint)) {
            apiCallbacks[registeredEndpoint].forEach(function (callback) {
                callback(response);
            });
        }
    });

    // Process the response in the view
    if (response.data && typeof response.data !== 'string' && (typeof response.data === 'object' || Array.isArray(response.data))) {
        processViewData(response.data, endpoint);
    }
}

// Function to register ready callbacks
window.onApiReady = function (callback) {
    if (apiReadyState) {
        setTimeout(() => {
            callback();
        }, 500);
    } else {
        apiReadyCallbacks.push(callback);
    }
};

// Function to track API request completion
function executeApiRequests(apiRequests) {
    Promise.all(apiRequests)
        .then(results => {
            // console.log('All AJAX requests completed:', results);
            apiReadyState = true; // Mark API as ready

            // Execute all registered callbacks
            setTimeout(() => {
                apiReadyCallbacks.forEach(callback => callback());
                apiReadyCallbacks.length = 0; // Clear callbacks after execution
            }, 500);

            setTimeout(() => {
                doingAutoCrud = false; // Enable success notification after autoCrud is done
            }, 1000);
        })
        .catch(error => {
            console.error('One or more requests failed:', error);
        });
}

function processViewData(response, endpoint = null, elements = null) {
    //iterate through response object
    for (const [key, value] of Object.entries(response)) {
        //find all elements with data-tagid attribute equal to key and data-endpoint = endpoint
        let myelements = null;
        if (!elements) {
            myelements = $(`[data-tagid="${key}"][data-endpoint="${endpoint}"].apiAutoLoad`);
        }
        else {
            myelements = $(`[data-tagid="${key}"].apiAutoLoad`);
        }

        myelements.each(function () {
            const $el = $(this);

            // check if element has data-tagtype attribute
            const tagType = $el.data('tagtype');

            if (tagType === 'lineChart') {
                drawLineChart(key, value, $el);
            }
            else if (tagType === 'pieChart') {
                drawPieChart(key, value, $el);
            }
            else if (tagType === 'barChart') {
                drawBarChart(key, value, $el);
            }
            else if (tagType === 'paretoChart') {
                drawParetoChart(key, value, $el);
            }
            else if (tagType === 'guageChart') {
                drawGuageChart(key, value, $el);
            }
            else if (tagType === 'cartesianChart') {
                drawCartesianChart(key, value, $el);
            }
            else if (tagType === 'calenderCartesianChart') {
                drawCalenderCartesianChart(key, value, $el);
            }
            else if (tagType === 'sunBurstChart') {
                drawSunBurstChart(key, value, $el);
            }
            else {
                //check if element is table.
                if ($el.is('table')) {
                    const $tbody = $el.find('tbody');
                    $tbody.empty(); // Clear previous rows

                    const rows = (value.data) || value;

                    rows.forEach((row) => {
                        const $tr = $('<tr></tr>');
                        // Extract only the values from the row object
                        Object.values(row).forEach((cell) => {
                            // find column number
                            const col = Object.keys(row).find(key => row[key] === cell);
                            //find th of same column number
                            const th = $el.find('th').eq(col);

                            //check if th has data-format attribute
                            const format = th.data('format');
                            const currency = th.data('currency') || 'INR';
                            const country = th.data('country') || 'IN';

                            const formatedValue = formatValue(cell, format, currency, country);

                            $('<td></td>').html(formatedValue).appendTo($tr);
                        });
                        $tbody.append($tr);
                    });


                }
                //check if element is input.
                else if ($el.is('input')) {
                    $el.val(value || '--');
                }
                //check if element is select.
                else if ($el.is('select')) {
                    $el.val(value || '--');
                    //set as data attribute
                    $el.attr('data-value', value || '--');
                }
                //check if element is textarea.
                else if ($el.is('textarea')) {
                    $el.val(value || '--');
                }
                //check if element is img.
                else if ($el.is('img')) {
                    $el.attr('src', value || '--');
                }
                //check if element is a.
                else if ($el.is('a')) {
                    $el.attr('href', value || '--');
                }
                else {
                    const format = $el.data('format');
                    const currency = $el.data('currency') || 'INR';
                    const country = $el.data('country') || 'IN';
                    const formatedValue = formatValue(value, format, currency, country);
                    if (format == "html")
                        $el.html(formatedValue);
                    else
                        $el.text(formatedValue);
                }
            }
        });
    }
}

// Function to fetch data for grouped elements
async function apiCall(method, endpoint, data = {}, isFormData = false) {

    // remove base_url from endpoint
    if (endpoint.startsWith(base_url)) {
        endpoint = endpoint.replace(base_url, '');
    }

    endpoint = endpoint.replace(/^\/|\/$/g, '');


    if (apiReadyState && !skipPreloader) {
        showPreloader();
    }

    const jwtToken = localStorage.getItem('jwt');

    // Prepare headers
    const headers = {
        // 'Authorization': 'Bearer ' + jwtToken,
        'X-Client-Type': 'web',
        'X-Client-Deice-Type': localStorage.getItem('clientType') || 'unknown'
    };

    if (!isFormData) {
        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';
    }

    const requestOptions = {
        method: method,
        headers: headers
    };

    if (['POST', 'PUT', 'PATCH'].includes(method.toUpperCase())) {
        requestOptions.body = isFormData ? data : (data === undefined || data === null ? null : JSON.stringify(data));
    }


    try {
        const response = await fetch(base_url + endpoint, requestOptions);
        const contentType = response.headers.get('Content-Type');
        let result = null;

        if (contentType.includes("application/json")) {
            result = await response.json();

            handleNotificationForApiResponse(response, result);

            // Check if result.data is valid JSON (object or array)
            if (typeof result.data == 'undefined') {
                result.data = {};
            }

            processApiResponse(endpoint, result);
        }

        if (apiReadyState) {
            hidePreloader();
        }

        if (response.status === 401) {
            // Handle token expiration by attempting refresh
            return await handleTokenRefresh(() => apiCall(method, endpoint, data, isFormData));
        }

        if (response.status < 200 || response.status >= 300) {
            throw new Error(`Unexpected response status: ${response.status}`);
        }

        if (contentType.includes("application/json")) {
            const event = new CustomEvent('apiSuccess', {
                detail: {
                    endpoint,
                    method,
                    data,
                    response: result
                }
            });
            window.dispatchEvent(event);
            return result;
        } else if (contentType.includes("application/pdf") || contentType.includes("application/octet-stream")) {
            return downloadFile(response);
        } else {
            mtplAlerts.show('error', `Unsupported response type. ${contentType}`, 'Error');
            throw new Error(`Unexpected response type: ${contentType}`);
        }

    } catch (error) {
        // mtplAlerts.show('error', `API Call Error`, 'Error');
        // console.error("API Call Error:", error);
        throw error;
    }
}

// ✅ Function to handle token refresh and retry the original request
async function handleTokenRefresh(retryFunction) {
    try {
        const refreshResponse = await refreshToken();
        // console.log("Token refreshed successfully", refreshResponse);
        if (refreshResponse) {
            const token = refreshResponse.data.token;
            const refreshToken = refreshResponse.data.refreshToken;

            // Update tokens in local storage
            // document.cookie = "jwt=" + token + "; path=/; Secure; SameSite=Lax";
            // document.cookie = "refreshToken=" + refreshToken + "; path=/; Secure; SameSite=Lax";

            // localStorage.setItem('jwt', token);
            // localStorage.setItem('refreshToken', refreshToken);

            // Retry the original request
            return await retryFunction();
        } else {
            throw new Error("Token refresh failed. Please log in again.");
        }
    } catch (error) {
        throw new Error("Token refresh failed. Please log in again.");
    }
}

// ✅ Function to handle errors and show alerts
async function handleNotificationForApiResponse(response, result) {

    let message = null;
    let stickyNotification = null;
    if (result.message) {
        message = result.message;
    }
    else if (result.messages) {
        message = result.messages;
    }

    if (result.stickyNotification) {
        stickyNotification = result.stickyNotification;
    }

    //for bootstrap alert style notification
    $("#notificationContainer").empty();
    if (stickyNotification && typeof stickyNotification === 'object' && !Array.isArray(stickyNotification)) {
        showNotification(stickyNotification);
    } else if (Array.isArray(stickyNotification)) {
        stickyNotification.forEach(showNotification);
    }


    //ignore for success message when initial api call is made to load view.
    if (doingAutoCrud && response.status === 200 && result.status) {
        return;
    }

    //if message is array, then join it with <br>
    if (Array.isArray(message)) {
        message = message.join("<br>");
    }

    // if object, then join values with <br>
    if (typeof message === 'string' && message.trim() === '') {
        message = null;
    } else if (typeof message === 'object' && message !== null) {
        message = Object.values(message).join("<br>");
    }

    if (response.status === 503) {
        mtplAlerts.show('error', "You are offline or service not available.", 'Error');
    }
    else if (response.status < 200 || response.status >= 300) {
        if (message) {
            mtplAlerts.show('error', message, 'Error');
        } else {
            // mtplAlerts.show('error', "An unknown error occurred.", 'Error');
        }
    }
    else {
        if (message) {
            if (result.status) {
                mtplAlerts.show('success', message, 'Success');
            } else {
                mtplAlerts.show('error', message, 'Error');
            }
        }
    }

}

function showNotification(notification) {
    const alertEl = $(`
        <div class="alert alert-${notification.type} alert-dismissible" role="alert" style="display:none;">
            ${notification.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    $("#notificationContainer").append(alertEl);
    alertEl.slideDown("fast");

    if (notification.duration && parseInt(notification.duration) > 0) {
        setTimeout(() => {
            alertEl.slideUp("fast", () => alertEl.remove());
        }, parseInt(notification.duration));
    }
}

// ✅ Function to download PDF or binary files
async function downloadFile(response) {
    const blob = await response.blob();

    // Extract filename from Content-Disposition header
    let fileName = "document.pdf"; // Default name
    const contentDisposition = response.headers.get('Content-Disposition');
    if (contentDisposition) {
        const match = contentDisposition.match(/filename="(.+?)"/);
        if (match && match[1]) {
            fileName = match[1];
        }
    }

    // Create a download link and trigger the download
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}



function refreshToken() {
    return $.ajax({
        url: base_url + 'api/auth/refreshToken',
        type: 'GET',
        headers: {
            // 'Authorization': 'Bearer ' + localStorage.getItem('jwt'),
        },
        dataType: 'json',
    });
}


// //create refreshToken function
// function refreshToken() {
//     // alert("test");
//     // return;
//     return $.ajax({
//         url: base_url + 'api/auth/refreshToken',
//         type: 'GET',
//         headers: {
//             'Authorization': 'Bearer ' + localStorage.getItem('refreshToken'),
//         },
//         dataType: 'json',
//     });
// }



function getFormDataWithFiles(form) {
    var formData = new FormData(form);
    var structuredData = {};
    var fileData = new FormData();
    var fileFound = false;

    // Define size limits
    const maxIndividualFileSize = window.appSettings.maxFileSizeMB * 1024 * 1024; // 2 MB
    const maxTotalUploadSize = window.appSettings.maxTotalFileSizeMB * 1024 * 1024; // 10 MB
    const allowedExtensions = window.appSettings.allowedFileTypes
        .split(",")
        .map(ext => ext.trim())
        .filter(ext => ext !== "");



    var totalUploadSize = 0;

    formData.forEach(function (value, key) {
        if (value instanceof File && value.size > 0) {
            // Check if the entry is a valid file (not a folder or invalid entry)
            // if (value.type === '') {
            //     mtplAlerts.show('error', `Invalid file selected: "${value.name}"`, 'Error');
            //     throw new Error(`Invalid file selected: "${value.name}"`);
            // }

            // Validate file extension
            const fileExtension = value.name.split('.').pop().toLowerCase(); // Extract extension
            if (!allowedExtensions.includes(fileExtension)) {
                console.log(allowedExtensions, fileExtension);
                mtplAlerts.show('error', `Invalid file extension: "${value.name}"`, 'Error');
                throw new Error(`Invalid file extension: "${value.name}"`);
            }

            // Validate individual file size
            if (value.size > maxIndividualFileSize) {
                mtplAlerts.show('error', `File "${value.name}" exceeds the individual size limit of ${window.appSettings.maxFileSizeMB} MB.`, 'Error');
                throw new Error(`File "${value.name}" is too large.`);
            }

            // Add to total size
            totalUploadSize += value.size;

            // Validate total upload size
            if (totalUploadSize > maxTotalUploadSize) {
                mtplAlerts.show('error', `Total upload size exceeds the limit of ${window.appSettings.maxTotalFileSizeMB} MB.`, 'Error');
                throw new Error('Total upload size too large.');
            }

            // Append valid file to FormData
            fileData.append(key, value);
            fileFound = true;
        } else {
            // Otherwise, add it to the structured JSON object
            setNestedValue(structuredData, key, value);
        }
    });

    return { jsonData: structuredData, fileData, fileFound };
}

function getFormDataObject(form) {
    var formData = new FormData(form);
    var structuredData = {};

    formData.forEach(function (value, key) {
        // Otherwise, add it to the structured JSON object
        setNestedValue(structuredData, key, value);
    });

    return structuredData;
}


// Helper function for structuring nested JSON objects
function setNestedValue(obj, key, value) {
    var keys = key.split(/[\[\]]+/).filter(k => k !== ""); // Split by [] and remove empty keys
    var lastKey = keys.pop();

    var current = obj;
    keys.forEach(function (k) {
        if (!current[k]) {
            current[k] = isNaN(Number(k)) ? {} : [];
        }
        current = current[k];
    });

    if (lastKey === "") {
        if (!Array.isArray(current)) {
            current = [];
        }
        current.push(value);
    } else if (Array.isArray(current)) {
        current.push(value);
    } else {
        current[lastKey] = current[lastKey] !== undefined
            ? [].concat(current[lastKey], value)
            : value;
    }
}


function drawLineChart(tagid, chartData, container) {
    const transformedData = transformApiResponseToDynamicChartData(chartData);
    let chartConfig = prepareDynamicChartOptions(transformedData);

    // Use the chartData.config.title as the chart title if defined
    if (chartData.config?.title) {
        chartConfig.title = {
            text: chartData.config.title,
            left: 'left', // Center-align the title
        };
    }

    // use the chartdata.config.xAxisLabel as the x-axis label if defined
    if (chartData.config?.xAxisLabel) {
        chartConfig.xAxis = {
            ...chartConfig.xAxis,
            name: chartData.config.xAxisLabel,
            nameLocation: 'middle',
            nameGap: 30,
        };
    }

    chartConfig = enableChartFeatures(chartConfig, container);


    //check if chart optiosn provided by view specific js file 
    chartConfig = overrideChartOptions(tagid, chartConfig);

    //fix for time series data when axis type is time.
    chartConfig = convertSeriesForTimeAxis(chartConfig, chartData.data, chartData.config?.xAxis);

    let chartInstance = echarts.getInstanceByDom(container);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }
}

function deepMergeObjects(target, source) {
    for (const key in source) {
        if (source[key] && typeof source[key] === 'object') {
            if (Array.isArray(source[key])) {
                // Handle arrays by merging corresponding elements
                target[key] = source[key].map((item, index) => {
                    if (typeof item === 'object' && target[key] && target[key][index]) {
                        return deepMergeObjects(target[key][index], item); // Merge individual objects in the array
                    }
                    return item; // Replace if not an object or no corresponding item exists
                });
            } else {
                // Recursively merge objects
                target[key] = deepMergeObjects(target[key] || {}, source[key]);
            }
        } else {
            // Overwrite primitive values directly
            target[key] = source[key];
        }
    }
    return target;
}


//to handle dynamic chart data for multiple series
function prepareDynamicChartOptions(transformedData) {
    const { labels, series } = transformedData;

    const yAxis = series.map((s, index) => ({
        type: 'value',
        // name: s.name, // Dynamic Y-axis name
        position: index % 2 === 0 ? 'left' : 'right', // Alternate left/right positioning
        offset: index > 1 ? (index - 1) * 50 : 0, // Offset for additional axes
        axisLabel: {
            show: index < 2,
        }
    }));

    return {
        tooltip: {
            trigger: 'axis',
        },
        legend: series.length > 1 ? { data: series.map(s => s.name), top: 20 } : null,
        xAxis: {
            type: 'category',
            data: labels,
        },
        yAxis, // Dynamically created Y-axes
        series: series.map(s => ({
            type: 'line',
            name: s.name,
            data: s.data,
            yAxisIndex: s.yAxisIndex,
            smooth: true,
        })),
    };
}

//to handle dynamic chart data for multiple series
function transformApiResponseToDynamicChartData(apiResponse) {
    // Extract data and configuration
    const data = apiResponse?.data;
    const config = apiResponse?.config;

    // Ensure data exists and is an array with at least one object
    if (!Array.isArray(data) || data.length === 0 || typeof data[0] !== 'object') {
        throw new Error("Data is undefined, not an array, empty, or does not contain objects");
    }

    // Get the X-axis column name from config
    const xAxisKey = config?.xAxis || Object.keys(data[0])[0]; // Fallback to the first key if xAxis is not specified

    // Extract all keys from the first object in data
    const keys = Object.keys(data[0]);

    // Remove the X-axis key from the value keys
    const valueKeys = keys.filter(key => key !== xAxisKey);

    // Extract labels for the X-axis
    const labels = data.map(item => item[xAxisKey]);

    // Create series dynamically
    let series = valueKeys.map((key, index) => ({
        name: key,
        data: data.map(item => item[key]),
        yAxisIndex: index // Map each series to a different Y-axis
    }));

    // Rearrange the series based on config.series order
    if (Array.isArray(config?.series) && config.series.length > 0) {
        const configSeriesNames = config.series; // Already an array of series names
        series = configSeriesNames.map((name, index) => {
            const match = series.find(s => s.name === name);
            return (
                match || {
                    name,
                    data: [],
                    yAxisIndex: index, // Assign yAxisIndex based on the config.series order
                }
            );
        });

        // Reassign yAxisIndex to maintain proper indexing
        series = series.map((s, index) => ({
            ...s,
            yAxisIndex: index,
        }));
    }

    // console.log("Dynamic chart data:", { labels, series });

    return { labels, series };
}




function drawPieChart(tagid, chartData, container) {

    const transformedData = transformApiResponseToDynamicChartData(chartData);
    let chartConfig = prepareDynamicChartOptions(transformedData);

    chartConfig = transformLineToPieChartConfig(chartConfig);

    // Use the chartData.config.title as the chart title if defined
    if (chartData.config?.title) {
        chartConfig.title = {
            text: chartData.config.title,
            left: 'left', // Center-align the title
        };
    }

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    let chartInstance = echarts.getInstanceByDom(container);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }
}

function drawSunBurstChart(tagid, chartData, container) {

    // console.log("Sunburst Chart Data", chartData);

    // Calculate parent values iteratively
    const processedData = calculateParentValuesIteratively(chartData.data);

    // Chart configuration
    let chartConfig = {
        title: {
            text: chartData.config.title || '',
            left: 'center',
        },
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c}', // {b}: name, {c}: value
        },
        series: [
            {
                type: 'sunburst',
                data: processedData,
                radius: [0, '90%'], // Inner and outer radius
                label: {
                    show: true,
                    formatter: '{b}', // Display name
                },
                emphasis: {
                    focus: 'ancestor', // Highlight the path to the root on hover
                },
            },
        ],
    };

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    let chartInstance = echarts.getInstanceByDom(container);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }
}

function drawCalenderCartesianChart(tagid, chartData, container) {

    // console.log("Calender Cartesian Chart Data", chartData);

    // Extract configuration and data
    const { config, data } = chartData;

    // Extract min and max values for visualMap
    const values = data.map(d => d[1]);
    const minValue = Math.min(...values);
    const maxValue = Math.max(...values);

    // Chart configuration
    let chartConfig = {
        title: {
            text: config.title || '',
            left: 'center',
        },
        tooltip: {
            position: 'top',
            formatter: function (params) {
                return `Date: ${params.data[0]}<br>Value: ${params.data[1]}`;
            },
        },
        visualMap: {
            min: minValue,
            max: maxValue,
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '0%',

        },
        calendar: {
            range: config.year || new Date().getFullYear(),
            cellSize: ['auto', 'auto'], // Adjust width and height of cells
            bottom: '15%',
        },
        series: [
            {
                type: 'heatmap',
                coordinateSystem: 'calendar',
                data: data,
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 0, 0, 0.5)',
                    },
                },
            },
        ],
    };


    chartConfig.visualMap.type = container.data('rangetype') !== undefined ? container.data('rangetype') : 'continuous';
    chartConfig.visualMap.splitNumber = container.data('split') !== undefined ? container.data('split') : 10;

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    let chartInstance = echarts.getInstanceByDom(container);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }
}

function drawCartesianChart(tagid, chartData, container) {
    // Extract configuration and data
    const { config, data } = chartData;

    // Extract X-axis labels and Y-axis labels (all keys except xAxis)
    const xAxisLabels = data.map(item => item[config.xAxis]);
    const yAxisLabels = Object.keys(data[0]).filter(key => key !== config.xAxis);

    // Prepare Cartesian data
    const heatmapData = [];
    data.forEach((row, xIndex) => {
        yAxisLabels.forEach((yLabel, yIndex) => {
            heatmapData.push([xIndex, yIndex, row[yLabel]]);
        });
    });

    // Chart configuration
    let chartConfig = {
        title: {
            text: config.title || '',
            left: 'center',
        },
        tooltip: {
            position: 'top',
            formatter: function (params) {
                const xLabel = xAxisLabels[params.data[0]];
                const yLabel = yAxisLabels[params.data[1]];
                const value = params.data[2];
                return `${yLabel} (${xLabel}): ${value}`;
            },
        },
        xAxis: {
            type: 'category',
            data: xAxisLabels,
        },
        yAxis: {
            type: 'category',
            data: yAxisLabels,
        },
        visualMap: {
            min: Math.min(...heatmapData.map(d => d[2])),
            max: Math.max(...heatmapData.map(d => d[2])),
            calculable: true,
            orient: 'horizontal',
            left: 'center',
            bottom: '0%',
        },
        series: [
            {
                name: '',
                type: 'heatmap',
                data: heatmapData,
                label: {
                    show: true,
                },
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowColor: 'rgba(0, 0, 0, 0.5)',
                    },
                },
            },
        ],
    };

    chartConfig.visualMap.type = container.data('rangetype') !== undefined ? container.data('rangetype') : 'continuous';
    chartConfig.visualMap.splitNumber = container.data('split') !== undefined ? container.data('split') : 10;

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    let chartInstance = echarts.getInstanceByDom(container);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }
}

function drawGuageChart(tagid, chartData, container) {

    // Extract data
    const gaugeData = chartData.data;

    // Validate essential properties
    if (gaugeData.value === undefined || gaugeData.max === undefined || gaugeData.min === undefined) {
        console.error('Gauge data missing required fields:', gaugeData);
        return;
    }

    // Prepare axis colors based on thresholds or default gray
    const axisColors =
        gaugeData.thresholds && gaugeData.thresholds.length > 0
            ? gaugeData.thresholds.map(threshold => [
                threshold.value / gaugeData.max, // Normalize threshold value
                threshold.color,
            ])
            : [[1, '#E0E0E0']]; // Default gray arc if no thresholds

    startAngle = container.data('startangle') !== undefined ? container.data('startangle') : 225;
    endAngle = container.data('endangle') !== undefined ? container.data('endangle') : -45;

    // Prepare series configuration
    const series = {
        name: gaugeData.name,
        progress: {
            show: !gaugeData.thresholds,
        },
        type: 'gauge',
        radius: '85%',
        center: ['50%', '60%'], // Center position
        max: gaugeData.max,
        min: gaugeData.min,
        axisLine: {
            lineStyle: {
                width: 15, // Gauge arc thickness
                color: gaugeData.thresholds ? axisColors : [[1, '#E0E0E0']], // Use thresholds or default
            },
        },
        pointer: {
            width: 5, // Pointer thickness
            length: '70%', // Pointer length
        },
        axisTick: {
            length: 8, // Tick length
        },
        splitLine: {
            length: 15, // Split line length
            lineStyle: {
                width: 2,
            },
        },
        axisLabel: {
            distance: 20, // Distance of labels from the gauge
            // formatter: value => `${value}${gaugeData.unit || ''}`, // Add unit to labels
        },
        startAngle: startAngle,
        endAngle: endAngle,
        detail: {
            formatter: `{value} ${gaugeData.unit || ''}`,
            fontSize: 16,
            offsetCenter: [0, '35%'], // Position detail below the pointer
        },
        data: [{ value: gaugeData.value, name: gaugeData.name }],
    };

    // Prepare the chart configuration
    let chartConfig = {
        title: {
            text: chartData.label || '',
            left: 'center',
            top: '2%', // Position the title higher
        },
        tooltip: {
            formatter: function (params) {
                return `${params.seriesName}: ${params.value} ${gaugeData.unit || ''}`;
            },
        },
        series: [series],
    };

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    let chartInstance = echarts.getInstanceByDom(container);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }
}

function transformLineToPieChartConfig(chartConfig) {

    // set tooltip trigger to item
    chartConfig.tooltip = {
        trigger: 'item',
    };

    const totalSeries = chartConfig.series.length; // Total datasets
    const maxOuterRadius = 50; // Outer radius for the largest ring (in %)
    const minInnerRadius = 10; // Inner radius for the smallest ring (in %)
    const radiusStep = (maxOuterRadius - minInnerRadius) / totalSeries; // Step for each ring

    return {
        ...chartConfig,
        xAxis: undefined, // Remove xAxis
        yAxis: undefined, // Remove yAxis
        series: chartConfig.series.map((series, index) => ({
            name: series.name,
            type: 'pie',
            radius: [
                `${minInnerRadius + index * radiusStep}%`, // Inner radius
                `${minInnerRadius + (index + 1) * radiusStep}%`, // Outer radius
            ],
            center: ['50%', '50%'], // Same center for all
            data: chartConfig.xAxis.data.map((label, i) => ({
                name: label,
                value: series.data[i],
            })),
        })),
    };
}

function drawBarChart(tagid, chartData, container) {

    const transformedData = transformApiResponseToDynamicChartData(chartData);
    let chartConfig = prepareDynamicChartOptions(transformedData);

    chartConfig = transformLineToBarChartConfig(chartConfig);

    // Use the chartData.config.title as the chart title if defined
    if (chartData.config?.title) {
        chartConfig.title = {
            text: chartData.config.title,
            left: 'left', // Center-align the title
        };
    }
    // use the chartdata.config.xAxisLabel as the x-axis label if defined
    if (chartData.config?.xAxisLabel) {
        chartConfig.xAxis = {
            ...chartConfig.xAxis,
            name: chartData.config.xAxisLabel,
            nameLocation: 'middle',
            nameGap: 30,
        };
    }

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    //fix for time series data when axis type is time.
    chartConfig = convertSeriesForTimeAxis(chartConfig, chartData.data, chartData.config?.xAxis);

    let chartInstance = echarts.getInstanceByDom(container[0]);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }

}

function convertSeriesForTimeAxis(chartConfig, rawData, xAxisKey = null) {
    if (!chartConfig?.xAxis || chartConfig.xAxis.type !== 'time') return chartConfig;

    // Auto-detect xAxisKey if not passed
    if (!xAxisKey && Array.isArray(rawData) && rawData.length > 0) {
        xAxisKey = Object.keys(rawData[0])[0];
    }

    // Sanity check
    if (!xAxisKey) return chartConfig;

    // Remap series using correct field name
    chartConfig.series = chartConfig.series.map(series => ({
        ...series,
        data: rawData.map(item => [
            item[xAxisKey] ?? null,
            parseFloat(item[series.name]) ?? 0
        ])
    }));

    // Remove xAxis.data (not used for time axis)
    delete chartConfig.xAxis.data;

    return chartConfig;
}

function drawParetoChart(tagid, chartData, container) {

    const transformedData = transformApiResponseToDynamicChartData(chartData);

    const { labels, series } = transformedData;


    // Extract the first series for Pareto calculation
    const paretoSeries = series[0];
    const dataWithLabels = labels.map((label, index) => ({
        label,
        value: paretoSeries.data[index],
    }));

    // Sort the data in descending order by value
    dataWithLabels.sort((a, b) => b.value - a.value);

    // Calculate the cumulative percentage for Pareto line
    const total = dataWithLabels.reduce((sum, item) => sum + item.value, 0);
    let cumulativeSum = 0;
    const paretoLine = dataWithLabels.map(item => {
        cumulativeSum += item.value;
        cumulativePercent = (cumulativeSum / total) * 100
        return cumulativePercent.toFixed(2);
    });

    // Extract sorted labels and values for the bar chart
    const sortedLabels = dataWithLabels.map(item => item.label);
    const sortedValues = dataWithLabels.map(item => item.value);

    // Build the chart configuration
    let chartConfig = {
        title: {
            text: 'Pareto Chart',
            left: 'center',
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
            },
        },
        legend: {
            data: [paretoSeries.name, ...series.slice(1).map(s => s.name)],
            top: '10%',
        },
        xAxis: {
            type: 'category',
            data: sortedLabels,
        },
        yAxis: [
            {
                type: 'value',
                name: paretoSeries.name,
                position: 'left',
            },
            {
                type: 'value',
                name: 'Cumulative %',
                position: 'right',
                axisLabel: {
                    formatter: '{value}%',
                },
            },
        ],
        series: [
            // Bar chart for the first series
            {
                name: paretoSeries.name,
                type: 'bar',
                data: sortedValues,
                yAxisIndex: 0,
            },
            // Line chart for Pareto line
            {
                name: 'Cumulative %',
                type: 'line',
                data: paretoLine,
                yAxisIndex: 1,
                smooth: true,
                lineStyle: {
                    color: '#FF5733', // Optional: Customize the Pareto line color
                },
            },
            // Additional series as normal line charts
            ...series.slice(1).map(s => ({
                name: s.name,
                type: 'line',
                data: s.data,
                yAxisIndex: 0,
                smooth: true,
            })),
        ],
    };

    // Use the chartData.config.title as the chart title if defined
    if (chartData.config?.title) {
        chartConfig.title = {
            text: chartData.config.title,
            left: 'left', // Center-align the title
        };
    }
    // use the chartdata.config.xAxisLabel as the x-axis label if defined
    if (chartData.config?.xAxisLabel) {
        chartConfig.xAxis = {
            ...chartConfig.xAxis,
            name: chartData.config.xAxisLabel,
            nameLocation: 'middle',
            nameGap: 30,
        };
    }

    chartConfig = enableChartFeatures(chartConfig, container);

    chartConfig = overrideChartOptions(tagid, chartConfig);

    let chartInstance = echarts.getInstanceByDom(container[0]);
    if (chartInstance) {
        // Update existing chart instance
        chartInstance.setOption(chartConfig, true);
    } else {
        // Create new chart instance
        chartInstance = echarts.init(container[0], window.appSettings.chartTheme);
        chartInstance.setOption(chartConfig);
    }

}

function transformLineToBarChartConfig(chartConfig) {
    if (!chartConfig || !chartConfig.series || !chartConfig.xAxis) {
        console.error("Invalid chartConfig passed to the function.");
        return chartConfig; // Return as-is if invalid
    }

    // Transform the configuration
    return {
        ...chartConfig,
        series: chartConfig.series.map(series => ({
            ...series,
            type: 'bar', // Change type to bar
            smooth: undefined, // Remove smooth property (not needed for bar charts)
        })),
        xAxis: {
            ...chartConfig.xAxis,
            type: 'category', // Ensure xAxis is categorical
        },
        yAxis: Array.isArray(chartConfig.yAxis)
            ? chartConfig.yAxis.map(axis => ({
                ...axis,
                type: 'value', // Ensure each Y-axis is numeric
            }))
            : [
                {
                    type: 'value',
                    position: 'left',
                },
            ],
    };
}

$(function () {
    jQuery(document).on("click", ".writeUiTagBtn", function (e) {
        e.preventDefault();
        let $btn = $(this);
        let tagId = $btn.data('tagid');
        let tagName = $btn.data('tagname') || ('Tag #' + tagId);

        mtplAlerts.prompt(
            'Write Scada Tag Value',
            'Enter value for tag <strong>"' + tagName + '"</strong> (ID: ' + tagId + '):',
            function (val) {
                if (val !== null && val !== undefined && val.trim() !== '') {
                    skipPreloader = true;
                    let payload = {};
                    payload[tagId] = val.trim();
                    apiCall('POST', 'api/OpMasterFront/writeTags', payload).then(function (response) {
                        if (response && response.status) {
                            mtplAlerts.show('success', response.message || 'Tag value written successfully!', 'Success');
                        } else {
                            let errMsg = (response && (response.errorMessage || response.message)) ? (response.errorMessage || response.message) : 'Failed to write tag';
                            mtplAlerts.show('error', errMsg, 'Error');
                        }
                    }).catch(function (err) {
                        mtplAlerts.show('error', 'Error writing tag value', 'Error');
                    });
                }
            }
        );
    });

    jQuery(document).on("click", ".apiAction", function (e) {
        let $clickedElement = $(this); // Store reference to $(this)
        let endpoint = $clickedElement.data('endpoint');
        let confirmMsg = $clickedElement.data('confirm');
        let reload = $clickedElement.data('reload');
        let reloadView = $clickedElement.data('reloadview');

        let executeAction = function () {
            apiCall('GET', endpoint).then(function (response) {
                if ($clickedElement.closest(".manageDataTable").length) {
                    $clickedElement.closest(".manageDataTable table").DataTable?.()?.ajax?.reload?.(null, false);
                    $clickedElement.closest(".manageDataTable").data('reload')?.();
                }
                else if (jQuery(".manageDataTable").length) {
                    jQuery(".manageDataTable table").DataTable?.()?.ajax?.reload?.(null, false);
                    jQuery(".manageDataTable").data('reload')?.();
                }

                if (reload) {
                    mtplAlerts.show('info', 'Reloading...', 'Success');
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }

                if (reloadView) {
                    if (typeof reloadView == 'string' && reloadView != "" && reloadView.toLowerCase() != "true") {
                        loadView(reloadView);
                    }
                    else {
                        loadView();
                    }
                }
            });
        };

        if (confirmMsg) {
            e.preventDefault();
            mtplAlerts.confirm(confirmMsg, executeAction);
        } else {
            executeAction();
        }
    });
});

function prepareFormView() {
    $('.autoCrudForm').each(function () {
        var $form = $(this);

        if ($form.data('processed')) return; // ✅ Skip already processed forms

        $form.data('processed', 1); // ✅ Mark as processed


        var resource = $form.data('resource');
        var recordId = $form.data('record-id');
        var dropdowns = $form.data('dropdowns') || [];
        var recordData = {};  // To store fetched record data if edit mode
        window.myPreLoadDropdowns = {};
        let apiRequests = [];

        // set form data attribute for readystate
        $form.data('readystate', 'loading');

        // add all dropdown requests to apiRequests array
        $.each(dropdowns, function (index, drop) {
            apiRequests.push(apiCall('GET', drop.endpoint).then(function (response) {
                window.myPreLoadDropdowns[drop.name] = response.data;
            }));
        });

        // add edit form data loading to apiRequests array
        if (recordId) {
            apiRequests.push(apiCall('GET', resource).then(function (response) {
                recordData = response.data;
            }));
        }

        Promise.all(apiRequests).then(() => {

            // console.log('All AJAX requests completed:', results);

            // Call function with all responses
            // allAjaxCompleted(results);
            // console.log(recordData);
            $form.data('readystate', 'ready');

            $.each(window.myPreLoadDropdowns, function (dropdownName, dropdownData) {
                let dropdowns = $form.find(`select[data-dropdown="${dropdownName}"], select[name="${dropdownName}"]`);

                dropdowns.each(function () {
                    let $dropdown = $(this);

                    if (dropdownData) {
                        let defaultValue = [];
                        let options = $.map(dropdownData, function (item) {

                            if (item.selected)
                                defaultValue.push(item.id);

                            var $opt = $('<option>', {
                                value: item.id,
                                text: item.name,
                                subtext: item.subtext || '',
                            });

                            // Set custom attributes
                            if (item.attributes) {
                                $.each(item.attributes, function (key, value) {
                                    $opt.attr('data-' + key, value);
                                });
                            }

                            return $opt;
                        });

                        $dropdown.empty().append(options);
                        if (!$dropdown.prop('multiple')) {
                            $dropdown.prepend('<option></option>');
                        }

                        if (defaultValue.length > 0) {
                            $dropdown.val(defaultValue).trigger('change');
                        }
                        else {
                            $dropdown.val(null).trigger('change');
                        }

                    }
                });
            });



            processFormData(recordData);
        });

        // Promise.all(apiRequests)
        //     .then(results => {


        //     })
        //     .catch(error => {
        //         console.error('One or more requests failed:', error);
        //     });

        function processFormData(response) {

            //check if response is blank object.
            if (response && typeof response === 'object' && Object.keys(response).length === 0 && $form.is('[autoCache]')) {
                const key = getFormKey($form);
                restoreFormData($form, key);

                var { jsonData, fileData, fileFound } = getFormDataWithFiles($form[0]);
                formsToCache.push({ form: $form, initial: jsonData });
            }

            // Populate non-dropdown fields
            $.each(response, function (key, value) {
                var $field = $form.find('[name="' + key + '"]');

                // update default image url for image cropper input.
                if ($field.hasClass("value_container")) {
                    $field.parent(".imageUploader").find(".user_pic_container").attr("src", value);
                }
                else {

                    if ($field.length && value) {

                        // if checkobx
                        if ($field.attr('type') == 'checkbox') {
                            if (Array.isArray(value)) {
                                value.forEach(val => {
                                    $field.filter(`[value="${val}"]`).prop('checked', true);
                                });
                            } else if (typeof value === 'object') {
                                // console.log("Object Received", value);
                            } else {
                                $field.prop('checked', value);
                            }
                        }

                        // if radio
                        else if ($field.attr('type') == 'radio') {
                            $field.filter('[value="' + value + '"]').prop('checked', true);
                        }

                        //for ajax select2 dropdowns
                        else if ($field.data('selecttype') == 'ajax') {
                            if (Array.isArray(value)) {
                                //for multi select ajax
                                value.forEach(val => {
                                    $field.append(new Option(val.text, val.id, true, true)).trigger('change');
                                });
                            }
                            else if (typeof value === 'object') {
                                //for single select ajax
                                // console.log($field.attr("name"), value);
                                $field.append(new Option(value.text, value.id, true, true)).trigger('change');
                            }
                            else {
                                $field.append(new Option(value, value, true, true)).trigger('change');
                                mtplAlerts.show('error', 'You need to provide id/text elements as array for ajax based dropdown for ' + key + '.', 'Error');
                            }
                        }
                        else if ($field.hasClass('datePicker')) {
                            $field.val(moment(value).format('DD/MM/YYYY')).trigger('change');
                        }
                        else if ($field.hasClass('timePicker')) {
                            $field.val(moment("1970-01-01 " + value).format('HH:mm A')).trigger('change');
                        }
                        else if ($field.hasClass('dateTimePicker')) {
                            $field.val(moment(value).format('DD/MM/YYYY HH:mm A')).trigger('change');
                        }
                        //check if element is select
                        else if ($field.is('select')) {
                            // console.log("Select Element", key, value);
                            $field.val(value).trigger('change');
                        }
                        else if ($field.attr('type') != 'hidden') {
                            $field.val(value);
                        }

                        if ($field.hasClass('colorPicker')) {
                            $field.trigger('change');
                        }
                    }
                }


                //for one to many relationship
                if ($('.oneToManyWrapper[data-group="' + key + '"]').length && Array.isArray(value) && value.length > 0) {
                    const $wrapper = $('.oneToManyWrapper[data-group="' + key + '"]');

                    const $template = $wrapper.find('.oneToManyElement').first();

                    // Destroy select2 before cloning
                    $template.find('select').each(function () {
                        if ($(this).data('select2')) {
                            $(this).select2('destroy');
                        }
                    });

                    // Remove extra elements except first
                    $wrapper.find('.oneToManyElement:gt(0)').remove();

                    const newElements = [];

                    for (let i = 0; i < value.length; i++) {
                        const $newElement = (i === 0) ? $template : $template.clone();
                        let lastIndex = i + 1;
                        let uid = `${key}_${lastIndex}`;

                        $newElement.attr("data-index", lastIndex);
                        $newElement.find(".elementTitle").text("Item " + lastIndex);
                        $newElement.find('[data-bs-target]').attr('data-bs-target', `#${uid}`);
                        $newElement.find(".uid").attr("id", uid);

                        $.each(value[i], function (fieldKey, fieldVal) {
                            let $input = $newElement.find(`[name="${key}[${fieldKey}]"]`);
                            if ($input.length) {
                                if ($input.data('selecttype') === 'ajax') {
                                    if (typeof fieldVal === 'object' && fieldVal.id && fieldVal.text) {
                                        $input.append(new Option(fieldVal.text, fieldVal.id, true, true));
                                    } else {
                                        $input.append(new Option(fieldVal, fieldVal, true, true));
                                    }
                                } else {
                                    $input.val(fieldVal);
                                }
                            }
                        });

                        $newElement.find("select.select2:not(.select2-hidden-accessible)").each(function () {
                            $(this).removeClass('select2-initialized');
                        });

                        if (i !== 0) $wrapper.find('.oneToManyElement').last().after($newElement);
                        newElements.push($newElement);

                        // fire new custom event
                        const event = new CustomEvent('oneToManyElementAdded', {
                            detail: {
                                element: $newElement,
                                group: key,
                                index: lastIndex,
                                editData: value[i],
                            }
                        });
                        window.dispatchEvent(event);
                    }



                    // Update card title if available
                    newElements.forEach(($el, i) => {
                        $el.find('.titleSource').trigger('change');
                    });

                    // Keep only last "Add" button visible
                    $wrapper.find('.addOneToManyElement').hide();
                    $wrapper.find('.oneToManyElement').last().find('.addOneToManyElement').show();
                }


            });


            // execute calculate function if exists
            if (typeof calculate === 'function') {
                calculate();
            }
            // console.log("Form data processed successfully.");
            applyUiLibrary();
        }

        // Handle form submission (same as before)
        $form.submit(function (e) {

            if (typeof calculate === 'function') {
                calculate();
            }

            e.preventDefault();

            //validate form for required attributes.
            let isValid = true;

            // remove all invalid class
            jQuery(this).find('.is-invalid').removeClass('is-invalid');

            jQuery(this)
                .find("[required]")
                .each(function () {
                    let $field = jQuery(this);

                    let val = $field.val();
                    let isEmpty = (
                        val === null ||
                        val === undefined ||
                        (Array.isArray(val) && val.length === 0) ||
                        (typeof val === 'string' && val.trim() === '') ||
                        (Array.isArray(val) && val.length === 1 && val[0].trim() === '')
                    );

                    $field.removeClass("is-invalid is-valid");

                    if (isEmpty) {
                        $field.addClass("is-invalid");
                        if ($field.hasClass('select2-hidden-accessible')) {
                            $field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                        }
                        isValid = false;
                    } else {
                        $field.addClass("is-valid");
                        if ($field.hasClass('select2-hidden-accessible')) {
                            $field.next('.select2-container').find('.select2-selection')
                                .removeClass('is-invalid')
                                .addClass('is-valid');
                        }
                    }
                });


            if (!isValid) {
                return false;
            }

            // check readystate
            if ($form.data('readystate') != 'ready') {
                mtplAlerts.show('error', 'Form is not ready yet, please wait.', 'Error');
                return false;
            }


            // var formData = getFormDataObject($form[0]);
            var { jsonData, fileData, fileFound } = getFormDataWithFiles($form[0]);

            if (fileFound) {
                // Add the JSON data to the FormData as a blob
                fileData.append('mtplJsonData', new Blob([JSON.stringify(jsonData)], { type: 'application/json' }));
                postData = fileData;
            }
            else {
                postData = jsonData;
            }

            // replace get with save in the endpoint, only first occurrence of get from the end
            var endpoint = resource.replace(/get(?!.*get)/, 'save');

            apiCall('POST', endpoint, postData, fileFound).then(function (response) {
                // if (response.message != "") {
                //     mtplAlerts.show('success', response.message, 'Success');
                // }
                if (!recordId) {
                    $form[0].reset();

                    // Reset all Select2
                    $form.find('select.select2').each(function () {
                        const $el = $(this);
                        const isAjax = $el.data('selecttype') === 'ajax';

                        if (isAjax) {
                            // Clear value and trigger change, even if no <option> exists
                            $el.val(null).trigger('change');

                            // Optional: if using Select2 with AJAX and it shows stale text, force UI reset
                            $el.empty(); // remove selected <option>
                        } else if ($el.prop('multiple')) {
                            $el.val(null).trigger('change');
                        } else {
                            const defaultVal = $el.find('option[selected]').val() || $el.find('option:first').val();
                            $el.val(defaultVal).trigger('change');
                        }
                    });

                    //remove is-invalid and is-valid class from all inputs
                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.is-valid').removeClass('is-valid');
                }

                //check if field needs to be updated
                if (response.setValue) {
                    const field = response.setValue.field;
                    const id = response.setValue.id;
                    const text = response.setValue.text;

                    const $field = jQuery(`[name="${field}"]`);
                    if ($field.length) {
                        $field.append(new Option(text, id, true, true)).trigger('change').trigger('select2:select');
                    }
                }

                if (response.nextPopup) {
                    openApiPopup(
                        response.nextPopup.endpoint,
                        response.nextPopup.title || '',
                        response.nextPopup.size || 'xl',
                        response.nextPopup.modaltype || 'self',
                        response.nextPopup.stricttype || '',
                    );
                }
                else {
                    //check if form is inside modal
                    if ($form.closest('.modal').length) {
                        $form.closest('.modal').modal('hide');
                    }
                }

                key = getFormKey($form);
                clearFormData(key);

                if (jQuery(".manageDataTable").length) {
                    // jQuery(".manageDataTable table").DataTable().ajax.reload(null, false);
                    jQuery(".manageDataTable table").DataTable?.()?.ajax?.reload?.(null, false);
                    $('.manageDataTable').data('reload')?.();
                }
            }).catch(function (error) {
                // console.error('Error in GET:', error);
                // console.error('Submission error:', error.textStatus, error.errorThrown);
            });
        });
    });
}


function calculateParentValuesIteratively(data) {
    const stack = [...data]; // Clone the data into a stack for iterative processing
    const processedNodes = new Map(); // To store processed nodes with calculated values

    while (stack.length > 0) {
        const node = stack.pop();

        // If the node has children, add children to the stack
        if (node.children && node.children.length > 0) {
            const unprocessedChildren = node.children.filter(child => !processedNodes.has(child.name));
            if (unprocessedChildren.length > 0) {
                // Push the node back to the stack for later processing
                stack.push(node);
                // Add unprocessed children to the stack
                stack.push(...unprocessedChildren);
            } else {
                // All children are processed; calculate the node's value
                const totalValue = node.children.reduce((sum, child) => sum + processedNodes.get(child.name).value, 0);
                processedNodes.set(node.name, { ...node, value: totalValue });
            }
        } else {
            // Leaf node, directly store it
            processedNodes.set(node.name, node);
        }
    }

    // Reconstruct the tree from processed nodes
    function reconstructTree(node) {
        if (node.children) {
            return { ...node, children: node.children.map(reconstructTree) };
        }
        return node;
    }

    return data.map(reconstructTree);
}

// Resize all charts on window resize
window.addEventListener('resize', () => {
    document.querySelectorAll('canvas[_echarts_instance_], div[_echarts_instance_]').forEach(
        function (e) {
            const instance = echarts.getInstanceByDom(e);
            if (instance) {
                instance.resize();
            }
        }
    );
});


function enableChartFeatures(chartConfig, container) {
    const chartZoon = container.data('chartzoom') !== undefined ? container.data('chartzoom') : false;
    const chartSave = container.data('chartsave') !== undefined ? container.data('chartsave') : false;

    if (chartZoon) {
        chartConfig.dataZoom = [
            {
                type: 'slider', // Slider for manual adjustment
                show: true,
                start: 0, // Start percentage of the visible data
                end: 100, // End percentage of the visible data
            },
            {
                type: 'inside', // Allows zooming via mouse scroll or touch gestures
                start: 0,
                end: 100,
            },
        ];
    }

    if (chartSave) {
        chartConfig.toolbox = {
            feature: {
                saveAsImage: {},
            }
        };
    }

    return chartConfig;
}


function overrideChartOptions(tagid, chartConfig) {

    const defaults = {
        tooltip: {
            backgroundColor: '#fff',
            textStyle: {
                color: '#000'
            },
            borderColor: '#ccc',
            borderWidth: 1
        }
    };

    chartConfig = deepMergeObjects({ ...chartConfig }, defaults);

    //check if chart optiosn provided by view specific js file 
    if (chartOptions[tagid]) {
        chartConfig = deepMergeObjects({ ...chartConfig }, chartOptions[tagid]);
    }
    return chartConfig;
}


function formatValue(value, format, currency, country) {
    let myValue = value || '--';
    if (format === 'currency') {
        myValue = new Intl.NumberFormat('en-' + country, { style: 'currency', currency: currency }).format(value);
    }
    else if (format === 'percent') {
        myValue = new Intl.NumberFormat('en-' + country, { style: 'percent' }).format(value);
    }
    else if (format === 'number') {
        myValue = new Intl.NumberFormat('en-' + country).format(value);
    }
    else if (format === 'date' || format === 'datetime' || format === 'time') {
        myValue = formatDateTime(format, value);
    }
    else if (format === 'boolean') {
        myValue = value ? 'Yes' : 'No';
    }
    return myValue;
}





/*****************************************
 * Chat Application Function
 * ***************************************/

$(document).ready(function () {
    // Open chat when user is clicked
    $('.user-item').click(function () {
        var user = $(this).data('user');
        $('#chatUser').text('Chat with ' + user);
        $('#chatWindow').fadeIn();
    });

    // Close chat
    $('.close-chat').click(function () {
        $('#chatWindow').fadeOut();
    });

    // Minimize chat
    $('.minimize-chat').click(function () {
        $('.chat-body, .chat-footer').toggle();
    });
});


/*****************************************
 * addOneToManyRow Function
 * ***************************************/

jQuery(document).on("click", ".addOneToManyElement", function () {
    var $clickedElement = $(this); // Store reference to $(this)
    var $wrapper = $clickedElement.closest(".oneToManyWrapper");

    // check if wraper found, else find from neigbour
    if ($wrapper.length === 0) {
        $wrapper = $(`.oneToManyWrapper[data-group="${$clickedElement.data('group')}"]`);
    }


    const groupName = $wrapper.data('group');
    var $lastElement = $wrapper.find(".oneToManyElement").last();
    var lastIndex = $lastElement.data("index");
    lastIndex++;
    const uid = `${groupName}_${lastIndex}`;

    // Destroy select2 on existing row before cloning
    $lastElement.find('select').each(function () {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
    });

    var $newRow = $lastElement.clone();

    // assign index
    $newRow.attr("data-index", lastIndex);
    $newRow.find(".elementTitle").text("Item " + lastIndex);
    $newRow.find('[data-bs-target]').attr('data-bs-target', `#${uid}`);
    $newRow.find(".uid").attr("id", uid);

    $newRow.find("input, select").val("");
    $newRow.find("input[type=checkbox]").prop("checked", false);
    $wrapper.find(".oneToManyElement").last().after($newRow);

    // Reinitialize select2 on both rows
    // $lastElement.find('select').select2();
    // $newRow.find('select').select2();

    //hide all buttons and keep last one open only.
    $wrapper.find(".oneToManyElement").find(".addOneToManyElement").hide();
    $wrapper.find(".oneToManyElement").last().find(".addOneToManyElement").show();

    $lastElement.find("select.select2:not(.select2-hidden-accessible)").each(function () {
        console.log("Destroying select2 on last element");
        $(this).removeClass('select2-initialized');
    });

    $newRow.find("select.select2:not(.select2-hidden-accessible)").each(function () {
        $(this).removeClass('select2-initialized');
    });

    applyUiLibrary();
    if (typeof calculate === 'function') {
        calculate();
    }

    // fire new custom event
    const event = new CustomEvent('oneToManyElementAdded', {
        detail: {
            element: $newRow,
        }
    });
    window.dispatchEvent(event);
});

//removeOneToManyRow
jQuery(document).on("click", ".removeOneToManyElement", function () {
    var $clickedElement = $(this); // Store reference to $(this)
    var $wrapper = $clickedElement.closest(".oneToManyWrapper");
    var $lastElement = $clickedElement.closest(".oneToManyElement");

    // Check if there is more than one row
    if ($wrapper.find(".oneToManyElement").length > 1) {
        $lastElement.remove();
    }

    $wrapper.find(".oneToManyElement").last().find(".addOneToManyElement").show();
    if (typeof calculate === 'function') {
        calculate();
    }
});

window.onApiReady(function () {
    hidePreloader();
    // fixColspan();
});


// function fixColspan() {
//     //for each table having class oneToManyTable
//     jQuery(".oneToManyTable").each(function () {
//         var $table = jQuery(this);
//         var $row = $table.find("tr.oneToManyRow").last();
//         var $tds = $row.find("td");
//         var totalCols = $tds.length;

//         $table.find("tr.oneToManyTotalRow").each(function () {
//             var rowCols = jQuery(this).find("td").length;
//             var finalColspan = totalCols - rowCols + 1;
//             jQuery(this).find("td").first().attr("colspan", finalColspan);
//         });
//     });
// }


/****************************************/
// Functions to auto cache form with autoCache attribute for unsaved data, to restore back on next visit within few minutes
/****************************************/

function getFormAttributes(form) {
    const attrs = {};
    $.each(form[0].attributes, function () {
        if (this.specified) {
            attrs[this.name] = this.value;
        }
    });
    return attrs;
}

function getFormKey(form) {
    const attrs = getFormAttributes(form);
    const sortedAttrs = Object.keys(attrs).sort().map(key => `${key}=${attrs[key]}`).join('&');
    const locationPart = window.location.pathname + window.location.search;
    return 'formCache_' + btoa(locationPart + '|' + sortedAttrs);
}

function restoreFormData(form, key) {
    return;
    const raw = sessionStorage.getItem(key);
    if (!raw) return;

    try {
        const { data, timestamp } = JSON.parse(raw);
        const age = (Date.now() - timestamp) / 1000 / 60;
        if (age > timeoutMinutes) {
            sessionStorage.removeItem(key);
            return;
        }

        let restoredSomething = false;

        Object.entries(data).forEach(([name, value]) => {
            const inputs = form.find(`[name="${name}"]`);

            if (!inputs.length) return;

            inputs.each(function () {
                const type = this.type;
                const $input = $(this);

                if (type === 'checkbox') {
                    const valArray = Array.isArray(value) ? value : [value];
                    const shouldBeChecked = valArray.includes(this.value);
                    if (this.checked !== shouldBeChecked) {
                        this.checked = shouldBeChecked;
                        restoredSomething = true;
                        $input.trigger('change'); // Trigger change event for select2 or other libraries
                    }

                } else if (type === 'radio') {
                    const shouldBeChecked = this.value === value;
                    if (this.checked !== shouldBeChecked) {
                        this.checked = shouldBeChecked;
                        restoredSomething = true;
                        $input.trigger('change'); // Trigger change event for select2 or other libraries
                    }

                } else {
                    if ($input.val() !== value) {
                        $input.val(value);
                        restoredSomething = true;
                        $input.trigger('change'); // Trigger change event for select2 or other libraries
                    }
                }
            });
        });

        if (restoredSomething) {
            mtplAlerts.show('info', 'We’ve restored your unsaved form data from last visit.', 'Info');
        }
    } catch (e) {
        sessionStorage.removeItem(key);
    }
}
function saveFormData(form, key) {

    var { jsonData, fileData, fileFound } = getFormDataWithFiles(form[0]);

    const payload = {
        data: jsonData,
        timestamp: Date.now()
    };
    sessionStorage.setItem(key, JSON.stringify(payload));
}

function clearFormData(key) {
    sessionStorage.removeItem(key);
}

function areFormsEqual(a, b) {

    const a1 = Object.keys(a).sort().map(key => `${key}=${a[key]}`).join('&');
    const b1 = Object.keys(b).sort().map(key => `${key}=${b[key]}`).join('&');

    return a1 === b1;
}

window.addEventListener('beforeunload', function () {
    return;
    formsToCache.forEach(({ form, initial }) => {
        const key = getFormKey(form);
        var { jsonData, fileData, fileFound } = getFormDataWithFiles(form[0]);
        // console.log(getFormDataWithFiles(form[0]));
        if (!areFormsEqual(initial, jsonData)) {
            saveFormData(form, key);
        }
    });
});


function openApiPopup(endpoint, title = '', size = '', modalType = '', strictType = '') {

    const parentModal = $('.modal.show');
    const modalOptions = {};

    if (strictType === 'strict') {
        modalOptions.backdrop = 'static';
        modalOptions.keyboard = false;
    }

    apiCall('GET', endpoint).then(function (response) {
        const htmlContent = response.data;

        if (modalType === "self") {
            parentModal.modal('hide');
        }

        openDynamicModal({
            title: title,
            htmlContent: htmlContent,
            size: size,
            callbacks: {},
            modalOptions: modalOptions
        });
    });
}

$(document).on('change input', '.oneToManyWrapper .titleSource', function () {
    let $card = $(this).closest('.oneToManyElement');
    let value = '';

    if ($(this).is('select')) {
        value = $(this).find('option:selected').text();
    } else {
        value = $(this).val();
    }

    if (!value) value = 'Item ' + ($card.index() + 1);

    $card.find('.elementTitle').text(value.trim());
});

$(document).on('click', '.oneToManyWrapper .card-header', function () {
    const $clickedCard = $(this).closest('.oneToManyElement');
    const targetId = $(this).attr('data-bs-target');

    // Collapse others
    // $('.oneToManyCard .collapse').not(targetId).collapse('hide');
    $(this).closest(".oneToManyWrapper").find(".collapse").not(targetId).collapse('hide');
});

// Listen to collapse events and toggle icon direction
$(document).on('shown.bs.collapse hidden.bs.collapse', '.oneToManyElement .collapse', function () {
    const $card = $(this).closest('.oneToManyElement');
    const $icon = $card.find('.card-header i.fa-chevron-down, .card-header i.fa-chevron-up').first();

    const isShown = $(this).hasClass('show');
    $icon.removeClass('fa-chevron-down fa-chevron-up')
        .addClass(isShown ? 'fa-chevron-up' : 'fa-chevron-down');
});
