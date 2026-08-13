const isElectron = typeof window.nativeAPI !== 'undefined';
currentView = null;
let allTagDetails = {};
let debugRun = false;
let autoStartMode = "first";
const passwordCheckInterval = 10 * 1000;
let isCycleMonitorEnabled = false;
let isSendingNextLine = false;

programLogicTemplate = {
    programId: 0,
    completedCycles: 0,
    selectedLine: 1, //Holds Last selected Row Number (serial-1)
    isReady: false, //if false, error in machine setup or matching head values
    nextLineNumber: 1, //Holds Next Operation Number.
    // lastExecutedLineNumber: 0, //Holds Last Operation Number
    isFinished: false, // Boolean
    programData: null, //Actual Aligned Program
    programItems: null, //HOLDS Selected Items+Qnt Details
    counters: {
        "totalItems": 0,
        "totalOperations": 0,
        "DA1": 0,
        "DA2": 0,
        "DA3": 0,
        "DB1": 0,
        "DB2": 0,
        "DB3": 0,
        "Marking1": 0,
        "Marking2": 0,
        "Marking3": 0,
        "Marking4": 0,
        "cuttings": 0,
    },
};

var programLogic;
resetProgram();


jQuery(document).ready(function () {

    PlcUIManager.init();
    loadAllTagDetails();

    // 
    setTimeout(() => {
        initProductionRuntime();
    }, 2000);

    //keep checking for password requirement, every 10 seconds
    startPasswordCheckInterval(passwordCheckInterval);

    jQuery(".nativeChannelBtns").hide();
    if (isElectron) {
        jQuery(".nativeChannelBtns").show();

        jQuery(document).on("click", ".exitBtn", function () {
            if (confirm("Are you sure you want to exit the application?")) {
                window.nativeAPI.exitApp();
            }
        });

        //  shutdownBtn
        jQuery(document).on("click", ".shutdownBtn", function () {
            if (confirm("Are you sure you want to shutdown the system?")) {
                window.nativeAPI.shutdown();
            }
        });

        // restartBtn
        jQuery(document).on("click", ".restartBtn", function () {
            if (confirm("Are you sure you want to restart the system?")) {
                window.nativeAPI.restart();
            }
        });

    }


    // loadRoute('machineParameters'); // Load the initial view
    // loadRoute('home'); // Load the initial view
    // loadRoute('homing'); // Load the initial view

    // Add click event to buttons with class 'loadRoutes'
    $(document).on('click', '.loadRoutes', function () {
        const route = $(this).data('route');
        if (route) {
            loadRoute(route);
        } else {
            console.error('No route specified for this button.');
        }
    });

    $(document).on("click", ".viewCloseBtn", function () {
        // Close the current view
        jQuery('.loadRoutes').removeClass("btn-success").addClass("btn-primary");
        currentView = null;
        $('#spaContainer').fadeOut(200, function () {
            $(this).empty().show();
        });
    });

    $(document).on("click", ".nodeProcess", function () {
        const action = $(this).data("node-action");

        apiCall("POST", "api/OpMasterFront/manageNodeApp", { action: action }).then(function (response) {
            if (response.status) {

                if (response.logs) {
                    // Show logs in a modal or alert
                    showDynamicModal("Process Logs", response.logs);
                }

            } else {
                // mtplAlerts.show("error", "Failed to write tag: " + response.message);
            }
        })
    });

    $(document).on("click", ".bulkWriteBtn", function () {
        const group = $(this).data("group");
        const tagValues = {};

        $(`.plcTagInput[data-group="${group}"]`).each(function () {
            const $input = $(this);
            const nodeId = $input.data("node-id");
            const value = $input.val();
            if (nodeId) {
                tagValues[nodeId] = value;
            }
        });

        // Call bulk write function
        writeTags(tagValues);
    });

    $(document).on("click", "#startJobcardButton", function () {
        startJobcardButton();
    });

    // btnAutoRun
    // $(document).on("click", "#btnAutoRun", function () {
    //     if (!programLogic.programData) {
    //         mtplAlerts.show("error", "No program data available to run.");
    //         return;
    //     }
    //     autoRun();

    // });


    $(document).on("click", ".data-row", function () {
        const serialNo = $(this).data("serialno");

        //if has class selected-row, remove it
        // if ($(this).hasClass("selected-row")) {
        //     $(this).removeClass("selected-row");
        //     visualizer.highlightMatchingPoints(() => false); // Clear highlight
        //     return;
        // }

        // Remove selected-row class from all rows
        $(".data-row").removeClass("selected-row");

        // Add selected-row class to the clicked row
        $(this).addClass("selected-row");

        visualizer.highlightMatchingPoints(meta =>
            meta.serialNo === serialNo
        );

        programLogic.selectedLine = serialNo;
    });

    $(document).on("click", "#btnShowHideCanvas", function () {
        jQuery("#canvasContainer").toggle();
        if (jQuery("#canvasContainer").is(":visible")) {
            jQuery(this).html('<i class="fa fa-eye-slash"></i>');
            jQuery(this).addClass("btn-danger").removeClass("btn-success");
        } else {
            jQuery(this).html('<i class="fa fa-eye"></i>');
            jQuery(this).addClass("btn-success").removeClass("btn-danger");
        }
    });

    // scrapTypeToggle click event
    $(document).on("click", ".scrapTypeToggle", function () {
        setTimeout(() => {
            updateLeadPrincherScrapeByType();
        }, 100);
    });

    $(document).on("click", "#clearGoBack", function () {

        // confirm
        if (!confirm("Are you sure you want to clear the program cycle?")) {
            return;
        }

        // Clear the program cycle details
        resetProgram();

        loadRoute('autoControl', true);

    });

    registerApiCallback("/api/jobCards/save", function (data) {
        loadPendingJobcards();
    });

    $(document).on("click", "#btnNextStep", function () {
        sendNextLine();

        // send command to PLC to reset ready state
        writeTags({ 208: true }).then(() => {
            addLog("autoCycleLog", "Ready Command sent to plc");
        }).catch(error => {
            addLog("autoCycleLog", "Failed to Send Ready Command sent to plc: " + error.message);
            // console.error("Error resetting ready state:", error);
            mtplAlerts.show("error", "Failed to reset ready state: " + error.message);
        });
    });

    // $(document).on("click", ".autoStartMode", function () {
    //     const mode = jQuery(this).data("mode");
    //     autoStartMode = mode;
    //     jQuery(".autoStartMode").removeClass("btn-success").addClass("btn-primary");
    //     jQuery(this).addClass("btn-success").removeClass("btn-primary");

    //     if (autoStartMode == 'first') {
    //         startFirst();
    //     }
    //     else {
    //         startSelected();
    //     }
    // });

    // Add item -> always append a NEW row (no auto-increment merge)
    $(document).on('click', '.selectJobcard', function () {
        const recipeId = $(this).data('recipeid');
        const itemName = $(this).data('itemname') || $(this).data('name') || $(this).data('title') || 'Item';
        $('#selectedJobsBody').append(renderRow(recipeId, itemName));
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });

    $(document).on("click", "#initDebug", function () {
        if (isCycleMonitorEnabled)
            CycleMonitor.DebugPanel.toggle();

        // Optional: shorter thresholds for smoke testing
        // ProductionRuntime.setThresholds({ pauseMs: 3000, idleMs: 30000 });
    });

});

function isCanvasVisible() {
    return jQuery("#canvasContainer").is(":visible");
}

function loadRoute(viewName, forceLoad = false) {
    if (currentView === viewName && !forceLoad) {
        return; // No need to reload the same view
    }

    skipPreloader = true;
    apiCall("GET", "/OpMaster/" + viewName, {}).then(function (response) {
        if (response.status) {
            // Load the view into the main content area
            $('#spaContainer').hide().html(response.htmlContent).fadeIn(200);

            if (viewName === 'homing') {
                // initMap(); // Initialize the map if the view is homing
            }

            if (viewName === 'programPrepare') {
                loadPendingJobcards();

                initProgramPrepareLogic();
                loadProgramItems();

            }

            if (viewName === 'autoControl') {
                const princherScrapValue = PlcDataLayer.tagValues[305] || 0;
                const leadScrapValue = PlcDataLayer.tagValues[230] || 0;
                if (jQuery(".princherScrapInput").val() === "") {
                    jQuery(".princherScrapInput").val(princherScrapValue);
                }
                if (jQuery(".leadScrapInput").val() === "") {
                    jQuery(".leadScrapInput").val(leadScrapValue);
                }

                // jQuery(".autoStartMode").removeClass("btn-success").addClass("btn-primary");
                // if (autoStartMode == "first") {
                //     jQuery(".autoStartMode[data-mode='first']").addClass("btn-success").removeClass("btn-primary");
                // }
                // else {
                //     jQuery(".autoStartMode[data-mode='selected']").addClass("btn-success").removeClass("btn-primary");
                // }

                loadProgramAlign();

                setTimeout(() => {
                    // updatePrincherScrap(leadScrapValue);
                    updateLeadPrincherScrapeByType();
                }, 500);

                if (isSingleItem()) {
                    jQuery("#skipCutOperation").prop("disabled", false);
                }
                updateDateInPLC();

            }

            PlcUIManager.init();
            currentView = viewName;
            jQuery('.loadRoutes').removeClass("btn-success").addClass("btn-primary");
            jQuery('[data-route="' + viewName + '"]').addClass("btn-success").removeClass("btn-primary");

            if (viewName == "machineParameters") {
                plcToCi4Binding();
            }
            else if (viewName == "autoControl") {
                plcToCi4BindingAuto();
                updateOnAutoBarLength();
                loadActiveAlarms();
            }


            PlcDataLayer.onTagChange(231, () => ifReadySendNextLine());
            PlcDataLayer.onTagChange(208, () => ifReadySendNextLine());

            PlcDataLayer.onTagChange(643, () => ifNeedToAskPassword());

            if (isCycleMonitorEnabled)
                CycleMonitor.resubscribe();


        } else {
            console.error('Failed to load view:', response.message);
        }
    }).catch(function (error) {
        console.error('Error loading view:', error);
    });
}

function parseMessage(data) {
    try {
        const parsedData = JSON.parse(data);
        if (parsedData.type === "plcStatus") {
            PlcDataLayer.isConnected = parsedData.status;
            if (PlcDataLayer.isConnected) {
                mtplAlerts.show("success", parsedData.message, "PLC Status");
            } else {
                mtplAlerts.show("error", parsedData.message, "PLC Status");
            }
        }
        else if (parsedData.type === "tagValues") {
            PlcDataLayer.updateFromPLC(parsedData.data);
            // Assuming parsedData.data is an array of tag objects
            // parsedData.data.forEach(tag => {
            //     const tagElement = document.querySelector(`[data-tag="${tag.name}"]`);
            //     if (tagElement) {
            //         if (tagElement.tagName.toLowerCase() === 'input' && tagElement.type === 'checkbox') {
            //             tagElement.checked = tag.value;
            //         } else if (tagElement.tagName.toLowerCase() === 'input' && tagElement.type === 'text') {
            //             tagElement.value = tag.value;
            //         } else {
            //             tagElement.textContent = tag.value; // For other elements like span, div, etc.
            //         }
            //     }
            // });
        }
        else if (parsedData.type === "notification") {
            if (parsedData.status === "success") {
                mtplAlerts.show("success", parsedData.message);
            } else if (parsedData.status === "error") {
                mtplAlerts.show("error", parsedData.message);
            } else {
                console.warn("Unknown notification status:", parsedData.status);
            }
        } else if (parsedData.type === "alarm") {
            if (parsedData.action === "resolve") {
                mtplAlerts.show("success", parsedData.message, parsedData.tagName);
            } else if (parsedData.action === "trigger") {
                if (parsedData.alarmType === "critical") {
                    mtplAlerts.show("error", parsedData.message, parsedData.tagName);
                } else {
                    mtplAlerts.show("warning", parsedData.message, parsedData.tagName);
                }
            } else {
                console.warn("Unknown alarm action:", parsedData.action);
            }

            loadActiveAlarms();
        }
        else {
            console.log("Received message:", parsedData);
        }
    } catch (error) {
        console.error("❌ Error parsing message:", error);
    }
}


let tagsLoopIntervalId = null; // Global or outer-scope variable

async function writeTags(tagMap) {
    await new Promise(resolve => setTimeout(resolve, 100)); // Add a 100ms delay

    skipPreloader = true;
    return apiCall("POST", "api/OpMasterFront/writeTags", tagMap).then(function (response) {
        if (response.status) {
            // mtplAlerts.show("success", "Tag written successfully");

            for (const tagId in tagMap) {
                if (tagMap.hasOwnProperty(tagId)) {
                    // PlcDataLayer.tagValues[tagId] = null; // Clear the value
                    const tagName = allTagDetails[tagId]?.tagName || 'Unknown';
                    addLog("tagWriteLog", "Tag <strong>" + tagId + ":" + tagName + "</strong> written with value: <strong>" + tagMap[tagId] + "</strong>");
                }
            }

        } else {
            mtplAlerts.show("error", "Failed to write tag: " + response.errorMessage);

            // clear the tag values in PlcDataLayer
            for (const tagId in tagMap) {
                if (tagMap.hasOwnProperty(tagId)) {
                    PlcDataLayer.tagValues[tagId] = null; // Clear the value
                }
            }
        }
    }).catch(function (error) {
        // console.error('Error writing tag:', error);
        // mtplAlerts.show("error", "❌ Error writing tag: " + error.message);
    });
}

function startJobcardButton() {

    //make sure atleast one input is present
    if (jQuery("[data-finalid]").length === 0) {
        mtplAlerts.show("error", "First select at least one item to prepare a program.");
        return;
    }

    //make sure atleast one input has a value
    let hasValidInput = false;
    let postData = [];

    jQuery("[data-finalid]").each(function () {
        const $input = jQuery(this);
        const recipeId = $input.data("finalid");
        const name = $input.data("name");
        const quantity = parseFloat($input.val());
        const isReverse = $input.closest("tr").find(".isReverseSwitch").is(":checked");

        if (!isNaN(quantity) && quantity > 0) {
            hasValidInput = true;
            postData.push({ recipeId, name, quantity, isReverse });
        }
    });

    if (!hasValidInput) {
        mtplAlerts.show("error", "Please enter a valid quantity for at least one recipe.");
        return;
    }

    resetProgram();
    programLogic.programItems = postData;

    loadRoute('autoControl');
}

function isSingleItem() {
    return programLogic.programItems &&
        programLogic.programItems.length === 1 &&
        programLogic.programItems[0].quantity === 1;
}

function loadProgramAlign() {

    loadActiveAlarms();

    if (!programLogic.programItems) {
        mtplAlerts.show("error", "Please prepare a program first.");
        return;
    }

    const leadScrapValue = parseFloat(jQuery(".leadScrapInput").val()) || 0;

    $finalPostData = {
        programItems: programLogic.programItems,
        leadScrap: leadScrapValue,
        tagValues: PlcDataLayer.tagValues
    }

    // Make API call to start jobcard
    apiCall("POST", "api/productionMaster/programAlign", $finalPostData).then(function (response) {
        programLogic.programData = response.data;
        if (response.status) {

            // ProductionRuntime.onProgramAligned(programLogic.programData, programLogic.programItems);
            displayProgramSummary();
            const data = generateHtmlTableFromObjectArray(programLogic.programData.program, "programOutputTable");

            jQuery(".programOutput").html(data);
            initMap();

            nextPointHighlight(programLogic.nextLineNumber);

            if (checkBarLength()) {
                programLogic.isReady = true;
            }
        }
        else {
            programLogic.isReady = false;
            jQuery(".programOutput").html("<div class='alert alert-danger'>" + response.errors + "</div>");
            initMap();
        }



    }).catch(function (error) {

    });


}

// function autoRun() {
//     setTimeout(() => {
//         nextPointHighlight(programLogic.nextLineNumber);
//         // sendNextLine();

//         if (programLogic.nextLineNumber < programLogic.programData.program.length) {
//             autoRun(); // Call autoRun again for the next point
//         } else {
//             console.log("Auto run completed.");
//             mtplAlerts.show("info", "Auto run completed.");
//         }

//         jQuery("#completedOperations").text(programLogic.nextLineNumber);
//         programLogic.nextLineNumber++; // Increment the index for the next point
//     }, 500); // Adjust the interval as needed
// }

function nextPointHighlight(startIndex) {

    if (currentView != "autoControl") {
        return;
    }

    if (!programLogic.isReady) {
        return;
    }

    startIndex = startIndex;
    //find item from programLogic.programData.program where serialNo is 0
    const item = programLogic.programData.program.find(item => item.serialNo === startIndex);
    if (!item) {
        console.error("Item with serialNo " + startIndex + " not found in programData.");
        return;
    }

    //highlight the table row based on serialNo
    jQuery(".programOutputTable .data-row").removeClass("selected-row nextopr-row");
    jQuery(`.programOutputTable .data-row[data-serialno="${startIndex}"]`).addClass("nextopr-row");

    // scroll to the row in the table
    const $row = $(`tr[data-serialno="${startIndex}"]`);
    const $container = $row.parent();

    scrollToRow($row.get(0), $container.get(0));

    if (isCanvasVisible()) {
        // For speed performance on large numbers of operation, this has been commented out.

        // visualizer.highlightMatchingPoints(meta =>
        //     meta.serialNo === startIndex
        // );

        // if (item.headType == 'Cutting' && item.itemIndex > 0) {
        //     newOffsetX = item.x * visualizer.scale - visualizer.currentBarEndX;

        //     jQuery("#producedItems").text(formatValue(item.itemIndex, 'number', null, 'IN'));

        //     // visualizer.stage.position({ x: -newOffsetX, y: 0 });
        //     visualizer.stage.to({
        //         x: -newOffsetX,
        //         y: 0,
        //         duration: 0.5, // seconds
        //         easing: Konva.Easings.EaseInOut
        //     });
        // }
    }
}

function loadPendingJobcards() {

    // jQuery(".programAlignment").hide();
    // jQuery(".prepareForProgramAlign").show();

    apiCall("GET", "api/productionMaster/pendingJobcards", {}).then(function (response) {
        if (response.status) {
            const jobcards = response.data;

            // pendingJobcardsTableBody
            const $tableBody = $("#pendingJobcardsTableBody");
            $tableBody.empty(); // Clear existing items

            jobcards.forEach(jobcard => {
                const $row = $(`
                   <tr>
                       <td>${jobcard.itemCode}</td>
                       <td>${jobcard.width}</td>
                       <td>${jobcard.thickness}</td>
                       <td>${jobcard.material}</td>
                       <td>${jobcard.programLength}</td>
                       <td>${jobcard.requiredQuantity}</td>
                       <td>${jobcard.completedQuantity}</td>
                       <td> ${jobcard.previewButton}  ${jobcard.detailsButton}  ${jobcard.selectButton}</td>
                   </tr>
               `);
                $tableBody.append($row);
            });

            if (jobcards.length === 0) {
                $tableBody.append('<tr><td colspan="8" class="text-center">No pending jobcards</td></tr>');
            }

            setTimeout(() => {
                $(document).off("input", "#jobCardSearch").on("input", "#jobCardSearch", function () {
                    const searchVal = $(this).val().toLowerCase();
                    $("#pendingJobcardsTable tbody tr").each(function () {
                        const rowText = $(this).text().toLowerCase();
                        $(this).toggle(rowText.indexOf(searchVal) !== -1);
                    });
                });
            }, 0);

        } else {
            console.error('Failed to load jobcards:', response.message);
            mtplAlerts.show("error", "Failed to load jobcards: " + response.message);
        }
    }).catch(function (error) {
        console.error('Error loading jobcards:', error);
        mtplAlerts.show("error", "❌ Error loading jobcards: " + error.message);
    });
}

function initMap() {
    // Example usage
    const scale = 1;
    visualizer = new AngleBarVisualizer('canvasContainer', scale);

    visualizer.tooltip = document.getElementById('tooltip');
    visualizer.sideAThickness = parseFloat(programLogic.programData.sideAThickness);
    visualizer.sideBThickness = parseFloat(programLogic.programData.sideAThickness);
    // visualizer.expandableHeight = parseFloat(programLogic.programData.sideAWidth) + parseFloat(programLogic.programData.sideBWidth);

    visualizer.initBar(programLogic.programData.totalLength, parseFloat(programLogic.programData.sideAWidth), parseFloat(programLogic.programData.sideBWidth));


    programLogic.programData.program.forEach(item => {
        if (item.headType == "Marking") {
            visualizer.addPoint({
                serialNo: item.serialNo,
                type: item.headType,
                x: parseFloat(item.x),
                y: parseFloat(programLogic.programData.sideBWidth / 2),
                size: parseFloat(item.value),
                value: item.value,
                side: 'B' // Default to side A if not specified
            });
        }
        else {
            visualizer.addPoint({
                serialNo: item.serialNo,
                type: item.headType,
                x: parseFloat(item.x),
                y: parseFloat(item.y),
                size: parseFloat(item.value),
                value: item.value,
                side: item.side || 'A' // Default to side A if not specified
            });
        }
    });

    visualizer.highlightMatchingPoints(meta =>
        meta.serialNo === 0
    );


    // Item 1 recipe
    // const item1Recipe = [
    //     { type: 'punch', x: 40, y: 20, size: 10, side: 'A' },
    //     { type: 'punch', x: 80, y: 15, size: 15, side: 'A' },
    //     { type: 'punch', x: 20, y: 10, size: 10, side: 'B' },
    //     { type: 'punch', x: 80, y: 15, size: 15, side: 'B' },
    //     { type: 'marker', x: 180, y: 5, value: 'HPT', side: 'A' },
    //     { type: 'marker', x: 180, y: 5, value: 'MTPL', side: 'B' },
    //     { type: 'cut', x: 200, y: 0, side: 'A' }
    // ];

    // // Item 2 recipe
    // const item2Recipe = [
    //     { type: 'punch', x: 80, y: 15, size: 18, side: 'A' },
    //     { type: 'marker', x: 150, y: 12, value: 'B2', side: 'A' },
    //     { type: 'punch', x: 80, y: 15, size: 18, side: 'B' },
    //     { type: 'marker', x: 150, y: 12, value: 'B2', side: 'B' },
    //     { type: 'cut', x: 200, y: 0, side: 'A' }
    // ];

    // visualizer.addItemRecipe(item1Recipe, 5, 0);
    // visualizer.addItemRecipe(item2Recipe, 15, 0);

    $("#btnReset").off("click").on("click", () => { if (isCanvasVisible()) visualizer.resetView(); });
    $("#btnLeft").off("click").on("click", () => { if (isCanvasVisible()) visualizer.panLeft(); });
    $("#btnRight").off("click").on("click", () => { if (isCanvasVisible()) visualizer.panRight(); });
    $("#btnCenter").off("click").on("click", () => { if (isCanvasVisible()) visualizer.panCenter(); });
    $("#btnFit").off("click").on("click", () => { if (isCanvasVisible()) visualizer.fitScreen(); });
    $("#btnExpand").off("click").on("click", () => { if (isCanvasVisible()) visualizer.toggleCanvasHeight(); });
    $("#btnFlip").off("click").on("click", () => { if (isCanvasVisible()) visualizer.toggleYOrientation(); });

}



function generateHtmlTableFromObjectArray(data, classNames = '') {
    if (!Array.isArray(data) || data.length === 0) return '<p>No data to display</p>';

    // Define columns manually: { header: "Display Name", key: "objectKey", render: (row) => ... }
    const columns = [
        { header: "Sr", key: "serialNo" },
        // { header: "I", key: "itemIndex" },
        {
            header: "Operation",
            key: "operations",
            render: row =>
                `${row.headName ?? ''} ${row.value ?? ''}`
        },
        { header: "X", key: "finalX" },
        { header: "Y", key: "y" },
        {
            header: "Program",
            key: "program",
            render: row =>
                `${row.itemCode ?? ''} [${row.itemIndex ?? ''}]`
        }
        // Add/remove columns as needed
    ];

    let html = `<table class="table table-bordered table-sm text-nowrap ${classNames}"><thead><tr>`;
    columns.forEach(col => html += `<th>${col.header}</th>`);
    html += '</tr></thead><tbody>';

    data.forEach(row => {
        html += `<tr class="data-row" data-serialno="${row.serialNo || ''}">`;
        columns.forEach(col => {
            if (col.render) {
                html += `<td>${col.render(row)}</td>`;
            } else {
                html += `<td>${row[col.key] ?? ''}</td>`;
            }
        });
        html += '</tr>';
    });

    html += '</tbody></table>';
    return html;
}


function scrollToRow(rowElement) {

    if (!rowElement) return;

    const containerElement = rowElement.closest('[style*="overflow-y:auto"]');
    if (!containerElement) return;

    const rowRect = rowElement.getBoundingClientRect();
    const containerRect = containerElement.getBoundingClientRect();

    const offset = rowRect.bottom - containerRect.bottom;

    if (offset > 0 || rowRect.top < containerRect.top) {
        containerElement.scrollBy({
            top: offset + 200, // +2 for slight margin
            behavior: 'smooth'
        });
    }
}

window.PlcDataLayer = {
    tagValues: {},
    listeners: {},
    localFlags: {},

    write(tagId, value) {
        // WebSocketClient.send({ type: "write", tagId, value });
        writeTags({ [tagId]: value });
    },

    toggle(tagId) {
        const current = this.tagValues[tagId] || false;
        this.write(tagId, current ? false : true);
    },

    onTagChange(tagId, cb) {
        if (!this.listeners[tagId]) this.listeners[tagId] = [];
        this.listeners[tagId].push(cb);

        // 🔥 Immediately call with current value if already available
        // if (tagId in this.tagValues) {
        cb(this.tagValues[tagId] ?? '');
        // }
    },

    updateFromPLC(data) {

        // console.log("Listners", this.listeners);

        for (let tagId in data) {
            let val = data[tagId];
            if (tagId == 351) {
                val = val / 10;
            }
            if (this.tagValues[tagId] !== val) {
                this.tagValues[tagId] = val;
                (this.listeners[tagId] || []).forEach(cb => cb(val));
            }
        }
    },

    resetWatchers() {
        this.listeners = {};  // Clear all previous watchers
    },

    watchDisableCondition(condStr, cb) {
        if (!condStr) return;
        const [tagId, val] = condStr.split("=");
        const tagNum = parseInt(tagId);
        console.log(`Watching disable condition for tag ${tagNum} expecting value ${val}`);
        this.onTagChange(tagNum, current => {
            console.log(`Watch condition for ${tagId}: current=${current}, expected=${val}`);
            cb(String(current) === val);
        });
    }
};


const PlcUIManager = {
    init() {
        PlcDataLayer.resetWatchers(); // Reset all tag listeners
        this.unbindAll();             // Remove old UI events
        this.initButtons();
        this.initDropdowns();
        this.initOutputs();
        this.initInputs();
        this.initListboxes();
        this.initGauges();

    },

    unbindAll() {
        $(".plc-btn").off();
        $(".plc-dropdown").off();
        // Add .off() for other types if needed
    },

    initButtons() {
        $(".plc-btn").off().each(function () {
            const $el = $(this);
            const tagId = parseInt($el.data("tag-id"));
            const behavior = $el.data("behavior");
            const indicatorId = parseInt($el.data("indicator-id"));
            const disableCond = $el.data("disable-condition");
            const onColor = $el.data("on-color") || "#28a745";  // green
            const offColor = $el.data("off-color") || "#dc3545"; // red
            const disableColor = $el.data("disable-color") || "#6c757d"; // gray


            // Check local flag mode
            const isLocal = $el.data("local-flag") === true || $el.data("local-flag") === "true";
            const isIndicatorOnly = $el.data("indicator-only") === true || $el.data("indicator-only") === "true";
            // add title attribute in button on $el
            const tag = allTagDetails[tagId];
            $el.attr("title", `${tagId}: ${tag?.tagName}`);

            // 🔁 Local-only flag logic
            if (isLocal) {
                const localKey = `flag_${tagId}`;
                PlcDataLayer.localFlags[localKey] = PlcDataLayer.localFlags[localKey] || false;


                const val = PlcDataLayer.localFlags[localKey];
                const color = val ? onColor : offColor;
                $el.css("background-color", color).css("color", "#fff");

                const label = val ? $el.data("on-label") : $el.data("off-label");
                if (label) $el.html(label);

                $el.on("click", () => {
                    PlcDataLayer.localFlags[localKey] = !PlcDataLayer.localFlags[localKey];
                    const val = PlcDataLayer.localFlags[localKey];
                    const color = val ? onColor : offColor;
                    $el.css("background-color", color).css("color", "#fff");

                    const label = val ? $el.data("on-label") : $el.data("off-label");
                    if (label) $el.html(label);
                });

                return; // 🛑 skip PLC logic
            }

            // 🔁 Indicator-only logic (no click, only watch tag change)
            if (isIndicatorOnly) {
                PlcDataLayer.onTagChange(tagId, val => {
                    const color = val ? onColor : offColor;
                    $el.css("background-color", color).css("color", "#fff");

                    const label = val ? $el.data("on-label") : $el.data("off-label");
                    if (label) $el.html(label);
                });

                return; // 🛑 skip click/behavior handlers
            }



            // Handle disable condition
            PlcDataLayer.watchDisableCondition(disableCond, isDisabled => {
                if (isDisabled) {
                    $el.prop("disabled", true).css("background-color", disableColor).css("color", "#fff");
                    $el.addClass("disabled");
                } else {

                    //get current tag value
                    const currentValue = PlcDataLayer.tagValues[tagId];
                    const color = currentValue ? onColor : offColor;
                    $el.prop("disabled", false).css("background-color", color).css("color", "#fff");
                    $el.removeClass("disabled");
                }
            });

            // Handle indicator status and update button color
            if (indicatorId) {
                PlcDataLayer.onTagChange(indicatorId, val => {

                    //if not disabled, update color
                    if (!$el.prop("disabled")) {
                        const color = val == 1 ? onColor : offColor;
                        $el.css("background-color", color).css("color", "#fff");
                    }


                    // 🔁 Update label
                    const label = (val == 1) ? $el.data("on-label") : $el.data("off-label");
                    if (label) $el.html(label);

                    // 🔁 Update confirm message
                    const confirm = (val == 1) ? $el.data("on-confirm") : $el.data("off-confirm");
                    $el.data("confirm", confirm);  // override the confirmMsg used on click
                });
            }

            // Behavior handling with confirmation logic
            if (behavior === "momentary") {
                let isPressed = false;
                let isTouch = false;

                $el.on("touchstart", (e) => {
                    isTouch = true;
                    isPressed = true;
                    PlcDataLayer.write(tagId, true);
                    e.preventDefault(); // prevent ghost click
                });

                $el.on("touchend touchcancel", () => {
                    if (isPressed) {
                        isPressed = false;
                        PlcDataLayer.write(tagId, false);
                    }
                });

                $el.on("mousedown", (e) => {
                    if (isTouch) return; // skip mouse if already touched
                    isPressed = true;
                    PlcDataLayer.write(tagId, true);
                });

                $el.on("mouseup mouseleave", () => {
                    if (isTouch) return; // skip mouse events after touch
                    if (isPressed) {
                        isPressed = false;
                        PlcDataLayer.write(tagId, false);
                    }
                });

                // PlcDataLayer.write(tagId, false);
            }
            else if (behavior === "maintain") {
                $el.on("click", () => {
                    const dynamicConfirm = $el.data("confirm");
                    if (!dynamicConfirm || confirm(dynamicConfirm)) {
                        PlcDataLayer.toggle(tagId);
                    }
                });
            } else if (behavior === "latched") {
                $el.on("click", () => {
                    const dynamicConfirm = $el.data("confirm");
                    if (!dynamicConfirm || confirm(dynamicConfirm)) {
                        PlcDataLayer.write(tagId, true);
                    }
                });
            }
        });
    },


    initDropdowns() {
        $(".plc-dropdown").off().each(function () {
            const $el = $(this);
            const tagId = parseInt($el.data("tag-id"));
            const confirmAttr = $el.data("confirm");
            const disableCond = $el.data("disable-condition");

            // ✨ Custom parse for [key:Label,...] format
            const raw = $el.attr("data-list") || "[]";
            const list = raw
                .replace(/^\[|\]$/g, "") // strip brackets
                .split(",")
                .map(entry => {
                    const [key, label] = entry.split(":");
                    return { value: parseFloat(key.trim()), label: label.trim() };
                });

            $el.empty();
            list.forEach(opt => {
                $el.append(`<option value="${opt.value}">${opt.label}</option>`);
            });

            // Auto-select based on live tag value
            PlcDataLayer.onTagChange(tagId, val => {
                $el.val(val);
            });

            $el.on("change", function () {
                const val = parseFloat(this.value);
                const confirmNow = $el.data("confirm");
                if (!confirmNow || confirm(confirmNow)) {
                    PlcDataLayer.write(tagId, val);
                }
                else {
                    // Reset to previous value if user cancels
                    const currentVal = PlcDataLayer.tagValues[tagId];
                    $el.val(currentVal);
                }
            });

            PlcDataLayer.watchDisableCondition(disableCond, isDisabled => {
                if (isDisabled) {
                    $el.prop("disabled", true).css("background-color", "#6c757d").css("color", "#fff");
                    $el.addClass("disabled");
                }
                else {
                    $el.prop("disabled", false).css("background-color", "").css("color", "");
                    $el.removeClass("disabled");
                }
            });
        });
    },

    initOutputs() {
        $(".plc-output").each(function () {
            const $el = $(this);
            const tagId = parseInt($el.data("tag-id"));
            // add title attribute in button on $el
            const tag = allTagDetails[tagId];
            $el.attr("title", `${tagId}: ${tag?.tagName}`);
            PlcDataLayer.onTagChange(tagId, val => $el.text(val));
        });
    },

    initInputs() {
        $(".plc-input").off().each(function () {
            const $el = $(this);
            const tagId = parseInt($el.data("tag-id"));
            const disableCond = $el.data("disable-condition");
            const disableColor = $el.data("disable-color") || "";

            // add title attribute in button on $el
            const tag = allTagDetails[tagId];
            $el.attr("title", `${tagId}: ${tag?.tagName}`);


            //disable inputs if user has no permission
            const disAllowedTags = window.disAllowedTags || [];
            if (disAllowedTags.includes(tagId)) {
                $el.prop("disabled", true);
            }

            // add readonly attribute if data-readonly is true
            if ($el.hasClass("virtualNumKeypad")) {
                $el.attr("readonly", true);
            }


            // Track whether user is editing (focused)
            let isEditing = false;

            $el.on("focus", () => isEditing = true);
            $el.on("change", function () {
                isEditing = false;
                PlcDataLayer.write(tagId, $(this).val());
            });

            // Optional: Write on Enter key
            $el.on("keydown", function (e) {
                if (e.key === "Enter") {
                    isEditing = false;
                    $el.blur(); // triggers blur event
                }
            });

            // Sync from tag only if not being edited
            PlcDataLayer.onTagChange(tagId, val => {
                if (!isEditing && $el.val() !== String(val)) {
                    $el.val(val);
                }
            });

            PlcDataLayer.watchDisableCondition(disableCond, isDisabled => {
                $el.prop("disabled", isDisabled);
                if (disableColor) {
                    $el.css("background-color", isDisabled ? disableColor : "");
                }
            });
        });
    },

    initListboxes() {
        $(".plc-listbox").off().each(function () {
            const $el = $(this);
            const tagId = parseInt($el.data("tag-id"));
            const disableCond = $el.data("disable-condition");
            const disableColor = $el.data("disable-color") || "";
            const confirmMsg = $el.data("confirm");
            // add title attribute in button on $el
            const tag = allTagDetails[tagId];
            $el.attr("title", `${tagId}: ${tag?.tagName}`);



            const rawList = $el.attr("data-list") || "[]";
            const items = rawList
                .replace(/^\[|\]$/g, "")
                .split(",")
                .map(entry => {
                    const [val, label] = entry.split(":");
                    return { value: parseFloat(val.trim()), label: label.trim() };
                });

            $el.empty(); // Rebuild
            items.forEach(item => {
                const $item = $(`<button type="button" class="list-group-item py-1 px-2 list-group-item-action">${item.value}. ${item.label}</button>`);
                $item.data("value", item.value);

                $item.on("click", function () {
                    if ($el.prop("disabled")) return;
                    if (confirmMsg && !confirm(confirmMsg)) return;
                    PlcDataLayer.write(tagId, item.value);
                });

                $el.append($item);
            });

            // Highlight active
            PlcDataLayer.onTagChange(tagId, val => {
                $el.find(".list-group-item").each(function () {
                    const isActive = $(this).data("value") == val;
                    $(this).toggleClass("active", isActive);
                });
            });

            // Disable logic
            PlcDataLayer.watchDisableCondition(disableCond, isDisabled => {
                $el.prop("disabled", isDisabled);
                $el.find(".list-group-item").toggleClass("disabled", isDisabled);
                if (disableColor) {
                    $el.css("background-color", isDisabled ? disableColor : "");
                }
            });
        });
    },

    initGauges() {
        $(".plc-gauge").each(function () {
            const $el = $(this);
            const tagId = parseInt($el.data("tag-id"));
            const min = parseFloat($el.data("min"));
            const max = parseFloat($el.data("max"));
            const label = $el.data("label") || "";

            let rangeConfig = [];
            const rawRanges = $el.attr("data-ranges");

            if (rawRanges) {
                try {
                    rangeConfig = JSON.parse(rawRanges);
                } catch (e) {
                    console.warn("Invalid JSON in data-ranges", e);
                }
            }

            // Default to single full-range color if nothing defined
            if (!Array.isArray(rangeConfig) || rangeConfig.length === 0) {
                rangeConfig = [{ from: min, to: max, color: "#0f75bc" }]; // default blue
            }

            // Convert to ECharts format
            const colorStops = rangeConfig.map(r => {
                const normalized = Math.min(1, r.to / max);
                return [normalized, r.color];
            });


            const chart = echarts.init($el[0]);

            const baseOption = {
                series: [{
                    type: 'gauge',
                    radius: '90%',  // Ensure it uses full available space
                    center: ['50%', '50%'],  // Center in canvas
                    min: min,
                    max: max,
                    axisLine: {
                        lineStyle: {
                            width: 8,
                            color: colorStops
                        }
                    },
                    pointer: {
                        itemStyle: {
                            color: 'auto'
                        }
                    },
                    axisTick: {
                        distance: -8,
                        length: 2,
                        lineStyle: {
                            color: '#fff',
                            width: 1
                        }
                    },
                    splitLine: {
                        distance: -8,
                        length: 6,
                        lineStyle: {
                            color: '#fff',
                            width: 1
                        }
                    },
                    axisLabel: {
                        // color: 'inherit',
                        distance: 12,
                        fontSize: 10
                    },
                    title: {
                        offsetCenter: [0, '90%'],
                        fontSize: 12
                    },
                    detail: {
                        valueAnimation: true,
                        formatter: '{value}',
                        color: 'inherit',
                        offsetCenter: [0, '40%'],
                        fontSize: 14
                    },
                    data: [{ value: 0, name: label }],
                }]
            };

            chart.setOption(baseOption);

            PlcDataLayer.onTagChange(tagId, val => {
                chart.setOption({
                    series: [{
                        data: [{ value: val, name: label }]
                    }]
                });
            });
        });
    }
};


function showDynamicModal(title, content) {
    const modalId = 'dynamicModal_' + Date.now();
    const modalHtml = `
    <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${title}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body bg-black text-gray-300">${content}</div>
        </div>
      </div>
    </div>`;

    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();

    // Optional: auto-remove from DOM when hidden
    $(`#${modalId}`).on('hidden.bs.modal', function () {
        $(this).remove();
    });
}

function pushTagToCi4($tagId, value) {
    // Ensure tagId is a number
    const tagId = parseInt($tagId);
    if (isNaN(tagId)) {
        mtplAlerts.show("error", "Invalid tagId: " + $tagId);
        return;
    }

    // Prepare data to send
    const data = { tagId: tagId, value: value };

    // Send API call to push tag to CI4
    apiCall("POST", "api/OpMasterFront/pushTagToCi4", data).then(function (response) {
        if (response.status) {
            // mtplAlerts.show("success", "Tag pushed successfully");
        } else {
            mtplAlerts.show("error", "Failed to push tag: " + response.message);
        }
    }).catch(function (error) {
        console.error('Error pushing tag:', error);
        mtplAlerts.show("error", "❌ Error pushing tag: " + error.message);
    });
}


function plcToCi4Binding() {
    //hard code some tags to store value in database on value change detected.

    // Machine Setup
    //head1
    PlcDataLayer.onTagChange(468, (val) => pushTagToCi4(468, val));
    //head2
    PlcDataLayer.onTagChange(469, (val) => pushTagToCi4(469, val));
    //head3
    PlcDataLayer.onTagChange(470, (val) => pushTagToCi4(470, val));
    //head4
    PlcDataLayer.onTagChange(471, (val) => pushTagToCi4(471, val));
    //head5
    PlcDataLayer.onTagChange(472, (val) => pushTagToCi4(472, val));
    //head6
    PlcDataLayer.onTagChange(473, (val) => pushTagToCi4(473, val));

    //mark1
    PlcDataLayer.onTagChange(579, (val) => pushTagToCi4(579, val));
    //mark2
    PlcDataLayer.onTagChange(580, (val) => pushTagToCi4(580, val));
    //mark3
    PlcDataLayer.onTagChange(581, (val) => pushTagToCi4(581, val));
    //mark4
    PlcDataLayer.onTagChange(582, (val) => pushTagToCi4(582, val));

    // machine configuration

    // Cutting Position. 318
    PlcDataLayer.onTagChange(393, (val) => pushTagToCi4(393, val));

    // Cutting Hold Down. 215
    PlcDataLayer.onTagChange(392, (val) => pushTagToCi4(392, val));

    //Punch 1 position 308
    PlcDataLayer.onTagChange(403, (val) => pushTagToCi4(403, val));

    // Punch 1 Hold Down. 218
    PlcDataLayer.onTagChange(395, (val) => pushTagToCi4(395, val));

    // Punch 2 position 310
    PlcDataLayer.onTagChange(404, (val) => pushTagToCi4(404, val));

    // Punch 2 Hold Down. 219
    PlcDataLayer.onTagChange(396, (val) => pushTagToCi4(396, val));

    // Punch 3 position 312
    PlcDataLayer.onTagChange(405, (val) => pushTagToCi4(405, val));

    // Punch 3 Hold Down. 220
    PlcDataLayer.onTagChange(397, (val) => pushTagToCi4(397, val));

    // Punch 4 position 314
    PlcDataLayer.onTagChange(406, (val) => pushTagToCi4(406, val));

    // Punch 4 Hold Down. 221
    PlcDataLayer.onTagChange(398, (val) => pushTagToCi4(398, val));

    // Punch 5 position 314
    PlcDataLayer.onTagChange(407, (val) => pushTagToCi4(407, val));

    // Punch 5 Hold Down. 221
    PlcDataLayer.onTagChange(399, (val) => pushTagToCi4(399, val));
    // Punch 6 position 314
    PlcDataLayer.onTagChange(408, (val) => pushTagToCi4(408, val));

    // Punch 6 Hold Down. 221
    PlcDataLayer.onTagChange(400, (val) => pushTagToCi4(400, val));
    // Marking Position. 296
    PlcDataLayer.onTagChange(402, (val) => pushTagToCi4(402, val));

};

function plcToCi4BindingAuto() {
    // Prevent double binding by unbinding previous handler first
    jQuery(document).off("change", ".leadScrapInput").on("change", ".leadScrapInput", function () {
        const val = parseFloat(jQuery(this).val());

        const leadScrapValue = PlcDataLayer.tagValues[230] || 0;
        if (val < leadScrapValue) {
            mtplAlerts.show("error", "Lead scrap cannot be less than " + leadScrapValue + ".");
            jQuery(this).val(leadScrapValue);
            return;
        }

        // updateLeadScrap(val);
        updatePrincherScrap(val);
    });

    jQuery(document).off("change", ".princherScrapInput").on("change", ".princherScrapInput", function () {
        const val = parseFloat(jQuery(this).val());

        const princherScrapValue = PlcDataLayer.tagValues[305] || 0;
        if (val < princherScrapValue) {
            mtplAlerts.show("error", "Princher scrap cannot be less than " + princherScrapValue + ".");
            jQuery(this).val(princherScrapValue);
            return;
        }

        updateLeadScrap(val);
    });

    // callback for bar length change
    PlcDataLayer.onTagChange(212, () => updateOnAutoBarLength());

    // PlcDataLayer.onTagChange(305, (val) => updateLeadScrap(val));
    // PlcDataLayer.onTagChange(230, (val) => updatePrincherScrap(val));

    // callback for auto program cycle
    // PlcDataLayer.onTagChange(231, () => ifReadySendNextLine());
    // PlcDataLayer.onTagChange(208, () => ifReadySendNextLine());

    //AUTO START FROM SELECTED
    PlcDataLayer.onTagChange(211, (val) => startSelected());

    //AUTO START FROM FIRST
    PlcDataLayer.onTagChange(210, (val) => startFirst());

}

function startSelected() {

    if (PlcDataLayer.tagValues[10] == true) {
        return;
    }

    autoStartMode = "selected"
    addLog("autoCycleLog", "Start from selected row");
    programLogic.nextLineNumber = programLogic.selectedLine;
    nextPointHighlight(programLogic.nextLineNumber);
    displayProgramSummary();
}

function startFirst() {
    if (PlcDataLayer.tagValues[10] == true) {
        return;
    }
    autoStartMode = "First"
    addLog("autoCycleLog", "Start from first row");

    programLogic.nextLineNumber = 1;
    nextPointHighlight(programLogic.nextLineNumber);
    displayProgramSummary();
}

function hideAlarmPopover(btn) {
    if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
        const pop = bootstrap.Popover.getInstance(btn);
        if (pop) {
            pop.hide();
        }
    } else if (typeof $.fn.popover === 'function') {
        try {
            $(btn).popover('hide');
        } catch (e) { }
    }
}

jQuery(document).on('click', function (e) {
    const $target = jQuery(e.target);
    if (!$target.closest('.alarm-info-btn, .popover').length) {
        jQuery('.alarm-info-btn').each(function () {
            hideAlarmPopover(this);
        });
    } else if ($target.closest('.alarm-info-btn').length) {
        const clickedBtn = $target.closest('.alarm-info-btn')[0];
        jQuery('.alarm-info-btn').each(function () {
            if (this !== clickedBtn) {
                hideAlarmPopover(this);
            }
        });
    }
});

function addAlarmNotification(message, type = 'danger', time = null, solution = null) {
    const $list = $('#notificationList');
    let timestamp = new Date().toLocaleString(); // Format: "DD/MM/YYYY, HH:MM:SS"
    if (time) {
        timestamp = time;
    }

    // Remove "No notifications yet." if present
    $list.find('li:contains("No notifications yet.")').remove();

    const solutionText = (solution && typeof solution === 'string' && solution.trim() !== '')
        ? solution.trim()
        : 'No solution available.';

    const safeSolution = escapeHtml(solutionText);

    const $li = $('<li>')
        .addClass(`text-${type} d-flex align-items-center justify-content-between my-1 py-1 border-bottom border-secondary border-opacity-25`)
        .html(`
            <div class="pe-2 flex-grow-1">
                <small>[${timestamp}]</small> <strong>${message}</strong>
            </div>
            <button type="button" class="btn btn-sm btn-link p-0 ms-2 text-${type} alarm-info-btn text-decoration-none"
                style="font-size: 14px; cursor: pointer;"
                data-bs-toggle="popover"
                data-bs-trigger="click"
                data-bs-placement="top"
                data-bs-title="Solution"
                data-bs-content="${safeSolution}">
                <i class="fa fa-info-circle"></i>
            </button>
        `);

    $list.prepend($li);

    const infoBtn = $li.find('.alarm-info-btn')[0];
    if (infoBtn) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
            try {
                new bootstrap.Popover(infoBtn, {
                    trigger: 'click',
                    placement: 'top',
                    title: 'Solution',
                    content: solutionText,
                    container: 'body'
                });
            } catch (e) {
                console.log("Bootstrap Popover init note:", e);
            }
        } else if (typeof $.fn.popover === 'function') {
            try {
                $(infoBtn).popover({
                    trigger: 'click',
                    placement: 'top',
                    title: 'Solution',
                    content: solutionText,
                    container: 'body'
                });
            } catch (e) {
                console.log("jQuery Popover init note:", e);
            }
        }
    }
}

function updateLeadScrap(val) {

    // loadProgramAlign();

    console.log("Updating lead scrap with value:", val);
    if (programLogic.programData == null || programLogic.programData == undefined) {
        // mtplAlerts.show("error", "Program data not loaded yet. Please start a jobcard first.");
        return;
    }


    const barLength = PlcDataLayer.tagValues[212] || 0; // Assuming 318 is the tag for bar length

    if (barLength <= 0) {
        mtplAlerts.show("error", "Bar length error");
        return;
    }

    let leadscrap = barLength - val - programLogic.programData.barLength;

    const leadScrapValue = PlcDataLayer.tagValues[230] || 0;

    if (leadscrap < leadScrapValue) {
        mtplAlerts.show("error", "Lead scrap cannot be less than " + leadScrapValue + ".");
        return;
    }

    // mtplAlerts.show("info", "Lead scrap updated: " + leadscrap);
    jQuery(".leadScrapInput").val(leadscrap);

}

function updatePrincherScrap(val) {

    // loadProgramAlign();

    console.log("Updating Princher scrap with value:", val);

    if (programLogic.programData == null || programLogic.programData == undefined) {
        // mtplAlerts.show("error", "Program data not loaded yet. Please start a jobcard first.");
        return;
    }


    const barLength = PlcDataLayer.tagValues[212] || 0; // Assuming 318 is the tag for bar length

    if (barLength <= 0) {
        mtplAlerts.show("error", "Bar length error");
        return;
    }

    let princherScrap = barLength - val - programLogic.programData.barLength;

    const princherScrapValue = PlcDataLayer.tagValues[305] || 0;

    if (princherScrap < princherScrapValue) {
        mtplAlerts.show("error", "Princher scrap cannot be less than " + princherScrapValue + ".");
        return;
    }

    // mtplAlerts.show("info", "Princher scrap updated: " + princherScrap);
    jQuery(".princherScrapInput").val(princherScrap);

}

function clearAlarms() {
    jQuery('.alarm-info-btn').each(function () {
        hideAlarmPopover(this);
        if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
            const pop = bootstrap.Popover.getInstance(this);
            if (pop) {
                pop.dispose();
            }
        } else if (typeof $.fn.popover === 'function') {
            try {
                jQuery(this).popover('dispose');
            } catch (e) { }
        }
    });
    $('#notificationList').empty();
    // $('#notificationList').append('<li class="text-muted">No notifications yet.</li>');
    // mtplAlerts.show("info", "All alarms cleared.");
}

function sendNextLine() {
    if (isSendingNextLine) {
        console.log("sendNextLine already in progress, ignoring duplicate call.");
        return;
    }
    isSendingNextLine = true;

    storeProgramState();

    addLog("autoCycleLog", "--" + programLogic.nextLineNumber + "---------------------------------------");
    addLog("autoCycleLog", "Sending next line: " + programLogic.nextLineNumber);

    if (programLogic.programData == null || programLogic.programData == undefined) {
        mtplAlerts.show("error", "Program data not loaded yet. Please start a jobcard first.");
        isSendingNextLine = false;
        return;
    }

    //verify if program align status is ready.
    if (!programLogic.isReady) {
        mtplAlerts.show("error", "Program alignment is not ready.");
        isSendingNextLine = false;
        return;
    }

    if (!checkBarLength()) {
        isSendingNextLine = false;
        return;
    }

    if (programLogic.nextLineNumber > programLogic.programData.program.length) {

        // Reset nextLineNumber to 0
        programLogic.nextLineNumber = 1;
        nextPointHighlight(programLogic.nextLineNumber);


        if (isCanvasVisible()) {
            // initMap();
        }

        programLogic.isFinished = true;
        programLogic.completedCycles++;

        displayProgramSummary();

        // tell PLC that all operations are completed
        writeTags({ 216: true }).then(() => {
            // mtplAlerts.show("success", "All operations completed and reset.");
            addLog("autoCycleLog", "================All operations completed and reset.================");
            // addLog("autoCycleLog", "All operations completed and reset.");
            isSendingNextLine = false;
        }).catch(error => {
            console.error("Error resetting operations:", error);
            mtplAlerts.show("error", "Failed to reset operations: " + error.message);
            addLog("autoCycleLog", "Failed to reset operations: " + error.message);
            isSendingNextLine = false;
        });

        mtplAlerts.show("info", "All operations completed.");
        storeProgramState(true); // store after finishing
        return;
    }

    const item = programLogic.programData.program[programLogic.nextLineNumber - 1];
    if (!item) {
        mtplAlerts.show("error", "No operation found for line number " + programLogic.nextLineNumber);

        addLog("autoCycleLog", "No operation found for line number " + programLogic.nextLineNumber);
        isSendingNextLine = false;
        return;
    }

    let tagsToWrite = {};

    const barLength = PlcDataLayer.tagValues[212] || 0; // Assuming 318 is the tag for bar length

    if (barLength <= 0 && !debugRun) {
        mtplAlerts.show("error", "Bar length error");
        isSendingNextLine = false;
        return;
    }

    const finalX = item.finalX - barLength;

    if (item.headName === 'DA1') {
        tagsToWrite = {
            321: finalX,
            339: item.y,
            206: 3 // DA1 operation code
        };
    }
    else if (item.headName === 'DA2') {
        tagsToWrite = {
            321: finalX,
            345: item.y,
            206: 4 // DA2 operation code
        };
    }
    else if (item.headName === 'DA3') {
        tagsToWrite = {
            321: finalX,
            345: item.y,
            206: 4 // DA2 operation code
        };
    }
    else if (item.headName === 'DB1') {
        tagsToWrite = {
            321: finalX,
            327: item.y,
            206: 1 // DB1 operation code
        };
    }
    else if (item.headName === 'DB2') {
        tagsToWrite = {
            321: finalX,
            333: item.y,
            206: 2 // DB2 operation code
        };
    }
    else if (item.headName === 'DB3') {
        tagsToWrite = {
            321: finalX,
            333: item.y,
            206: 2 // DB2 operation code
        };
    }
    else if (item.headName === 'Marking') {
        tagsToWrite = {
            321: finalX,
            203: item.cassetId,
            206: 5 // Marking operation code
        };
    }
    else if (item.headName === 'Cutting') {

        // skip if isSingleItem and skipCutOperation is checked 
        if (isSingleItem() && jQuery("#skipCutOperation").is(":checked")) {
            addLog("autoCycleLog", "Skipping Cutting operation as per settings.");
            mtplAlerts.show("info", "Skipping Cutting operation as per settings.");
            programLogic.nextLineNumber++;
            // if (programLogic.nextLineNumber <= programLogic.programData.program.length) {
            //     nextPointHighlight(programLogic.nextLineNumber);
            //     programLogic.selectedLine = programLogic.nextLineNumber;
            // }
            // else {
            //     programLogic.selectedLine = 1;
            // }
            // displayProgramSummary();
            // updateProgramCounters(item);
            storeProgramState(true); // store after cutting

            // Record item completion when cutting operation is completed
            if (item.itemRecipeId) {
                recordItemCompletion(item.itemRecipeId);
            }
            isSendingNextLine = false;
            sendNextLine();
            return;
        }

        tagsToWrite = {
            321: finalX,
            206: 6 // Cutting operation code
        };

        storeProgramState(true); // store after cutting


        // Record item completion when cutting operation is completed
        if (item.itemRecipeId) {
            recordItemCompletion(item.itemRecipeId);
        }

    }
    else if (item.headName === 'holdDown') {
        tagsToWrite = {
            321: finalX,
            206: 7 // holdDown operation code
        };
    }
    else {
        mtplAlerts.show("error", "Unknown operation type: " + item.headName);
        isSendingNextLine = false;
        return;
    }

    addLog("autoCycleLog", "Next Line Operation Item: " + JSON.stringify(item));
    addLog("autoCycleLog", "Sending tags to PLC: " + JSON.stringify(tagsToWrite));

    // programLogic.nextLineNumber++;

    // Write the tags to PLC
    writeTags(tagsToWrite).then(() => {
        mtplAlerts.show("success", "Operation sent: " + item.serialNo + ": " + item.headName);

        addLog("autoCycleLog", "Operation completed: " + item.headName);
        // send command to PLC to reset ready state

        writeTags({ 208: true }).then(() => {
            // programLogic.lastExecutedLineNumber = programLogic.nextLineNumber;


            // meta fields are optional, but recommended for reporting
            // ProductionRuntime.onOperationExecuted(programLogic.nextLineNumber, {
            //     opType: item.headName,   // 'Punching'|'Marking'|'Cutting'...
            //     side: item.side,            // 'A'|'B'
            //     headId: 3,            // internal head selection
            //     value: item.value,          // tool dia or marking text
            //     itemCode: item.itemCode,  // for hourly per-item rollups
            //     isLastOperation: (programLogic.nextLineNumber == programLogic.programData.length)
            // });



            programLogic.nextLineNumber++;
            if (programLogic.nextLineNumber <= programLogic.programData.program.length) {
                nextPointHighlight(programLogic.nextLineNumber);
                programLogic.selectedLine = programLogic.nextLineNumber;
            }
            else {
                programLogic.selectedLine = 1;
            }



            displayProgramSummary();
            updateProgramCounters(item);


            addLog("autoCycleLog", "Ready Command sent to plc");
            isSendingNextLine = false;
        }).catch(error => {
            addLog("autoCycleLog", "Failed to Send Ready Command sent to plc: " + error.message);
            // console.error("Error resetting ready state:", error);
            mtplAlerts.show("error", "Failed to reset ready state: " + error.message);
            isSendingNextLine = false;
        });

    }).catch(error => {
        console.error("Error sending next operation:", error);
        addLog("autoCycleLog", "Failed to send next operation: " + error.message);
        mtplAlerts.show("error", "Failed to send next operation: " + error.message);
        isSendingNextLine = false;
    });

    // jQuery(".autoStartMode").removeClass("btn-success").addClass("btn-primary");
}


function ifReadySendNextLine() {


    if (PlcDataLayer.tagValues[231] == true) {
        setDotColor("debugDot1", "green");
    }
    else {
        setDotColor("debugDot1", "red");
    }

    if (PlcDataLayer.tagValues[208] == false) {
        setDotColor("debugDot2", "green");
    }
    else {
        setDotColor("debugDot2", "red");
    }

    //check if checkbox autoRunCheckbox is checked
    const autoRunStepEnabled = jQuery("#autoRunCheckbox").is(":checked");

    if (autoRunStepEnabled) {
        mtplAlerts.show("warning", "Click 'Next Opr.' button to send next operation", "Auto Run Step is enabled");
        return false;
    }

    if ((debugRun && autoRunStepEnabled) || (PlcDataLayer.tagValues[231] == true && PlcDataLayer.tagValues[208] == false)) {
        // If ready, proceed with sending next line
        sendNextLine();


        return true;
    }
    return false;
}


function loadActiveAlarms() {
    clearAlarms();
    skipPreloader = true;
    apiCall("GET", "api/OpMasterFront/activeAlarms", {}).then(function (response) {
        if (response.status && typeof response.data === 'object' && response.data !== null && Array.isArray(response.data)) {
            clearAlarms();
            response.data.forEach(alarm => {
                // alarm should have: message, type (danger/warning/info/success)
                let alarmType = '';
                if (alarm.alarmType == 'lo' || alarm.alarmType == 'hi') {
                    alarmType = 'warning';
                } else if (alarm.alarmType == 'lolo' || alarm.alarmType == 'hihi') {
                    alarmType = 'danger';
                }

                let message = '';
                if (alarm.message) {
                    message = alarm.message;
                }
                else {
                    message = `${alarm.tagName}: Alarm Triggered: ${alarm.triggerValue}`;
                }

                addAlarmNotification(message, alarmType, alarm.triggerTime, alarm.solution);
            });
        } else {
            mtplAlerts.show("error", "Failed to load active alarms: " + (response.message || "Unknown error"));
        }
    }).catch(function (error) {
        mtplAlerts.show("error", "❌ Error loading active alarms: " + error.message);
    });
}

function loadAllTagDetails() {
    apiCall("GET", "api/OpMasterFront/allTagDetails", {}).then(function (response) {
        if (response.status && response.data) {
            allTagDetails = response.data;

            //prepare html table structure from allTagDetails json object.

            const searchTable = `<input type="text" id="tagSearch" placeholder="Search Tags" class="form-control form-control-sm mb-2">`;

            let tableHtml = searchTable + "<table class='table table-sm table-bordered'><thead><tr><th>Tag ID</th><th>Tag Name</th><th>Type</th><th>Value</th></tr></thead><tbody>";
            for (const tagId in allTagDetails) {
                if (allTagDetails.hasOwnProperty(tagId)) {
                    const tag = allTagDetails[tagId];

                    const tagContainer = `<span class="plc-output"
                                                data-ui-type="output"
                                                data-tag-id="${tagId}"
                                                data-label="${tag.tagName}">
                                            </span>`;

                    tableHtml += "<tr><td>" + tagId + "</td><td>" + tag.tagName + "</td><td>" + tag.dataType + "</td><td>" + tagContainer + "</td></tr>";
                }
            }
            tableHtml += "</tbody></table>";
            jQuery("#liveTagView").html(tableHtml);
            PlcUIManager.init();

            // Bind search event after table is rendered
            setTimeout(() => {
                $(document).off("input", "#tagSearch").on("input", "#tagSearch", function () {
                    const searchVal = $(this).val().toLowerCase();
                    $("#liveTagView table tbody tr").each(function () {
                        const rowText = $(this).text().toLowerCase();
                        $(this).toggle(rowText.indexOf(searchVal) !== -1);
                    });
                });
            }, 0);

        } else {
            mtplAlerts.show("error", "Failed to load all tag details: " + (response.message || "Unknown error"));
        }
    }).catch(function (error) {
        mtplAlerts.show("error", "❌ Error loading all tag details: " + error.message);
    });
}

function addLog($tab, $content) {
    let timestamp = new Date().toLocaleString(); // Format: "DD/MM/YYYY, HH:MM:SS"
    $logEntry = "<div class='log-entry'><small>[" + timestamp + "]:</small> " + $content + "</div>";
    jQuery("#" + $tab).prepend($logEntry);
}

function setDotColor(dotId, color) {
    const dot = document.getElementById(dotId);
    if (dot) {
        dot.classList.remove("green", "red");
        if (color === "green") dot.classList.add("green");
        if (color === "red") dot.classList.add("red");
    }
}


function checkBarLength() {
    const barLength = PlcDataLayer.tagValues[212] || 0; // Assuming 318 is the tag for bar length
    const leadScrapValue = parseFloat(jQuery(".leadScrapInput").val()) || 0;
    const princherScrapValue = parseFloat(jQuery(".princherScrapInput").val()) || 0;

    const programLength = programLogic.programData ? programLogic.programData.barLength : 0;
    const scrapType = PlcDataLayer.localFlags['flag_1'] || false; // Assuming flag_1 is used for scrap type toggle



    if (barLength < (leadScrapValue + programLength + princherScrapValue)) {
        mtplAlerts.show("error", "Bar length error");
        return false;
    }

    // if (scrapType) {
    //     // Princher scrap selected

    // }
    // else {
    //     // Lead scrap selected
    // }

    return true;
}

function updateOnAutoBarLength() {
    if (currentView != "autoControl") {
        return true;
    }

    if (jQuery("#actualBarLength").length) {
        jQuery("#actualBarLength").text(PlcDataLayer.tagValues[212] || 0);
    }

    const scrapType = PlcDataLayer.localFlags['flag_1'] || false; // Assuming flag_1 is used for scrap type toggle
    if (scrapType) {
        jQuery(".leadScrapInput").prop("disabled", true);
        jQuery(".princherScrapInput").prop("disabled", false);
        const princherScrapValue = PlcDataLayer.tagValues[305] || 0;
        jQuery(".princherScrapInput").val(princherScrapValue);
        updateLeadScrap(princherScrapValue);
    } else {
        jQuery(".leadScrapInput").prop("disabled", false);
        jQuery(".princherScrapInput").prop("disabled", true);

        const leadScrapValue = PlcDataLayer.tagValues[230] || 0;
        jQuery(".leadScrapInput").val(leadScrapValue);
        updatePrincherScrap(leadScrapValue);
    }
}

function initProgramPrepareLogic() {
    // Sortable on tbody (works for dynamic rows)
    const sortable = new Sortable(document.getElementById('selectedJobsBody'), {
        animation: 150,
        draggable: 'tr',
        handle: '.dragHandle',
        ghostClass: 'sortable-ghost',
        fallbackOnBody: true,
        forceFallback: true
    });

}


function renderRow(recipeId, itemName, value = 1, isReverse = false) {
    const safeName = escapeHtml(String(itemName));
    const isReverseChecked = isReverse ? "checked" : "";
    return `
      <tr>
        <td>
          <span class="dragHandle"><i class="fas fa-expand-arrows-alt fs-2"></i></span>
          <span class="fs-2 ms-1">${safeName}</span>
          <!--<div class="small text-muted">ID: ${escapeHtml(String(recipeId))}</div>-->
        </td>
        <td>
          <div class="input-group">
            <input type="text" data-name="${safeName}" data-finalid="${recipeId}" value="${value}" class="form-control qtyInput virtualNumKeypad" readonly>
            <button type="button" class="btn btn-danger removeRow"><i class="fas fa-times-circle"></i></button>
          </div>
        </td>
        <td>
            <div class="form-check form-switch">
                <input class="form-check-input isReverseSwitch" type="checkbox" ${isReverseChecked}>
            </div>
        </td>
      </tr>
    `;
}

function escapeHtml(str) {
    return str.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
}

function loadProgramItems() {
    if (programLogic.programItems) {
        programLogic.programItems.forEach(item => {
            const row = renderRow(item.recipeId, item.name, item.quantity, item.isReverse);
            jQuery("#selectedJobsBody").append(row);
        });
    }
}


function displayProgramSummary() {

    if (currentView != "autoControl") {
        return true;
    }

    if (!programLogic.isReady) {
        return;
    }

    jQuery("#programLength").text(formatValue(programLogic.programData.barLength, 'number', null, 'IN'));

    // // count the max itemIndex
    const maxItemIndex = Math.max(...programLogic.programData.program.map(item => item.itemIndex), 0);
    jQuery("#totalItems").text(formatValue(maxItemIndex, 'number', null, 'IN'));
    // jQuery("#totalOperations").text(formatValue(programLogic.programData.program.length, 'number', null, 'IN'));

    jQuery("#completedCycles").text(formatValue(programLogic.completedCycles, 'number', null, 'IN'));

    const nextLine = programLogic.nextLineNumber > programLogic.programData.program.length ? 1 : programLogic.nextLineNumber;

    jQuery("#nextLineNumber").text(formatValue(nextLine, 'number', null, 'IN') + "/" + formatValue(programLogic.programData.program.length, 'number', null, 'IN'));

    jQuery("#totalItemsDone").text(formatValue(programLogic.counters.totalItems, 'number', null, 'IN'));

    // punchCounters
    str = formatValue(programLogic.counters.DA1, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.DA2, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.DA3, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.DB1, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.DB2, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.DB3, 'number', null, 'IN');

    jQuery("#punchCounters").text(str);

    // MarkingCounters
    str = formatValue(programLogic.counters.Marking1, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.Marking2, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.Marking3, 'number', null, 'IN');
    str += "-" + formatValue(programLogic.counters.Marking4, 'number', null, 'IN');
    jQuery("#MarkingCounters").text(str);

    // TotalOperationsCounter
    jQuery("#TotalOperationsCounter").text(formatValue(programLogic.counters.totalOperations, 'number', null, 'IN'));

}

function updateProgramCounters(item) {

    //detect if cutting item, not lead scrap.
    if (item.headType == "Cutting" && item.itemIndex > 0) {
        programLogic.counters.totalItems++;
        programLogic.counters.cuttings++;
        programLogic.counters.totalOperations++;
    }

    else if (item.headType == "Punching") {
        if (item.headName == "DA1") {
            programLogic.counters.DA1++;
        }
        else if (item.headName == "DA2") {
            programLogic.counters.DA2++;
        }
        else if (item.headName == "DA3") {
            programLogic.counters.DA3++;
        }
        else if (item.headName == "DB1") {
            programLogic.counters.DB1++;
        }
        else if (item.headName == "DB2") {
            programLogic.counters.DB2++;
        }
        else if (item.headName == "DB3") {
            programLogic.counters.DB3++;
        }

        programLogic.counters.totalOperations++;

        // Record punch count for hourly tracking
        if (item.itemRecipeId) {
            recordPunchCount(item.itemRecipeId);
        }
    }

    else if (item.headType == "Marking") {
        const cassetId = item.cassetId || 1;
        programLogic.counters["Marking" + cassetId]++;

        programLogic.counters.totalOperations++;
    }



    displayProgramSummary();
}


function resetProgram() {
    programLogic = JSON.parse(JSON.stringify(programLogicTemplate));
    isSendingNextLine = false;
    if (isCycleMonitorEnabled)
        CycleMonitor.bindProgramLogic();         // reattach references
}


function initProductionRuntime() {

    endpoint = 'api/productionMaster/loadSettings';

    apiCall('GET', endpoint).then(function (response) {

        if (response.data.lastProgramState) {
            programLogic = response.data.lastProgramState;
            programLogic.programId = parseInt(response.data.lastProgramId) || programLogic.programId;
        }

        if (isCycleMonitorEnabled) {
            CycleMonitor.init();
            CycleMonitor.configure({
                idleReasons: response.data.idleReasons || [],
                pauseReasons: response.data.pauseReasons || [],
                pauseThresholdSec: response.data.pause_threshold_seconds || 300,
                idleThresholdSec: response.data.idle_threshold_seconds || 60
            });
        }

    });
}

// --- state ---
let __askPwdModalOpen = false;
let __askPwdIntervalId = null;

// --- modal + handler ---
function askPassword() {
    if (__askPwdModalOpen) return;

    // remove any prior residue
    const existing = document.getElementById('askPasswordModal');
    if (existing) existing.remove();

    const modalHtml = `
    <div class="modal fade" id="askPasswordModal" tabindex="-1" aria-labelledby="askPasswordLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="askPasswordLabel">Enter Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control virtualNumKeypad" id="userPassword" placeholder="Password" autofocus readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitPasswordBtn">Submit</button>
                </div>
            </div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modalEl = document.getElementById('askPasswordModal');
    const bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

    modalEl.addEventListener('shown.bs.modal', () => {
        __askPwdModalOpen = true;
        modalEl.querySelector('#userPassword')?.focus();
    });
    modalEl.addEventListener('hidden.bs.modal', () => {
        __askPwdModalOpen = false;
        modalEl.remove(); // clean up so future checks can open again
    });

    modalEl.querySelector('#submitPasswordBtn').addEventListener('click', () => {
        const pwd = modalEl.querySelector('#userPassword').value.trim();
        console.log('Entered Password:', pwd);

        writeTags({ 642: pwd }).then(() => {

            setTimeout(() => {
                const val = (window.PlcDataLayer?.tagValues?.[643]) || false;
                if (val === false) {
                    mtplAlerts.show("success", "Password accepted.");
                } else {
                    mtplAlerts.show("error", "Incorrect password. Please try again.");
                    // re-open the modal
                    askPassword();
                }
            }, 1000); // slight delay to allow PLC to process

            restartPasswordCheckInterval(passwordCheckInterval);
        }).catch(error => {
            writeTags({ 642: 0 });
            restartPasswordCheckInterval(passwordCheckInterval);
            mtplAlerts.show("error", "Failed to submit password: " + error.message);
        });


        bsModal.hide();
    });
    modalEl.querySelector('#userPassword').addEventListener('keypress', e => {
        if (e.key === 'Enter') modalEl.querySelector('#submitPasswordBtn').click();
    });

    bsModal.show();
}

// --- conditional trigger ---
function ifNeedToAskPassword() {
    const val = (window.PlcDataLayer?.tagValues?.[643]) || false;
    if (val === true && !__askPwdModalOpen) {
        askPassword();
    }
}

// --- interval controls ---
function startPasswordCheckInterval(ms = 2000) {
    stopPasswordCheckInterval();
    __askPwdIntervalId = setInterval(ifNeedToAskPassword, ms);
}

function stopPasswordCheckInterval() {
    if (__askPwdIntervalId) {
        clearInterval(__askPwdIntervalId);
        __askPwdIntervalId = null;
    }
}

function restartPasswordCheckInterval(ms = 2000) {
    stopPasswordCheckInterval();
    startPasswordCheckInterval(ms);
}



function showPlcTagIds() {
    document.querySelectorAll('.plc-input').forEach(inp => {
        const tagId = inp.getAttribute('data-tag-id');
        if (tagId && !inp.previousElementSibling?.classList?.contains('plc-tag-label')) {
            const label = document.createElement('span');
            label.textContent = `[${tagId}]`;
            label.className = 'plc-tag-label me-1';
            inp.parentNode.insertBefore(label, inp);
        }
    });
}

function storeProgramState(force = false) {
    // If no programId and not force, skip storing
    if (programLogic.programId === 0 || force) {
        endpoint = 'api/productionMaster/storeProgramState/' + programLogic.programId;
        const machineSetup = getMachineSetup();
        apiCall('POST', endpoint, { programState: programLogic, machineSetup: machineSetup }).then(function (response) {
            if (response.status) {
                programLogic.programId = parseInt(response.programId) || programLogic.programId;
                mtplAlerts.show("success", "Program state stored successfully.");
            } else {
                mtplAlerts.show("error", "Failed to store program state: " + (response.message || "Unknown error"));
            }
        }).catch(function (error) {
            mtplAlerts.show("error", "❌ Error storing program state: " + error.message);
        });
    }
}

function getMachineSetup() {
    tags = [307, 309, 311, 313, 292, 293, 294, 295, 318, 215, 308, 218, 310, 219, 312, 220, 314, 221, 296];

    tagValues = {};

    tags.forEach(tagId => {
        tagValues[tagId] = PlcDataLayer.tagValues[tagId] || 0;
    });

    return tagValues;
}

/**
 * Records the completion of a single item in the production system
 * This function is called after each cutting operation to track production progress
 * @param {number} itemRecipeId - The ID of the item recipe that was completed
 */
function recordItemCompletion(itemRecipeId) {
    // Get programId from global programLogic variable
    const programId = programLogic.programId ?? null;

    const postData = {
        itemRecipeId: itemRecipeId,
        programId: programId
    };

    // Call backend API to record item completion
    apiCall("POST", "api/productionMaster/recordItemCompletion", postData)
        .then(function (response) {
            if (response.status) {
                console.log("Item completion recorded successfully:", response.data);
                addLog("productionLog", `Item completed: Recipe ID ${itemRecipeId}, Jobcard: ${response.data.jobId}`);

                // Check if all jobcards are completed
                if (response.allJobcardsCompleted || response.data.allJobcardsCompleted) {
                    // Show prominent warning about completed jobcards
                    mtplAlerts.show("warning", response.message);
                    addLog("productionLog", "WARNING: All jobcards completed for this item!");

                    // You can add additional logic here to stop production or alert operators
                    // For example: pause the machine, show modal dialog, etc.
                    console.warn("All jobcards completed - consider stopping production for this item");
                } else {
                    // Show normal success message
                    if (response.message) {
                        mtplAlerts.show("success", response.message);
                    }
                }
            } else {
                console.error("Failed to record item completion:", response.message);
                mtplAlerts.show("error", response.message || "Failed to record item completion");
            }
        })
        .catch(function (error) {
            console.error("Error recording item completion:", error);
            mtplAlerts.show("error", "Error recording item completion");
        });
}

/**
 * Records punch operation completion for hourly punch counting
 * This function is called after each punch operation to track punch counts per hour
 * @param {number} itemRecipeId - The ID of the item recipe that was punched
 */
function recordPunchCount(itemRecipeId) {
    // Get programId from global programLogic variable
    const programId = programLogic.programId;

    if (!programId) {
        console.error("No programId found in programLogic for punch counting");
        return;
    }

    if (!itemRecipeId) {
        console.error("itemRecipeId is required for punch counting");
        return;
    }

    const postData = {
        itemRecipeId: itemRecipeId,
        programId: programId
    };

    // Call backend API to record punch count (silent - no user notifications)
    apiCall("POST", "api/productionMaster/recordPunchCount", postData)
        .then(function (response) {
            if (response.status) {
                console.log("Punch count recorded successfully:", response.data);
                addLog("productionLog", `Punch recorded: Recipe ID ${itemRecipeId}`);
            } else {
                console.error("Failed to record punch count:", response.message);
            }
        })
        .catch(function (error) {
            console.error("Error recording punch count:", error);
        });
}


function updateLeadPrincherScrapeByType() {
    const scrapType = PlcDataLayer.localFlags['flag_1'] || false; // Assuming flag_1 is used for scrap type toggle
    if (scrapType) {
        jQuery(".leadScrapInput").prop("disabled", true);
        jQuery(".princherScrapInput").prop("disabled", false);
        const princherScrapValue = PlcDataLayer.tagValues[305] || 0;
        jQuery(".princherScrapInput").val(princherScrapValue);
        updateLeadScrap(princherScrapValue);
    } else {
        jQuery(".leadScrapInput").prop("disabled", false);
        jQuery(".princherScrapInput").prop("disabled", true);

        const leadScrapValue = PlcDataLayer.tagValues[230] || 0;
        jQuery(".leadScrapInput").val(leadScrapValue);
        updatePrincherScrap(leadScrapValue);
    }
}
function updateDateInPLC() {
    const date = getCurrentDateInfo();
    writeTags({ 292: date.date, 293: date.month, 294: date.year });
}

function getCurrentDateInfo() {
    const now = new Date();

    return {
        date: now.getDate(),
        // We add 1 because JavaScript months are zero-indexed (0 = January, 11 = December)
        month: now.getMonth() + 1,
        year: now.getFullYear()
    };
}