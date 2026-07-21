var grid;
var currentEditItem = null;

jQuery(document).ready(function () {

    GridStack.renderCB = function (el, w) {
        el.innerHTML = w.content;
    };

    loadComponents();

    jQuery("#copyFrom").on("change", function () {
        var templateId = jQuery(this).val();

        apiCall('GET', 'api/system/dashboardTemplate/' + templateId).then(function (response) {
            const template = response.template;
            jQuery("#htmlTemplate").val(template.htmlTemplate);
            jQuery("#widgetName").val(template.widgetName + " - Copy");
            jQuery("#dataSource").val(template.dataSource);
        });

    });


    jQuery(".switchDashboard").on("change", function () {
        var dashboardNumber = jQuery(this).val();

        apiCall('GET', 'api/system/dashboardLayout/' + dashboardNumber).then(function (response) {
            var layout = response.layout.layout;
            var layout = JSON.parse(layout);
            var dashboardName = response.layout.dashboardName;

            jQuery("#dashBoardName").val(dashboardName);

            // clear the grid
            grid.removeAll();

            grid.load(layout);
            loadDashboardView();
        }).catch(function (error) {
            // clear the grid
            grid.removeAll();
            loadDashboardView();
        });

    });

    jQuery(".switchDashboard").trigger("change");

    jQuery("#saveDashboard").on("click", function () {
        saveDashboard();
    });

    jQuery("body").on("click", ".removeWidget", function () {
        if (confirm("Are you sure you want to remove this widget?")) {
            var item = jQuery(this).closest(".grid-stack-item");
            grid.removeWidget(item[0]);
        }
    });

    // copyWidget
    jQuery("body").on("click", ".copyWidget", function () {
        var item = jQuery(this).closest(".grid-stack-item");
        //save and create new component here
        var templateId = jQuery(item[0]).find(".mtplWidget").attr("data-templateid");

        apiCall('GET', 'api/system/dashboardTemplate/' + templateId).then(function (response) {
            const template = response.template;

            //copy the template and save as new tempalte
            let postData = {
                widgetName: template.widgetName + " - Copy",
                htmlTemplate: template.htmlTemplate,
                dataSource: template.dataSource,
            }

            apiCall('POST', 'api/system/dashboardTemplate/0', postData).then(function (response) {
                tamplateId = response.templateId;

                var widget = `<div class='mtplWidget' data-templateid='${tamplateId}'>${template.htmlTemplate}</div>
                <div class="widget-controls">
                    <a href="javascript:;" class="btn btn-icon btn-success copyWidget"><i class="fa fa-copy"></i></a>
                    <a href="javascript:;" class="btn btn-icon btn-warning editWidget"><i class="fa fa-cog"></i></a>
                    <a href="javascript:;" class="btn btn-icon btn-danger removeWidget"><i class="fa fa-trash"></i></a>
                </div>
                `;

                var widgetElement = widget;

                grid.addWidget({ w: 3, h: 20, content: widgetElement });

                saveDashboard();

                loadDashboardView();

                loadComponents();

            });
        });
    });


    jQuery("body").on("click", ".editWidget", function () {

        //code is not complete so left as of now.

        var item = jQuery(this).closest(".grid-stack-item");
        currentEditItem = item[0];


        var widgetHtml = jQuery(item[0]).find(".grid-stack-item-content").html();
        var templateId = jQuery(item[0]).find(".mtplWidget").attr("data-templateid");

        apiCall('GET', 'api/system/dashboardTemplate/' + templateId).then(function (response) {

            const template = response.template;

            jQuery("#widgetName").val(template.widgetName);
            jQuery("#htmlTemplate").val(template.htmlTemplate);
            jQuery("#dataSource").val(template.dataSource);
            jQuery("#widgetId").val(templateId);

            jQuery("#widgetModal").find(".addWidgetBtn").html('<i class="fa fa-save"></i> Update');
            jQuery("#widgetModal").modal("show");
        });
    });


    jQuery("body").on("click", ".addWidgetBtn", function () {

        var widgetName = jQuery("#widgetName").val();
        var widgetId = jQuery("#widgetId").val();
        var htmlTemplate = jQuery("#htmlTemplate").val();
        var dataSource = jQuery("#dataSource").val();

        if (!isValidHTML(htmlTemplate)) {
            mtplAlerts.show("error", "Invalid HTML format, kindly check your code.", "Error");
            return false;
        }

        //validate dataSource for valid json format.
        if (!isValidJSON(dataSource)) {
            mtplAlerts.show("error", "Invalid JSON format in Data Source JSON, kindly check your code.", "Error");
            return false;
        }

        let postData = {
            widgetName: widgetName,
            htmlTemplate: htmlTemplate,
            dataSource: dataSource,
        }

        console.log(postData);

        apiCall('POST', 'api/system/dashboardTemplate/' + widgetId, postData).then(function (response) {
            if (response.message != "") {
                mtplAlerts.show('success', response.message, 'Success');
            }

            tamplateId = response.templateId;

            var widget = `<div class='mtplWidget' data-templateid='${tamplateId}'>${htmlTemplate}</div>
            <div class="widget-controls">
                <a href="javascript:;" class="btn btn-icon btn-success copyWidget"><i class="fa fa-copy"></i></a>
                <a href="javascript:;" class="btn btn-icon btn-warning editWidget"><i class="fa fa-cog"></i></a>
                <a href="javascript:;" class="btn btn-icon btn-danger removeWidget"><i class="fa fa-trash"></i></a>
            </div>
            `;

            var widgetElement = widget;

            if (currentEditItem != null) {
                var gsx = jQuery(currentEditItem).attr("gs-x");
                var gsy = jQuery(currentEditItem).attr("gs-y");
                var gsw = jQuery(currentEditItem).attr("gs-w");
                var gsh = jQuery(currentEditItem).attr("gs-h");
                grid.removeWidget(currentEditItem);

                grid.addWidget({ w: gsw, h: gsh, x: gsx, y: gsy, content: widgetElement });
                currentEditItem = null;

            }
            else {
                grid.addWidget({ w: 3, h: 20, content: widgetElement });
            }

            jQuery("#widgetName").val("");
            jQuery("#htmlTemplate").val("");
            jQuery("#dataSource").val("");
            jQuery("#widgetId").val(0);


            // close the modal
            jQuery("#widgetModal").find(".addWidgetBtn").html('<i class="fa fa-plus-circle"></i> Add');
            jQuery("#widgetModal").modal("hide");

            loadDashboardView();

            loadComponents();

        })

    });

    options = {
        cellHeight: 5,
        animate: false, // show immediate (animate: true is nice for user dragging though)
        columnOpts: {
            breakpointForWindow: true,  // test window vs grid size
            breakpoints: [{ w: 700, c: 1 }, { w: 850, c: 3 }, { w: 950, c: 6 }, { w: 1100, c: 8 }]
        },
        // children: items,
        float: true
    };

    grid = GridStack.init(options);

    //auto clean up space and compact the grid
    // grid.on('change', function () {
    //     grid.compact();
    // });

    // Responsive breakpoints
    // grid.responsiveColumnWidths({
    //     minWidth: [576, 768, 992, 1200],
    //     columnWidths: [2, 3, 4, 6]
    // });

    // grid.load(savedLayout);

});

function loadDashboardView() {
    // load the view by default for data-endpoints, not the part of the widget but from apiHandler.js
    loadView();

    //collect all elements data-templateid as array.
    var widgets = [];
    jQuery(".mtplWidget").each(function () {
        var templateId = jQuery(this).attr("data-templateid");
        widgets.push(templateId);
    });

    //get all widget data from server
    apiCall('POST', 'api/system/dashboardData', { widgets: widgets }).then(function (response) {
        elements = jQuery(".mtplWidget [data-tagid]");
        processViewData(response.data, null, elements);
    });
}

function loadComponents() {
    apiCall('GET', 'api/system/dashboardTemplates').then(function (response) {
        var templates = response.templates;

        var html = "<option value=''>Select Template</option>";

        templates.forEach(template => {
            html += `<option value="${template.templateId}">${template.templateId}. ${template.widgetName}</option>`;
        });

        jQuery("#copyFrom").html(html);
    });
}

function saveDashboard() {
    var layout = grid.save();
    var dashboardNumber = jQuery(".switchDashboard").val();
    var dashBoardName = jQuery("#dashBoardName").val();

    var postData = {
        dashboardName: dashBoardName,
        dashboardNumber: dashboardNumber,
        layout: layout
    }

    apiCall('POST', 'api/system/dashboardLayout/' + dashboardNumber, postData).then(function (response) {
        if (response.message != "") {
            mtplAlerts.show('success', response.message, 'Success');
        }
    });
}


function isValidJSON(str) {
    try {
        JSON.parse(str);
        return true;
    } catch (e) {
        return false;
    }
}

function isValidHTML(htmlString) {
    var parser = new DOMParser();
    var doc = parser.parseFromString(htmlString, 'text/html');

    // Check if browser auto-fixed malformed HTML (by comparing input & output)
    var serializedHTML = new XMLSerializer().serializeToString(doc.body);
    if (!serializedHTML.includes(htmlString.trim())) {
        return false; // Browser modified the input, meaning it was invalid
    }

    // Check for parsing errors (works best in XML mode, but limited for HTML)
    var parseErrors = doc.querySelectorAll("parsererror");
    if (parseErrors.length > 0) {
        return false; // Found parsing errors
    }

    return true; // HTML is valid
}