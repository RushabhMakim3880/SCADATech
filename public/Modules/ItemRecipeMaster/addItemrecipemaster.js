function getItemRecipeStepsWrapper() {
    return jQuery(".oneToManyWrapper[data-group='itemRecipeSteps']");
}

var lastItemRecipeMeasurementType = "";

function getItemRecipeMeasurementTypeField($row) {
    return $row.find("[name='itemRecipeSteps[measurementType]']");
}

function getRowMeasurementType($row) {
    return getItemRecipeMeasurementTypeField($row).val() || "";
}

function rememberItemRecipeMeasurementType(value) {
    if (value) {
        lastItemRecipeMeasurementType = value;
    }
}

function rememberLastItemRecipeMeasurementType($wrapper) {
    if (!$wrapper || !$wrapper.length) {
        $wrapper = getItemRecipeStepsWrapper();
    }

    $wrapper.find("tbody .oneToManyElement").each(function () {
        rememberItemRecipeMeasurementType(getRowMeasurementType(jQuery(this)));
    });
}

function getPreviousItemRecipeMeasurementType($row) {
    var measurementType = "";

    $row.prevAll(".oneToManyElement").each(function () {
        measurementType = getRowMeasurementType(jQuery(this));
        return !measurementType;
    });

    return measurementType || lastItemRecipeMeasurementType || "Absolute";
}

function autofillItemRecipeMeasurementType($row, value) {
    if (!$row || !$row.length) {
        return;
    }

    var $field = getItemRecipeMeasurementTypeField($row);

    if (!$field.length) {
        return;
    }

    var measurementType = value || getPreviousItemRecipeMeasurementType($row);

    if (measurementType && !$field.val()) {
        $field.val(measurementType).trigger("change");
    }

    rememberItemRecipeMeasurementType($field.val());
}

function ensureItemRecipeSerialNoField($wrapper) {
    if (!$wrapper || !$wrapper.length) {
        return;
    }

    var $headerRow = $wrapper.find("thead tr").first();
    if ($headerRow.length && !$headerRow.find(".itemRecipeSerialNoHeader").length) {
        $headerRow.prepend("<th class='itemRecipeSerialNoHeader text-center'>Serial No</th>");
    }

    $wrapper.find("tbody .oneToManyElement").each(function () {
        var $row = jQuery(this);

        if (!$row.children("td.itemRecipeSerialNoCell").length) {
            $row.prepend(
                "<td class='itemRecipeSerialNoCell text-center align-middle'>" +
                "<span class='itemRecipeSerialNoText fw-semibold'></span>" +
                "<input type='hidden' class='itemRecipeSerialNoInput' name='itemRecipeSteps[serialNo]'>" +
                "<input type='hidden' class='itemRecipeOrdIdInput' name='itemRecipeSteps[ordId]'>" +
                "</td>"
            );
        }
    });
}

function reindexItemRecipeSteps($wrapper) {
    if (!$wrapper || !$wrapper.length) {
        $wrapper = getItemRecipeStepsWrapper();
    }

    ensureItemRecipeSerialNoField($wrapper);

    var groupName = $wrapper.data("group") || "itemRecipeSteps";

    $wrapper.find("tbody .oneToManyElement").each(function (index) {
        var serialNo = index + 1;
        var uid = groupName + "_" + serialNo;
        var $row = jQuery(this);

        $row.attr("data-index", serialNo);
        $row.find(".elementTitle").text("Item " + serialNo);
        $row.find("[data-bs-target]").attr("data-bs-target", "#" + uid);
        $row.find(".uid").attr("id", uid);
        $row.find(".itemRecipeSerialNoText").text(serialNo);
        $row.find(".itemRecipeSerialNoInput").val(serialNo);
        $row.find(".itemRecipeOrdIdInput").val(serialNo);

        $row.find("input, select, textarea").each(function () {
            var $el = jQuery(this);
            var id = $el.attr("id");

            if (!id) {
                return;
            }

            $el.attr("id", id.replace(/_\d+$/, "") + "_" + serialNo);
        });
    });

    $wrapper.find(".oneToManyElement .addOneToManyElement").hide();
    $wrapper.find(".oneToManyElement").last().find(".addOneToManyElement").show();
}

jQuery(document).ready(function () {
    reindexItemRecipeSteps();
    rememberLastItemRecipeMeasurementType();

    function normalizeMarkingValue(value) {
        if (typeof value !== 'string') return value;
        return value.toUpperCase().replace(/[^A-Z0-9 \-\n\r]/g, '');
    }

    jQuery(document).on("input", ".preSelector textarea", function () {
        var details = jQuery(this).data("details");
        if (details && details.headType === 'Marking') {
            var el = this;
            var originalValue = el.value;
            var normalized = normalizeMarkingValue(originalValue);

            if (normalized !== originalValue) {
                var start = el.selectionStart;
                var beforeCursor = originalValue.substring(0, start);
                var normalizedBeforeCursor = normalizeMarkingValue(beforeCursor);

                el.value = normalized;
                el.setSelectionRange(normalizedBeforeCursor.length, normalizedBeforeCursor.length);
            }
        }
    });

    jQuery(document).on("click", ".nextBtn", function () {
        jQuery(".oneToManyWrapper").show();
        jQuery(".preSelector input, .preSelector textarea").prop("disabled", true);
        jQuery(".nextBtn").hide();
        jQuery(".resetBtn").show();
        reindexItemRecipeSteps();
        rememberLastItemRecipeMeasurementType();

        // Clear all dropdown options first
        jQuery(".headSideDropdown").empty();

        // Add blank option
        jQuery(".headSideDropdown").append(jQuery("<option></option>").attr("value", "").text("--Select--"));

        // Iterate each preSelector input to prepare options
        jQuery(".preSelector input, .preSelector textarea").each(function () {
            var details = jQuery(this).data("details");
            var rawValue = jQuery(this).val();

            if (rawValue === "") {
                return;
            }

            var values = [rawValue];
            if (jQuery(this).is("textarea")) {
                values = rawValue.split("\n").filter(v => v.trim() !== "");
            }

            values.forEach(function (value) {
                var headType = details.headType;
                var side = details.side;
                var label = side == "N/A" ? headType + "-" + value : headType + "-" + side + "-" + value;

                if (headType === 'Marking') {
                    value = normalizeMarkingValue(value);
                }

                if (label) {
                    var option = jQuery("<option></option>")
                        .attr("value", headType)
                        .attr("data-side", side)
                        .attr("data-headvalue", value)
                        .attr("data-optype", headType)
                        .text(label);

                    jQuery(".headSideDropdown").append(option);
                }
            });
        });

        // 🔥 Auto-select after options created
        jQuery(".oneToManyElement").each(function () {
            var opType = jQuery(this).find("[name='itemRecipeSteps[opType]']").val();
            var opValue = jQuery(this).find("[name='itemRecipeSteps[opValue]']").val();
            var side = jQuery(this).find("[name='itemRecipeSteps[side]']").val();

            var dropdown = jQuery(this).find(".headSideDropdown");
            var found = false;

            dropdown.find("option").each(function () {
                var $option = jQuery(this);
                if (
                    $option.data("optype") === opType &&
                    $option.data("side") === side &&
                    $option.attr("data-headvalue") === opValue
                ) {
                    dropdown.find("option").prop("selected", false); // Clear
                    $option.prop("selected", true);
                    dropdown.trigger("change");
                    found = true;
                    console.log("Auto-selected: " + $option.text());
                    return false;
                }
            });

            if (!found) {
                dropdown.val("");
            }
        });

    });

    // Insert a new row AFTER the clicked row
    jQuery(document).on("click", ".insertOneToManyElement", function () {
        var $clickedElement = $(this);
        var $wrapper = $clickedElement.closest(".oneToManyWrapper");

        // if wrapper not found via DOM, fall back to data-group
        if ($wrapper.length === 0) {
            $wrapper = $(`.oneToManyWrapper[data-group="${$clickedElement.data('group')}"]`);
        }

        var $currentRow = $clickedElement.closest(".oneToManyElement");
        var measurementTypeForNewRow = getRowMeasurementType($currentRow) || lastItemRecipeMeasurementType;
        const groupName = $wrapper.data('group');
        const currentIndex = Number($currentRow.data("index")) || 0;
        const newIndex = currentIndex + 1;
        const newUid = `${groupName}_${newIndex}`;

        // Destroy select2 (if any) on current row before cloning
        $currentRow.find('select').each(function () {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });

        // Clone current row
        var $newRow = $currentRow.clone();

        // Assign index + reset values
        $newRow.attr("data-index", newIndex);
        $newRow.find(".elementTitle").text("Item " + newIndex);
        $newRow.find('[data-bs-target]').attr('data-bs-target', `#${newUid}`);
        $newRow.find(".uid").attr("id", newUid);

        $newRow.find("input, select, textarea").val("");
        $newRow.find("input[type=checkbox]").prop("checked", false);

        // Update name/id attributes in NEW row if they contain an index (e.g., items[3][field] / field_3)
        $newRow.find("input, select, textarea").each(function () {
            const $el = $(this);
            const name = $el.attr("name");
            const id = $el.attr("id");

            if (name) $el.attr("name", name.replace(/\[(\d+)\]/, `[${newIndex}]`));
            if (id) $el.attr("id", id.replace(/_(\d+)$/, `_${newIndex}`));

            $el.removeClass('select2-initialized');
        });

        // Insert after current row
        $currentRow.after($newRow);

        // Reindex all following rows to keep sequence & unique IDs/names
        var idx = newIndex + 1;
        $newRow.nextAll(".oneToManyElement").each(function () {
            const $row = $(this);
            const uid = `${groupName}_${idx}`;

            $row.attr("data-index", idx);
            $row.find(".elementTitle").text("Item " + idx);
            $row.find('[data-bs-target]').attr('data-bs-target', `#${uid}`);
            $row.find(".uid").attr("id", uid);

            $row.find("input, select, textarea").each(function () {
                const $el = $(this);
                const name = $el.attr("name");
                const id = $el.attr("id");
                if (name) $el.attr("name", name.replace(/\[(\d+)\]/, `[${idx}]`));
                if (id) $el.attr("id", id.replace(/_(\d+)$/, `_${idx}`));
                $el.removeClass('select2-initialized');
            });

            idx++;
        });

        // Hide all "add" buttons except on the last row
        $wrapper.find(".oneToManyElement .addOneToManyElement").hide();
        $wrapper.find(".oneToManyElement").last().find(".addOneToManyElement").show();
        reindexItemRecipeSteps($wrapper);
        autofillItemRecipeMeasurementType($newRow, measurementTypeForNewRow);

        // Cleanup any stale select2 class markers on current+new rows
        $currentRow.find("select.select2:not(.select2-hidden-accessible)").removeClass('select2-initialized');
        $newRow.find("select.select2:not(.select2-hidden-accessible)").removeClass('select2-initialized');

        // Re-init UI + optional recalc
        applyUiLibrary();
        if (typeof calculate === 'function') {
            calculate();
        }

        // Fire custom event
        const event = new CustomEvent('oneToManyElementInserted', {
            detail: { element: $newRow }
        });
        window.dispatchEvent(event);
    });



    jQuery(document).on("click", ".resetBtn", function () {
        jQuery(".preSelector input, .preSelector textarea").prop("disabled", false);
        jQuery(".oneToManyWrapper").hide();
        jQuery(".nextBtn").show();
        jQuery(".resetBtn").hide();
    });

    jQuery(document).on("change", ".headSideDropdown", function () {
        var selectedOption = jQuery(this).find("option:selected");
        var side = selectedOption.data("side");
        var value = selectedOption.data("headvalue");
        var optype = selectedOption.data("optype");

        // Set the value of the hidden inputs
        jQuery(this).closest(".oneToManyElement").find("[name='itemRecipeSteps[opType]']").val(optype);
        jQuery(this).closest(".oneToManyElement").find("[name='itemRecipeSteps[opValue]']").val(value);
        jQuery(this).closest(".oneToManyElement").find("[name='itemRecipeSteps[side]']").val(side);

    });

    jQuery(document).on("change", "[name='itemRecipeSteps[measurementType]']", function () {
        rememberItemRecipeMeasurementType(jQuery(this).val());
    });

    jQuery(document).on("click", ".removeOneToManyElement", function () {
        var $wrapper = jQuery(this).closest(".oneToManyWrapper");

        setTimeout(function () {
            reindexItemRecipeSteps($wrapper);
            rememberLastItemRecipeMeasurementType($wrapper);
        }, 0);
    });

    window.addEventListener("oneToManyElementAdded", function (event) {
        var $element = event && event.detail && event.detail.element ? jQuery(event.detail.element) : jQuery();
        var $wrapper = $element.closest(".oneToManyWrapper");
        reindexItemRecipeSteps($wrapper.length ? $wrapper : getItemRecipeStepsWrapper());
        autofillItemRecipeMeasurementType($element);
    });

    window.addEventListener("oneToManyElementInserted", function (event) {
        var $element = event && event.detail && event.detail.element ? jQuery(event.detail.element) : jQuery();
        var $wrapper = $element.closest(".oneToManyWrapper");
        reindexItemRecipeSteps($wrapper.length ? $wrapper : getItemRecipeStepsWrapper());
        autofillItemRecipeMeasurementType($element);
    });


});

jQuery(document).ready(function () {


    registerApiCallback("ItemRecipeMaster/addItemrecipemaster", function (data) {
        // setTimeout(function () {
        //     jQuery(".resetBtn").hide();
        //     jQuery(".oneToManyWrapper").hide();
        // }, 500);

    });

    registerApiCallback("ItemRecipeMaster/editItemrecipemaster", function (data) {
        setTimeout(function () {
            reindexItemRecipeSteps();
            rememberLastItemRecipeMeasurementType();
            // jQuery(".resetBtn").hide();
            // jQuery(".oneToManyWrapper").hide();

            const usedKeys = new Set();

            jQuery(".oneToManyElement").each(function () {
                const opType = jQuery(this).find("[name='itemRecipeSteps[opType]']").val();
                const opValue = jQuery(this).find("[name='itemRecipeSteps[opValue]']").val();
                const side = jQuery(this).find("[name='itemRecipeSteps[side]']").val();

                const key = `${opType}__${side}__${opValue}`;
                if (usedKeys.has(key)) return;

                jQuery(".preSelector input, .preSelector textarea").each(function () {
                    const details = jQuery(this).data("details");
                    if (details.headType === opType && details.side === side) {
                        if (jQuery(this).is("textarea")) {
                            let currentVal = jQuery(this).val();
                            let lines = currentVal ? currentVal.split("\n") : [];
                            if (!lines.includes(opValue)) {
                                lines.push(opValue);
                                jQuery(this).val(lines.join("\n"));
                            }
                            return false;
                        } else if (jQuery(this).val() === "") {
                            jQuery(this).val(opValue);
                            usedKeys.add(key);
                            return false; // exit input loop
                        }
                    }
                });
            });

            jQuery(".nextBtn").trigger("click");
            reindexItemRecipeSteps();
            rememberLastItemRecipeMeasurementType();
        }, 500);
    });
});


// window.onApiReady = function () {

// };
