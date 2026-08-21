// Function to create and insert the preloader into the DOM
var skipPreloader = false;
function createPreloader() {
    const preloader = document.createElement("div");
    preloader.id = "customPreloader";
    preloader.style.position = "fixed";
    preloader.style.top = "0";
    preloader.style.left = "0";
    preloader.style.width = "100%";
    preloader.style.height = "100%";
    preloader.style.background = "rgba(255, 255, 255, 0.5)";
    preloader.style.display = "flex";
    preloader.style.justifyContent = "center";
    preloader.style.alignItems = "center";
    preloader.style.zIndex = "999999";
    preloader.style.opacity = "0";
    preloader.style.transition = "opacity 0.3s ease-in-out";

    const spinner = document.createElement("div");
    spinner.style.width = "50px";
    spinner.style.height = "50px";
    spinner.style.border = "5px solid rgba(0, 0, 0, 0.1)";
    spinner.style.borderTop = "5px solid #000";
    spinner.style.borderRadius = "50%";
    spinner.style.animation = "spin 1s linear infinite";

    preloader.appendChild(spinner);
    document.body.appendChild(preloader);

    // Add keyframe animation for spinner
    const style = document.createElement("style");
    style.innerHTML = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}

// Function to show the preloader
function showPreloader() {
    if (!skipPreloader) {
        const preloader = document.getElementById("customPreloader");
        if (preloader) {
            preloader.style.opacity = "1";
            preloader.style.pointerEvents = "auto";
        }
    }
}

// Function to hide the preloader with fade effect
function hidePreloader() {
    const preloader = document.getElementById("customPreloader");
    if (preloader) {
        preloader.style.opacity = "0";
        setTimeout(() => {
            preloader.style.pointerEvents = "none";
        }, 300); // Match the transition duration
    }
    skipPreloader = false;
}

// Initialize preloader
createPreloader();
showPreloader();

jQuery(document).ready(function () {

    // if token does not exists on local storage then redirect to login page, if not already in login page.
    // if (!localStorage.getItem('jwt') && !window.location.href.includes("auth/login")) {
    //     // remove jwt from cookie set by document.cookie = "jwt=" + token + "; path=/; Secure; SameSite=Strict";
    //     document.cookie = "jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; Secure; SameSite=Strict";
    //     window.location.reload();
    // }

    userProfile();
    applyUiLibrary();

    $(document).on("change", "[name]", function () {
        updateDependentFields();
    });

    /*************************************
        Logout User Functionality
    *************************************/

    jQuery(".appLogOut").click(function () {
        appLogOut();
    });

    jQuery(document).on('click', 'a.editProfile', function (e) {
        e.preventDefault();
        editUserProfile();
    });

    /************************************************************************************
       on focus, automatically open select2 dropdown to shift focus to search input.
    *************************************************************************************/
    // $(document).on('focus', '.select2-hidden-accessible', function () {
    // const $select = $(this);
    // if (!$select.prop('multiple')) {
    //     $select.select2('open');
    // }
    // });

    jQuery(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
        jQuery(this).closest(".select2-container").siblings('select:enabled').select2('open');
    });


    /****************************************************************
        Select2 on change event to shift focus to next input
    *****************************************************************/
    jQuery("body").on("select2:select", ".select2", function (e) {

        // console.log("PRICE", getSelect2Attr(e, 'price'));

        // Get the closest form of the select element
        // var selectedOption = $(e.target).find(':selected');
        // console.log(selectedOption.data('price'));
        var form = $(e.target).closest('form');
        focusNextFormInput(form, e.target);

    });

    jQuery(document).on("change", ".datePicker", function (e) {
        // Get the closest form of the select element
        var form = $(e.target).closest('form');
        focusNextFormInput(form, e.target);
    });



    /****************************************************************
        sidebar menu search code.
    *****************************************************************/
    jQuery('#menuSearch').on('keyup', function () {
        var theme = $(this).data("theme");
        var value = $(this).val().trim().toLowerCase(); // Trim whitespace and convert to lowercase

        if (theme == 1) {
            $(".menu-item").each(function () {
                var menuItem = $(this);
                var menuText = menuItem.find('.menu-text').text().toLowerCase();

                // Check if the menu text matches the search value
                if (menuText.includes(value)) {
                    // Show the menu item
                    menuItem.show();

                    // Expand the parent dropdowns
                    menuItem.parents('.menu-submenu').show();
                    menuItem.parents('.menu-item').addClass('show').addClass('active');
                    menuItem.parents('.has-sub').find('.menu-link').addClass('active');
                } else {
                    // Hide the menu item if it doesn't match
                    menuItem.hide();
                    menuItem.parents('.menu-item').removeClass('show').removeClass('active');
                    menuItem.parents('.has-sub').find('.menu-link').removeClass('active');
                }
            });

            // Deselect all menu links if the search input is empty
            if (value === '') {
                $(".menu-item").show(); // Show all menu items
                $(".menu-item").removeClass('active'); // Collapse all submenus

                $(".menu-item has-sub").removeClass('show'); // Collapse all submenus
                $(".menu-item has-sub").removeClass('active'); // Collapse all submenus
                $(".menu-item has-sub").hide();
                $(".MenuItems").removeClass('active'); // Collapse all submenus
                $(".menu-item has-sub").hide(); // Hide all submenus
                $(".menu-link").removeClass('active'); // Remove 'active' class from all menu links
                $(".menu-submenu").hide(); // Hide all submenus
            }
        }
        else if (theme == 2) {
            $(".menuItem").each(function () {
                var menuItem = $(this);
                var menuText = menuItem.find('.nav-link-title').text().toLowerCase();

                // Check if the menu text matches the search value
                if (menuText.includes(value)) {
                    // Show the menu item
                    menuItem.show();
                    menuItem.addClass("active");

                    menuItem.parents('.menuItemParent').addClass('show').addClass('active');
                    menuItem.parents('.menuItemParent').siblings('.menuItem').addClass('show').addClass('active').show();

                    // menuItem.parents('.has-sub').find('.menu-link').addClass('active');
                } else {
                    // Hide all menu items not having "show" and "active" class
                    // if (!menuItem.hasClass('show') && !menuItem.hasClass('active')) {
                    menuItem.hide();
                    // }
                }
            });

            // Deselect all menu links if the search input is empty
            if (value === '') {
                $(".menuItem").show();
                $(".menuItem").removeClass('active'); // Collapse all submenus
                $(".menuItemParent").removeClass('show').removeClass('active'); // Collapse all submenus
            }
        }


    });

    /****************************************************************
       Copy to clipboard functionality
    *****************************************************************/
    jQuery(document).on('click', '.copyToClipboard', function () {
        var copyContent = $(this).data('copycontent');
        var tempElement = $('<div>').text(copyContent).appendTo('body');
        var range = document.createRange();

        range.selectNode(tempElement[0]);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        tempElement.remove();
        alert('Text copied to clipboard: ' + copyContent);
    });

    /****************************************************************
       Numeric input only functionality
    *****************************************************************/
    jQuery("body").on("keypress", ".numberInput", function (evt) {
        return onlyNumberKey(evt);
    });

    jQuery("body").on("change", ".numberInput", function (evt) {
        var str = jQuery(this).val();
        //        Replace anything other than numeric characters
        //        str = str.replace(/\D/g,'');

        //        replace all but keep . (dot), - (minus) and numeric charactors to capture positive/negetive float values only
        str = str.replace(/[^\d.-]/g, '');
        $limit = parseInt(jQuery(this).data("length"));
        if ($limit)
            str = str.slice(str.length - $limit);
        jQuery(this).val(str);
    });

    /****************************************************************
       Focus on first input field
    *****************************************************************/
    jQuery(".inputFocus").first().focus();

    /****************************************************************
       Enter key to move to next input field
    *****************************************************************/
    $('body').on("keydown", "input,select", function (e) {
        if (!$(this).hasClass("e2t_ignore")) {
            var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
            if (key == 13) {
                if (e.ctrlKey) {
                    //submit form.
                    $(this).closest('form').submit();
                }
                else {
                    if ($(this).attr("type") != "submit") {
                        e.preventDefault();
                        var inputs = $(this).closest('form').find(':input:not(:disabled):not([readonly]):visible:not(.e2t_ignore)');
                        inputs.eq(inputs.index(this) + 1).focus();
                    }
                }
            }

        }
    });

    jQuery(document).on("click", ".exportTable", function () {
        exportHtmlTableAsExcel(this);
    });

});


/****************************************************************
    Alert Sound Functionality       
*****************************************************************/

// --- Standalone sound toggle flag (independent of mtplAlerts cached config) ---
// Initialise from localStorage, fallback to appSettings default
(function initSoundFlag() {
    const saved = localStorage.getItem('notificationSoundEnabled');
    if (saved !== null) {
        window._notificationSoundEnabled = saved === '1';
    } else {
        window._notificationSoundEnabled = parseInt(window.appSettings?.notificationPlaySound) ? true : false;
    }
})();

function toggleNotificationSound() {
    const next = !window._notificationSoundEnabled;
    window._notificationSoundEnabled = next;
    localStorage.setItem('notificationSoundEnabled', next ? '1' : '0');

    // Update button icon
    const icon = document.getElementById('soundToggleIcon');
    if (icon) {
        icon.className = next ? 'fa fa-volume-up' : 'fa fa-volume-mute';
    }

    mtplAlerts.show(
        'success',
        next ? 'Notification sound enabled' : 'Notification sound muted',
        'Sound'
    );
}

var alarmTypes = {
    success: new Audio(base_url + "assets/audio/Success.mp3"),
    warning: new Audio(base_url + "assets/audio/Warning.mp3"),
    info: new Audio(base_url + "assets/audio/Info.mp3"),
    danger: new Audio(base_url + "assets/audio/Danger.mp3"),
    error: new Audio(base_url + "assets/audio/Danger.mp3"),
};

function playAlarm(type) {
    // Guard: respect the standalone sound toggle flag
    if (!window._notificationSoundEnabled) return;

    if (alarmTypes[type] && (typeof notificationSound !== 'undefined' ? notificationSound : true)) {
        alarmTypes[type].currentTime = 0;
        alarmTypes[type].play();
    }
}

/*************************************
    Normal Select2 functions
 ************************************/
function applySelect2All() {
    $("select.select2:not(.select2-hidden-accessible)").each(function () {
        if (!$(this).hasClass("select2-initialized")) {
            applySelect2(this);
            $(this).addClass("select2-initialized");
        }
    });
}



function applySelect2(ele) {
    // console.log("Applying Select2 to element");
    if (jQuery(ele).length) {
        if (!jQuery(ele).data('select2')) {

            var selectType = jQuery(ele).data('selecttype');
            var endpoint = jQuery(ele).data('endpoint');

            // Determine correct dropdown parent
            var $parentModal = jQuery(ele).closest(".modal-content");
            var dropdownParent = $parentModal.length ? $parentModal : jQuery(document.body);
            var allowClear = true;
            // if element has required attribute then set allowClear to false
            if (jQuery(ele).attr("required") || jQuery(ele).prop("required")) {
                allowClear = false;
            }


            if (selectType == 'tags') {
                jQuery(ele).select2({
                    width: '100%',
                    allowClear: allowClear,
                    placeholder: 'Select an option',
                    dropdownParent: dropdownParent,
                    tags: true,
                    tokenSeparators: [','],
                });
            } else if (selectType == 'ajax') {
                jQuery(ele).select2({
                    width: '100%',
                    allowClear: allowClear,
                    placeholder: 'Select an option',
                    dropdownParent: dropdownParent,
                    ajax: {
                        transport: function (params, success, failure) {
                            skipPreloader = true;
                            apiCall("POST", params.url, params)
                                .then(response => {
                                    if (response && typeof response === 'object') {
                                        success(response.data);
                                    } else {
                                        failure({ status: 'error', message: 'Invalid response format' });
                                    }
                                    skipPreloader = false;
                                })
                                .catch(error => {
                                    console.error('API Call Failed:', error);
                                    failure({ status: 'error', message: error.message || 'Request failed' });
                                    skipPreloader = false;
                                });
                        },
                        url: endpoint,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.items || [],
                                pagination: {
                                    more: (params.page * 50) < (data.totalCount || 0)
                                }
                            };
                        },
                        delay: 500,
                        cache: true
                    },
                    minimumInputLength: 1,
                    templateResult: formatSelect2AjaxResults,
                    templateSelection: function (item) { return item.text || ''; },
                    escapeMarkup: function (markup) { return markup; }
                });
            } else {
                jQuery(ele).select2({
                    width: '100%',
                    // theme: "bootstrap-5",
                    allowClear: allowClear,
                    placeholder: 'Select an option',
                    dropdownParent: dropdownParent,
                    templateResult: formatSelect2Results,
                    templateSelection: formatSelect2Selection,
                    // templateSelection: function (item) { return item.text || ''; },
                    escapeMarkup: function (markup) { return markup; }
                });
            }
        }
    }
}

function formatSelect2Results(item) {
    if (!item.id) return item.text;

    let parts = item.text.split('||');
    let main = parts[0]?.trim();
    let parent = parts[1]?.trim();

    let parentTrail = '';
    if (parent) {
        parentTrail = `<div style="font-size: 10px; opacity:0.7">(${parent})</div>`;
    }

    return `<div>${main}${parentTrail}</div>`;
}

function formatSelect2Selection(item) {
    if (!item.id) return item.text;

    let parts = item.text.split('||');
    let main = parts[0]?.trim();

    return main;
}


function formatSelect2AjaxResults(item) {
    if (!item.id) return item.text;

    let parentTrail = '';
    if (item.subtext) {
        parentTrail = `<div style="font-size: 10px; opacity:0.7">(${item.subtext})</div>`;
    }

    return `<div>${item.text}${parentTrail}</div>`;
}

// // Ensure dropdown remains positioned correctly when modal is scrolled
// jQuery(document).on('select2:open', function (e) {
//     var ele = jQuery(e.target);
//     var modal = ele.closest('.modal');

//     if (modal.length) {
//         setTimeout(() => {
//             var dropdown = ele.data('select2').$dropdown;
//             dropdown.css({
//                 position: 'absolute',
//                 top: ele.offset().top + ele.outerHeight(),
//                 left: ele.offset().left,
//                 width: ele.outerWidth(),
//                 zIndex: 99999
//             });
//         }, 10);
//     }
// });



// use this function to retrive attribute value from select2 element.
function getSelect2Attr(e, attributeName) {
    var $select = $(e.target);
    var $selectedOption = $select.find(':selected'); // Works for static dropdowns

    var attrValue = '';

    // 1️⃣ First, check if the attribute exists in the DOM-selected <option>
    if ($selectedOption.length) {
        attrValue = $selectedOption.attr('data-' + attributeName);
        if (attrValue) return attrValue; // If found, return immediately
    }

    // 2️⃣ If the dropdown is AJAX-loaded, check in e.params.data
    if (e.params && e.params.data) {
        attrValue = e.params.data[attributeName] || ''; // Check in AJAX response
    }

    return attrValue;
}



//Select2 functions for dynamic elements
// const observer = new MutationObserver(function (mutationsList) {
//     mutationsList.forEach(function (mutation) {
//         mutation.addedNodes.forEach(function (node) {
//             if ($(node).is('select.select2')) {
//                 applySelect2(node);
//             }
//         });
//     });
// });

// Start observing the document body for added nodes
// observer.observe(document.body, { childList: true, subtree: true });

/*************************************
    Other Core Functions Below
 ************************************/

function onlyNumberKey(evt) {
    // Allow digits (0-9), dot (.), and minus (-)
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode;
    // 48-57: 0-9, 46: dot, 45: minus
    if ((ASCIICode >= 48 && ASCIICode <= 57) || ASCIICode == 46 || ASCIICode == 45)
        return true;
    return false;
}

function editUserProfile() {
    const user = localStorage.getItem('userProfile');
    const editUserUrl = base_url + 'users/editUser/' + JSON.parse(user).userId;
    window.location.href = editUserUrl;
}

function appLogOut() {

    //confirm first.
    if (!confirm("Are you sure you want to logout?")) {
        return;
    }

    apiCall("GET", "api/auth/logout", {}).then(function (response) {
        document.cookie = "jwt=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; Secure; SameSite=Lax";
        document.cookie = "refreshToken=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; Secure; SameSite=Lax";

        // remove jwt from local storage
        localStorage.removeItem('jwt');

        // redirect to login page
        const urlencodedUrl = encodeURIComponent(window.location.href);
        window.location.href = base_url + 'auth/login?redirect=' + urlencodedUrl;
    }).catch(function (error) {
        console.error("Logout API call failed:", error);
    });
}

function userProfile() {
    var userProfile = localStorage.getItem('userProfile');
    if (userProfile) {
        userProfile = JSON.parse(userProfile);

        jQuery(".userProfileName").text(userProfile.firstName + " " + userProfile.lastName);
        jQuery(".userEmail").text(userProfile.email);
        jQuery(".userGroupName").text(userProfile.groupName);
        // set user profile image
        if (userProfile.profileImage) {
            jQuery(".userProfileImage").attr("src", userProfile.profileImage);
        }
    }
}


/*************************************
 * PHP Like date time format function
 ************************************/

function formatDateTime(format, date = new Date()) {

    if (format == "date")
        format = window.appSettings.dateFormat;
    else if (format == "time")
        format = window.appSettings.timeFormat;
    else if (format == "datetime")
        format = window.appSettings.dateTimeFormat;


    if (typeof date === "string") {
        date = date.includes("-") ? new Date(date.replace(" ", "T")) : new Date(parseInt(date) * 1000);
    }

    if (!(date instanceof Date) || isNaN(date)) return "Invalid Date";

    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const shortDayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const shortMonthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    const pad = (n, width = 2) => String(n).padStart(width, '0');
    const isLeapYear = (year) => (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
    const getDaysInMonth = (year, month) => new Date(year, month, 0).getDate();

    const hours12 = date.getHours() % 12 || 12;
    const timezoneOffset = -date.getTimezoneOffset();
    const timezoneOffsetHours = Math.floor(Math.abs(timezoneOffset) / 60);
    const timezoneOffsetMinutes = Math.abs(timezoneOffset) % 60;
    const timezoneSign = timezoneOffset >= 0 ? "+" : "-";

    const map = {
        "d": pad(date.getDate()), "D": shortDayNames[date.getDay()], "j": date.getDate(), "l": dayNames[date.getDay()],
        "N": date.getDay() === 0 ? 7 : date.getDay(), "S": ["st", "nd", "rd"][(date.getDate() % 10) - 1] || "th", "w": date.getDay(),
        "z": Math.floor((date - new Date(date.getFullYear(), 0, 1)) / 86400000), "W": Math.ceil((((date - new Date(date.getFullYear(), 0, 4)) / 86400000) + new Date(date.getFullYear(), 0, 4).getDay() + 1) / 7),
        "F": monthNames[date.getMonth()], "m": pad(date.getMonth() + 1), "M": shortMonthNames[date.getMonth()], "n": date.getMonth() + 1,
        "t": getDaysInMonth(date.getFullYear(), date.getMonth() + 1), "L": isLeapYear(date.getFullYear()) ? 1 : 0, "o": date.getFullYear(),
        "Y": date.getFullYear(), "y": String(date.getFullYear()).slice(-2), "a": date.getHours() < 12 ? "am" : "pm", "A": date.getHours() < 12 ? "AM" : "PM",
        "B": Math.floor((((date.getUTCHours() + 1) % 24) + date.getUTCMinutes() / 60 + date.getUTCSeconds() / 3600) * 1000 / 24),
        "g": hours12, "G": date.getHours(), "h": pad(hours12), "H": pad(date.getHours()), "i": pad(date.getMinutes()), "s": pad(date.getSeconds()), "v": pad(date.getMilliseconds(), 3),
        "e": Intl.DateTimeFormat().resolvedOptions().timeZone, "I": (new Date(date.getFullYear(), 6, 1).getTimezoneOffset() !== date.getTimezoneOffset()) ? 1 : 0,
        "O": `${timezoneSign}${pad(timezoneOffsetHours)}${pad(timezoneOffsetMinutes)}`, "P": `${timezoneSign}${pad(timezoneOffsetHours)}:${pad(timezoneOffsetMinutes)}`,
        "T": date.toLocaleTimeString('en-us', { timeZoneName: 'short' }).split(' ')[2], "Z": timezoneOffset * 60, "c": date.toISOString(), "r": date.toUTCString(),
        "U": Math.floor(date.getTime() / 1000)
    };

    return format.replace(/d|D|j|l|N|S|w|z|W|F|m|M|n|t|L|o|Y|y|a|A|B|g|G|h|H|i|s|v|e|I|O|P|T|Z|c|r|U/g, match => map[match]);
}


function applyDatePickers(container = document) {
    $(container).find(".datePicker, .dateTimePicker, .timePicker, .dateRangePicker, .dateRangePickerWithTime").not(".datepicker-applied").each(function () {
        jQuery(this).addClass("datepicker-applied"); // Move this to prevent duplicate bindings

        if ($(this).hasClass("datePicker")) {
            $(this).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                locale: { format: 'DD/MM/YYYY' },
                parentEl: "body",
                autoUpdateInput: false // Prevent automatic input update
            }, function (chosen_date) {
                $(this.element).val(chosen_date.format('DD/MM/YYYY'));
            });
        }

        if ($(this).hasClass("dateTimePicker")) {
            let defaultDate = moment().set({ hour: 10, minute: 0, second: 0, millisecond: 0 }); // today at 10 AM
            $(this).daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: false,
                timePickerIncrement: 1,
                timePickerSeconds: false,
                startDate: defaultDate,
                autoApply: true,
                locale: { format: 'DD/MM/YYYY hh:mm A' },
                parentEl: "body",
                autoUpdateInput: false // Prevent automatic input update
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY hh:mm A'));
            });
        }

        if ($(this).hasClass("timePicker")) {
            let defaultDate = moment().set({ hour: 10, minute: 0, second: 0, millisecond: 0 }); // today at 10 AM
            $(this).daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: false,
                timePickerIncrement: 1,
                timePickerSeconds: false,
                autoApply: true,
                startDate: defaultDate,
                locale: { format: 'hh:mm A' },
                parentEl: "body",
                autoUpdateInput: false // Prevent automatic input update
            }).on('show.daterangepicker', function () {
                $(this).data('daterangepicker').container.find('.calendar-table').hide();
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('hh:mm A'));
            });
        }

        if ($(this).hasClass("dateRangePicker")) {
            $(this).daterangepicker({
                autoApply: true,
                locale: { format: 'DD/MM/YYYY' },
                parentEl: "body",
                autoUpdateInput: false, // Prevent automatic input update
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });
        }

        if ($(this).hasClass("dateRangePickerWithTime")) {
            let defaultDate = moment().set({ hour: 10, minute: 0, second: 0, millisecond: 0 }); // today at 10 AM
            $(this).daterangepicker({
                timePicker: true,
                timePicker24Hour: false,
                timePickerIncrement: 1,
                timePickerSeconds: false,
                autoApply: true,
                startDate: defaultDate,
                locale: { format: 'DD/MM/YYYY hh:mm A' },
                parentEl: "body",
                autoUpdateInput: false, // Prevent automatic input update
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY hh:mm A') + ' - ' + picker.endDate.format('DD/MM/YYYY hh:mm A'));
            });
        }
    });

    // This code updates internal date pickers when the input value changes by autocrud form in edit mode.
    $(document).on("change", ".datePicker, .dateTimePicker, .timePicker, .dateRangePicker, .dateRangePickerWithTime", function () {
        let picker = $(this).data("daterangepicker");

        if (picker) {
            let newVal = $(this).val().trim();

            if (newVal) {
                // Parse input value and update the picker accordingly
                let format = picker.locale.format;
                let date = moment(newVal, format);

                if (date.isValid()) {
                    picker.setStartDate(date);
                    picker.setEndDate(date);
                }
            }
        }
    });

}


function applyColorPicker() {
    jQuery(".colorPicker").not(".colorPicker-applied").each(function () {
        jQuery(this).spectrum({
            showInput: true,
            hideAfterPaletteSelect: true,
        });
        // Mark as applied
        jQuery(this).addClass("colorPicker-applied");
    });
}

function applyInternationalNumber() {
    $("input.internationalNumber").each(function () {
        var $input = $(this);
        if (!$input.hasClass("iti-initialized")) {
            var countryCode = "IN";

            var iti = window.intlTelInput(this, {
                initialCountry: countryCode,
                nationalMode: false,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.4.0/build/js/utils.js"
            });

            // Skip tabbing into the country dropdown
            var $countryContainer = $input.closest(".iti").find(".iti__selected-country");
            $countryContainer.attr("tabindex", "-1");
            $countryContainer.addClass("e2t_ignore");

            $input.data("itiInstance", iti).addClass("iti-initialized");

            $input.on("blur", function () {
                var fullNumber = iti.getNumber();

                if (!iti.isValidNumber()) {
                    //if element has required attribute
                    if ($input.attr("required") || $input.prop("required")) {
                        $input.addClass("is-invalid").removeClass("is-valid");
                    }
                    else if (fullNumber != "") {
                        $input.addClass("is-invalid").removeClass("is-valid");
                    }
                    else {
                        $input.removeClass("is-invalid").removeClass("is-valid");
                    }

                    $input.val("");
                } else {
                    $input.addClass("is-valid").removeClass("is-invalid");
                    $input.val(fullNumber);
                }
            });
        }
    });
}

// Function to apply location picker to all inputs with class 'locationPicker'
function applyLocationPicker() {
    document.querySelectorAll(".locationPicker").forEach(input => {
        if (!input.dataset.pickerApplied) {
            new LocationPicker(input);
            input.dataset.pickerApplied = "true";
        }
    });
}

function updateDependentFields() {
    $("[data-dependent-on]").each(function () {
        let $container = $(this);
        let parentName = $container.data("dependent-on");
        let dependentValue = $container.data("dependent-value");

        let expectedValue = typeof dependentValue === "string" ? dependentValue.split(",") : [];
        let $parentElement = $(`[name="${parentName}"]`);

        if ($parentElement.length === 0) return; // Skip if parent not found

        let parentValue;

        if ($parentElement.is(":checkbox")) {
            // parentValue = $parentElement.is(":checked")
            //     ? $parentElement.val()  // Use checkbox value if checked
            //     : "";
            $parentElement.each(function () {
                if ($(this).is(":checked") && expectedValue.includes($(this).val())) {
                    parentValue = $(this).val();
                }
            });
        } else if ($parentElement.is(":radio")) {
            parentValue = $(`[name="${parentName}"]:checked`).val() || "";
        } else {
            parentValue = $parentElement.val();
        }

        // $container.toggle(parentValue == expectedValue);
        $container.toggle(expectedValue.includes(parentValue))

    });
}

function exportHtmlTableAsExcel(buttonElement) {
    const target = buttonElement.getAttribute("data-target-table");
    if (!target) return;

    const table = document.querySelector(target);
    if (!table) return;

    const clonedTable = table.cloneNode(true);
    const fileName = buttonElement.getAttribute("data-filename") || "export.xls";

    const html = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns="http://www.w3.org/TR/REC-html40">
      <head>
        <!--[if gte mso 9]>
        <xml>
          <x:ExcelWorkbook>
            <x:ExcelWorksheets>
              <x:ExcelWorksheet>
                <x:Name>Sheet1</x:Name>
                <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
              </x:ExcelWorksheet>
            </x:ExcelWorksheets>
          </x:ExcelWorkbook>
        </xml>
        <![endif]-->
        <meta charset="UTF-8">
      </head>
      <body>
        ${clonedTable.outerHTML}
      </body>
    </html>
    `;

    const blob = new Blob([html], {
        type: "application/vnd.ms-excel"
    });

    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}


function applyHtmlEditor() {
    // console.log("applyHtmlEditor() called");
    setTimeout(() => {
        $('textarea.htmlEditor').each(function (index) {
            const $this = $(this);

            // console.log(`Checking element #${index}`, $this[0]);

            if ($this.hasClass('htmlEditor-applied')) {
                // console.log(`Editor already applied on element #${index}`);
                return;
            }

            $this.addClass('htmlEditor-applied');

            // console.log(`Applying Trumbowyg on element #${index}`);

            $this.trumbowyg({
                btns: [
                    ['viewHTML'],
                    ['undo', 'redo'],
                    ['formatting', 'fontsize'],
                    ['strong', 'em', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    // ['insertImage'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    // ['horizontalRule'],
                    // ['removeformat'],
                    ['fullscreen']
                ],
                autogrow: false
            });

            // console.log(`Editor applied successfully on element #${index}`);
        });
    }, 0);
}



function applyUiLibrary() {
    applySelect2All();
    applyDatePickers();
    applyColorPicker();
    applyInternationalNumber();
    applyLocationPicker();
    updateDependentFields();
    applyHtmlEditor();
    setTimeout(() => {
        jQuery(".inputFocus").first().focus();
    }, 200);
}

/*************************************
    tooltip code starts
************************************/
document.addEventListener('DOMContentLoaded', function () {
    function addTippyToElementsWithTitles(rootElement) {
        // Find all elements with title attribute within the given rootElement
        const elementsWithTitle = rootElement.querySelectorAll('[title]:not([data-tippy-content])');

        // Initialize Tippy for each of these elements
        tippy(elementsWithTitle, {
            content(reference) {
                // Use the title attribute as the content and then remove it to avoid native tooltips
                const title = reference.getAttribute('title');
                reference.removeAttribute('title');
                return title;
            },
            // animation: 'shift-away',
            allowHTML: true,
            animation: 'fade',
            delay: [100, 0], // [show delay, hide delay]
            theme: 'material',  // Optional: Change theme
        });
    }

    // Apply Tippy to existing elements
    addTippyToElementsWithTitles(document.body);

    // Use MutationObserver to monitor for changes in the DOM
    const observer = new MutationObserver(function (mutationsList) {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                for (const addedNode of mutation.addedNodes) {
                    if (addedNode.nodeType === Node.ELEMENT_NODE) {
                        addTippyToElementsWithTitles(addedNode);
                    }
                }
            }
        }
    });

    // Start the observer with the entire document as the target and looking for child additions
    observer.observe(document.body, { childList: true, subtree: true });
});
//tooltip code ends


/*******************************************************
    Download PDF Functionality From API Response
********************************************************/
function downloadPdf(base64Data, filename = "download.pdf") {
    // Convert base64 to a Blob
    let byteCharacters = atob(base64Data);
    let byteNumbers = new Array(byteCharacters.length);
    for (let i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    let byteArray = new Uint8Array(byteNumbers);
    let blob = new Blob([byteArray], { type: "application/pdf" });

    // Create a download link
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;

    // Append link to body, trigger download, and remove the link
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}



/****************************************************
 * Popup Modal Functionality
 * **************************************************/
function openDynamicModal({
    title,
    htmlContent,
    size = "lg", // Default size is md
    callbacks = {}, // Object for Bootstrap modal event callbacks
    modalOptions = {} // Additional options for Bootstrap modal
}) {
    // Ensure size is only md, lg, or xl
    const allowedSizes = ["sm", "md", "lg", "xl", "xxl", "fullscreen"];
    size = allowedSizes.includes(size) ? `modal-${size}` : "modal-xxl";

    let modalId = "modal-" + Date.now();
    let modalHtml = `
        <div class="modal fade" id="${modalId}" aria-hidden="true">
            <div class="modal-dialog ${size}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">Loading...</div>
                    <div class="modal-footer d-none">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary d-none apiDynamicModalSubmit" id="${modalId}-submit-btn">Submit</button>
                    </div>
                </div>
            </div>
        </div>`;

    // Append modal to the body
    document.body.insertAdjacentHTML("beforeend", modalHtml);
    let modalElement = document.getElementById(modalId);
    let modalBody = modalElement.querySelector(".modal-body");
    let modalFooter = modalElement.querySelector(".modal-footer");
    let submitButton = modalElement.querySelector(`#${modalId}-submit-btn`);
    let bootstrapModal = new bootstrap.Modal(modalElement, modalOptions);

    modalBody.innerHTML = htmlContent;

    let $modalForm = modalBody.querySelector('.autoCrudForm');
    let $dataTable = modalBody.querySelector('.manageDataTable');
    let form = modalBody.querySelector("form");
    if (form || $modalForm) {
        modalFooter.classList.remove("d-none"); // Show modal footer
        submitButton.classList.remove("d-none"); // Show submit button
    }


    if (typeof callbacks.onShow === "function") callbacks.onShow(modalElement);

    // Bind Bootstrap modal event callbacks if provided
    const modalEvents = ["show.bs.modal", "shown.bs.modal", "hide.bs.modal", "hidden.bs.modal"];
    modalEvents.forEach(event => {
        if (typeof callbacks[event] === "function") {
            modalElement.addEventListener(event, () => callbacks[event](modalElement));
        }
    });

    // Handle close event and destroy modal
    modalElement.addEventListener("hidden.bs.modal", function () {
        if (typeof callbacks.onClose === "function") callbacks.onClose(modalElement);
        modalElement.remove(); // Destroy modal after closing
    }, { once: true });

    // Handle close event and destroy modal
    modalElement.addEventListener("show.bs.modal", function () {
        // alert('test');
        if (form || $modalForm) {
            applyUiLibrary();
            if ($modalForm) {
                prepareFormView();
            }
        }

        if ($dataTable) {
            applyDatatable();
        }
    });

    // Show modal
    bootstrapModal.show();
    return bootstrapModal; // Return instance if needed
}

function camelToHuman(text) {
    return text
        .replace(/([A-Z])/g, ' $1') // Add space before capital letters
        .replace(/^./, str => str.toUpperCase()) // Capitalize the first letter
        .trim();
}


function focusNextFormInput(form, target) {
    return false;
    if (form.length) {
        // console.log("Form found");
        // Find all focusable inputs in the form
        var inputs = form.find(':input:not(:disabled):not([readonly]):visible:not(.e2t_ignore):not(.select2-selection__clear)');

        // Move to the next input
        var nextInput = inputs.eq(inputs.index(target) + 1);
        // console.log("Next Input", nextInput);
        if (nextInput.length) {
            // console.log("testing", nextInput);
            setTimeout(() => {
                nextInput.focus();
            }, 100);
        }
    }
    else {
        // console.log("Form not found");
    }
}


$(document).on('click', '.pwaShareBtn', function () {
    const title = $(this).data('title') || document.title;
    const text = $(this).data('text') || '';
    const url = $(this).data('url') || window.location.href;

    if (navigator.share) {
        navigator.share({ title, text, url })
            .catch(err => console.error('Share failed:', err));
    } else {
        alert('Sharing not supported. Copy this link manually:\n' + url);
    }
});

$(document).on('click', '.pwaShareFileBtn', async function () {
    const fileUrl = $(this).data('url');
    const fileName = $(this).data('filename') || 'file.txt';
    const fileType = $(this).data('type') || 'application/octet-stream';

    if (!navigator.canShare || !navigator.canShare({ files: [new File([], '')] })) {
        alert('File sharing not supported on this device/browser.');
        return;
    }

    try {
        const response = await fetch(fileUrl);
        const blob = await response.blob();
        const file = new File([blob], fileName, { type: fileType });

        await navigator.share({
            title: fileName,
            text: 'Sharing file: ' + fileName,
            files: [file],
        });
    } catch (err) {
        console.error('Sharing failed:', err);
        alert('Failed to share file.');
    }
});


$('#captureImageInput').on('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            $('#previewImage').attr('src', event.target.result);
        };
        reader.readAsDataURL(file);
    }
});


$('#captureMediaInput').on('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const url = URL.createObjectURL(file);
        if (file.type.startsWith('video/')) {
            $('#previewVideo').attr('src', url).show();
            $('#previewAudio').hide();
        } else if (file.type.startsWith('audio/')) {
            $('#previewAudio').attr('src', url).show();
            $('#previewVideo').hide();
        }
    }
});


$('#getLocationBtn').on('click', function () {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude.toFixed(6);
            const lon = position.coords.longitude.toFixed(6);
            $('#locationOutput').text(`Latitude: ${lat}, Longitude: ${lon}`);
        }, function (err) {
            alert('Location access denied or failed.');
        });
    } else {
        alert('Geolocation not supported.');
    }
});


function isIos() {
    return /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
}

const isStandalone = ('standalone' in navigator) && navigator.standalone;


/* ==========================================================================
   UNIVERSAL PWA + PUSH LOGIC FOR ALL PLATFORMS
   Respecting: 
   - appSettings.pwaEnabled
   - appSettings.webPushNotification
========================================================================== */

let deferredPromptGlobal = null;
let swRegistrationGlobal = null;

const isPwaEnabled = parseInt(appSettings.pwaEnabled) === 1;
const isPushEnabled = parseInt(appSettings.webPushNotification) === 1;

/* ==========================================================================
   UTILITIES
========================================================================== */

// Detect if app is already running in standalone mode (PWA)
function isInStandaloneMode() {
    return (
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true
    );
}

// Check if install prompt can be shown (after dismiss timeout)
function canShowInstallPrompt() {
    if (isInStandaloneMode()) return false;
    const dismissedAt = localStorage.getItem('pwaInstallPromptDismissedAt');
    if (dismissedAt) {
        return Date.now() - parseInt(dismissedAt, 10) > 7 * 24 * 60 * 60 * 1000;
    }
    return true;
}

// Check if push prompt can be shown (after dismiss timeout)
function canShowPushPrompt() {
    if (Notification.permission === 'granted') return false;
    const dismissedAt = localStorage.getItem('pwaPushPromptDismissedAt');
    if (dismissedAt) {
        return Date.now() - parseInt(dismissedAt, 10) > 7 * 24 * 60 * 60 * 1000;
    }
    return true;
}

/* ==========================================================================
   UI HANDLERS
========================================================================== */

function showInstallPrompt() {
    const prompt = document.getElementById('installPrompt');
    prompt.style.display = 'block';
    setTimeout(() => prompt.classList.add('show'), 10);
}

function hideInstallPrompt() {
    const prompt = document.getElementById('installPrompt');
    prompt.classList.remove('show');
    setTimeout(() => (prompt.style.display = 'none'), 300);
    localStorage.setItem('pwaInstallPromptDismissedAt', Date.now().toString());
}

function showPushPrompt() {
    const prompt = document.getElementById('pushPrompt');
    prompt.style.display = 'block';
    setTimeout(() => prompt.classList.add('show'), 10);
}

function hidePushPrompt() {
    const prompt = document.getElementById('pushPrompt');
    prompt.classList.remove('show');
    setTimeout(() => (prompt.style.display = 'none'), 300);
    localStorage.setItem('pwaPushPromptDismissedAt', Date.now().toString());
}

/* ==========================================================================
   SERVICE WORKER & PUSH
========================================================================== */

// Register the service worker (always required for PWA support)
async function registerServiceWorker() {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        const reg = await navigator.serviceWorker.register('/serviceWorker.js');
        swRegistrationGlobal = reg;
        return reg;
    }
    console.error('ServiceWorker or PushManager not supported');
    return null;
}

// Subscribe user to push notifications (only when enabled)
async function subscribeUser(swRegistration) {
    if (!isPushEnabled) return;

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') return;

    const subscription = await swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(webPushPublicKey),
    });

    await fetch(base_url + 'home/saveUserSubscription', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(subscription),
    });
}

// Convert public key
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
}

/* ==========================================================================
   ON DOCUMENT READY
========================================================================== */

jQuery(document).ready(async function () {
    if (window.isSecureContext && window.location.protocol === "https:") {
        const swRegistration = await registerServiceWorker();

        /* ----------------------------------------
           1. INSTALL PROMPT (when PWA is enabled)
        ---------------------------------------- */
        if (isPwaEnabled) {
            window.addEventListener('beforeinstallprompt', (e) => {
                // e.preventDefault(); // Prevent Chrome's default mini install banner
                deferredPromptGlobal = e;
                if (!isIos() && canShowInstallPrompt()) {
                    showInstallPrompt();
                }
            });

            if (isIos() && canShowInstallPrompt()) {
                showInstallPrompt();
                $('#installPrompt .install-ios').show();
                $('#installPrompt .install-default').hide();
            } else {
                $('#installPrompt .install-default').show();
                $('#installPrompt .install-ios').hide();
            }

            $('#dismissButton').on('click', hideInstallPrompt);

            $('#installButton').on('click', () => {
                if (deferredPromptGlobal) {
                    deferredPromptGlobal.prompt();
                    deferredPromptGlobal.userChoice.then(choice => {
                        if (choice.outcome === 'accepted') hideInstallPrompt();
                        deferredPromptGlobal = null;
                    });
                }
            });
        }

        /* ----------------------------------------
           2. PUSH HANDLING (when Push is enabled)
        ---------------------------------------- */
        if (isPushEnabled) {
            $('#dismissPushPromptButton').on('click', hidePushPrompt);

            $(document).on('click', '#enablePushButton', async () => {
                if (swRegistrationGlobal) await subscribeUser(swRegistrationGlobal);
                hidePushPrompt();
            });

            if (isIos()) {
                if (isInStandaloneMode() && canShowPushPrompt()) {
                    showPushPrompt();
                }
            } else {
                if (swRegistration) await subscribeUser(swRegistration);
            }
        }
    }


});


/*--------------------------------------------
    Mobile Bottom Navigation Menu Popup Handling Code
--------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {
    const submenuItems = document.querySelectorAll(".nav-item-with-submenu");
    const dropupContainer = document.querySelector(".pwa-global-dropup");

    submenuItems.forEach((wrapper) => {
        const trigger = wrapper.querySelector(".submenu-trigger");
        const submenu = wrapper.querySelector(".submenu");

        if (trigger && submenu) {
            // Move submenu to global dropup
            const submenuClone = submenu.cloneNode(true);
            submenu.remove();

            trigger.addEventListener("click", (e) => {
                e.stopPropagation();

                // Reset dropup styles
                dropupContainer.style.left = '';
                dropupContainer.style.right = '';
                dropupContainer.style.transform = '';

                // Set dropup content
                dropupContainer.innerHTML = submenuClone.innerHTML;

                // Position near clicked trigger
                const rect = trigger.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;

                dropupContainer.style.left = `${centerX}px`;
                dropupContainer.style.transform = `translateX(-50%)`;
                dropupContainer.classList.remove("d-none");

                // Adjustment if overflow on left or right
                const dropupRect = dropupContainer.getBoundingClientRect();
                if (dropupRect.left < 0) {
                    dropupContainer.style.left = `0px`;
                    dropupContainer.style.transform = `none`;
                } else if (dropupRect.right > window.innerWidth) {
                    dropupContainer.style.right = `0px`;
                    dropupContainer.style.left = `auto`;
                    dropupContainer.style.transform = `none`;
                }
            });

        }
    });

    // Close dropup on outside click
    document.addEventListener("click", (e) => {
        if (dropupContainer && !e.target.closest(".submenu-trigger") && !dropupContainer.contains(e.target)) {
            dropupContainer.classList.add("d-none");
        }
    });
});

function roundUp(value, decimalPlaces = 2) {
    if (isNaN(value)) return 0;
    const factor = 10 ** decimalPlaces;
    return (Math.ceil((value * factor).toFixed(10)) / factor);
}

function roundDown(value, decimalPlaces = 2) {
    if (isNaN(value)) return 0;
    const factor = 10 ** decimalPlaces;
    return (Math.floor((value * factor).toFixed(10)) / factor);
}


function roundToTwo(num) {
    return +(Math.round(num + "e+2") + "e-2");
}

$(document).on('keydown', 'input, select, textarea', function (e) {
    if (!e.ctrlKey || !['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight'].includes(e.key)) return;

    e.preventDefault();

    let $current = $(this);
    let $cell = $current.closest('td, th');
    let $row = $current.closest('tr');
    let colIndex = $cell.index();
    let rowIndex = $row.index();

    let $targetCell;
    if (e.key === 'ArrowDown') {
        let $targetRow = $row.next('tr');
        if ($targetRow.length) {
            $targetCell = $targetRow.find('td, th').eq(colIndex);
        }
    } else if (e.key === 'ArrowUp') {
        let $targetRow = $row.prev('tr');
        if ($targetRow.length) {
            $targetCell = $targetRow.find('td, th').eq(colIndex);
        }
    } else if (e.key === 'ArrowRight') {
        $targetCell = $row.find('td, th').eq(colIndex + 1);
    } else if (e.key === 'ArrowLeft') {
        $targetCell = $row.find('td, th').eq(colIndex - 1);
    }

    if ($targetCell && $targetCell.length) {
        let $targetInput = $targetCell.find('input, select, textarea').filter(':visible').first();
        if ($targetInput.length) {
            $targetInput.focus();
        }
    }
});


//new search code
jQuery(document).ready(function () {
    var debounceTimer;

    jQuery("#search_bar").on("keyup", function (e) {
        console.log("Key pressed:", e.which);
        var search_term = jQuery(this).val();
        clearTimeout(debounceTimer);  // Clear the timer on each key press

        // Check if Enter key is pressed
        if (e.which === 13) {
            performSearch(search_term);
        } else {
            debounceTimer = setTimeout(function () {
                performSearch(search_term);
            }, 500);  // Adjust the delay as needed
        }
    });

    function performSearch(search_term) {
        if (search_term.length >= 3) {
            let params = {
                data: { search: search_term },
            }

            apiCall("POST", "api/GlobalSearch", params)
                .then(response => {
                    console.log("test");
                    jQuery(".search_result").html(response.data);
                    jQuery('.search-dropdown-toggle').dropdown('show');
                    jQuery("#search_bar").focus();
                })
                .catch(error => {

                });
        };
    }
});



function updateTippyOrTitle($el, newContent) {
    if ($el[0]._tippy) {
        $el[0]._tippy.setContent(newContent);
    } else {
        $el.attr('title', newContent);
    }
}