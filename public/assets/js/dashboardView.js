var currentEditItem = null;
let grid;

jQuery(document).ready(function () {
    GridStack.renderCB = function (el, w) {
        el.innerHTML = w.content;
    };


    options = {
        staticGrid: true, // Make the grid static
        disableResize: true, // Disable resizing
        disableDrag: true, // Disable dragging
        float: true, // Disable floating widgets
        cellHeight: 5,
        margin: 5,
        // cellHeight: 80,
        animate: true, // show immediate (animate: true is nice for user dragging though)
        column: 12,
        columnOpts: {
            breakpointForWindow: true,  // test window vs grid size
            breakpoints: [
                { w: 480, c: 1 },
                { w: 700, c: 2 },
                { w: 850, c: 3 },
                { w: 950, c: 6 },
                { w: 1100, c: 8 }
            ]
        },
    };

    grid = GridStack.init(options);

    // on change compact
    grid.on('change', function () {
        grid.compact();
    });


    // Load the saved layout
    apiCall('GET', 'api/system/dashboardLayout/' + myDashboardId).then(function (response) {
        var layout = response.layout.layout;
        var layout = JSON.parse(layout);
        var dashboardName = response.layout.dashboardName;

        jQuery("h1.page-header").text(dashboardName);

        // clear the grid
        grid.removeAll();

        grid.load(layout);

        jQuery(".widget-controls").remove();

        loadDashboardView();
    });
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