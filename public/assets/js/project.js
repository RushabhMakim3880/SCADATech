jQuery(document).ready(function () {
    jQuery(document).on('click', '.previewProgram', function () {
        var programId = jQuery(this).data('programid');
        // Modules/plcScada/AngleBarVisualizer.js
        if (typeof AngleBarVisualizer == 'undefined') {
            // import js file
            $.getScript(base_url + "Modules/plcScada/AngleBarVisualizer.js", function () {
                loadAndPreview(programId);
            });
        }
        else {
            loadAndPreview(programId);
        }

    });
});



function loadAndPreview(programId) {
    var url = base_url + 'api/ItemRecipeMaster/getProgramDetails/' + programId;

    apiCall('GET', url).then(function (response) {

        title = response.data.itemCode;

        // Remove any existing modal with the same ID before appending a new one
        $('#programDetailsModal').remove();

        var modalHtml = `<div class="modal fade" id="programDetailsModal">
  <div class="modal-dialog modal-xxl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">${title}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
      </div>
      <div class="modal-body">
        <div id="toolbar" class="my-1">
            <button class="btn btn-secondary" id="btnReset2"><i class="fa fa-refresh"></i></button>
            <button class="btn btn-secondary" id="btnLeft2"><i class="fa fa-arrow-left"></i></button>
            <button class="btn btn-secondary" id="btnRight2"><i class="fa fa-arrow-right"></i></button>
            <button class="btn btn-secondary" id="btnCenter2"><i class="fa fa-align-center"></i></button>
            <button class="btn btn-secondary" id="btnFit2"><i class="fa fa-text-width"></i></button>
            <button class="btn btn-secondary" id="btnExpand2"><i class="fa fa-expand"></i></button>
            <button class="btn btn-secondary" id="btnFlip2"><i class="fa fa-random"></i></button>
            
        </div>
        <div id="tooltipContainer"></div>
        <div id="previewCanvasContainer"></div>
                
      </div>
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal">Close</a>
      </div>
    </div>
  </div>
</div>`;

        $('body').append(modalHtml);
        $('#programDetailsModal').modal('show');

        setTimeout(() => {
            initVisualizer(response.data);
        }, 1000);

    });
}


function initVisualizer(programData) {
    const scale = 1;
    visualizer = new AngleBarVisualizer('previewCanvasContainer', scale);

    visualizer.tooltipContainer = document.getElementById('tooltipContainer');
    visualizer.sideAThickness = parseFloat(programData.sideAThickness);
    visualizer.sideBThickness = parseFloat(programData.sideBThickness);
    // visualizer.expandableHeight = parseFloat(programData.sideAWidth) + parseFloat(programData.sideBWidth);
    visualizer.initBar(programData.programLength, parseFloat(programData.sideAWidth), parseFloat(programData.sideBWidth));

    serialNo = 0;

    let pastRef = { "A": 0, "B": 0, "N/A": 0 };

    programData.itemRecipeSteps.forEach(item => {

        if (item.measurementType === 'Incremental') {
            // Convert to absolute position
            pastRef[item.side] = pastRef[item.side] + parseFloat(item.xPos);
            item.xPos = pastRef[item.side];
        } else {
            // Absolute, so just update the reference
            pastRef[item.side] = parseFloat(item.xPos);
        }

        if (item.opType == "Marking") {
            visualizer.addPoint({
                serialNo: serialNo++,
                type: item.opType,
                x: parseFloat(item.xPos),
                y: parseFloat(programData.sideBWidth / 2),
                size: parseFloat(item.opValue),
                value: item.opValue,
                side: 'B' // Default to side B for marking
            });
        }
        else {
            visualizer.addPoint({
                serialNo: serialNo++,
                type: item.opType,
                x: parseFloat(item.xPos),
                y: parseFloat(item.yPos),
                size: parseFloat(item.opValue),
                value: item.opValue,
                side: item.side || 'A' // Default to side A if not specified
            });
        }

    });

    // visualizer.highlightMatchingPoints(meta =>
    //     meta.serialNo === 0
    // );

    // Use event delegation for dynamically inserted buttons
    $("#programDetailsModal").off("click", "#btnReset2").on("click", "#btnReset2", () => { if (isPreviewCanvasVisible()) visualizer.resetView(); });
    $("#programDetailsModal").off("click", "#btnLeft2").on("click", "#btnLeft2", () => { if (isPreviewCanvasVisible()) visualizer.panLeft(); });
    $("#programDetailsModal").off("click", "#btnRight2").on("click", "#btnRight2", () => { if (isPreviewCanvasVisible()) visualizer.panRight(); });
    $("#programDetailsModal").off("click", "#btnCenter2").on("click", "#btnCenter2", () => { if (isPreviewCanvasVisible()) visualizer.panCenter(); });
    $("#programDetailsModal").off("click", "#btnFit2").on("click", "#btnFit2", () => { if (isPreviewCanvasVisible()) visualizer.fitScreen(); });
    $("#programDetailsModal").off("click", "#btnExpand2").on("click", "#btnExpand2", () => { if (isPreviewCanvasVisible()) visualizer.toggleCanvasHeight(); });
    $("#programDetailsModal").off("click", "#btnFlip2").on("click", "#btnFlip2", () => { if (isPreviewCanvasVisible()) visualizer.toggleYOrientation(); });
}

function isPreviewCanvasVisible() {
    return jQuery("#previewCanvasContainer").is(":visible");
}