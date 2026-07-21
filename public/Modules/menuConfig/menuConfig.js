window.tenantId = 0;
let menuCounter = 1;
let menuLocation = "sidebarMain"

window.onApiReady(function () {
    getReady();
});

jQuery(document).ready(function () {
    jQuery("#tenantIdSelector").on("change", function () {
        menuCounter = 1;
        tenantId = jQuery(this).val();
        if (!tenantId)
            tenantId = 0;
        getReady();
    });

    jQuery("#menuLocationSelector").on("change", function () {
        menuCounter = 1;
        menuLocation = jQuery(this).val();
        getReady();

    });

    jQuery(document).on("change", ".permission-dropdown", function () {
        let $this = jQuery(this);
        if ($this.val().length == 0) {
            $this.closest(".list-group-item").addClass("bg-warning");
        } else {
            $this.closest(".list-group-item").removeClass("bg-warning");
        }
    });
});

jQuery(document).ready(function () {

    let prevX = 0; // Store previous X position
    let routes = [];
    let permissions = {};

    // Initialize Sortable.js with horizontal nesting support
    let sortable = new Sortable(document.getElementById('menuList'), {
        group: 'nested',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onStart: function (evt) {
            prevX = evt.originalEvent.clientX; // Store initial X position
        },
        onEnd: function (evt) {
            let currentX = evt.originalEvent.clientX;
            let draggedItem = evt.item;
            let relatedItem = evt.item.previousElementSibling; // Item above the dragged item
            console.log("Current X: " + currentX + ", Previous X: " + prevX);
            if (currentX > prevX + 30) {
                // Dragging Right → Make it a child of the previous item
                if (relatedItem) {
                    let subMenu = relatedItem.querySelector(".sub-menu");
                    if (!subMenu) {
                        subMenu = document.createElement("ul");
                        subMenu.className = "nested-sortable sub-menu";
                        relatedItem.appendChild(subMenu);
                    }
                    subMenu.appendChild(draggedItem);
                }
            } else if (currentX < prevX - 30) {
                // Dragging Left → Move out of parent
                let parentItem = draggedItem.parentElement.closest("li");
                if (parentItem) {
                    parentItem.after(draggedItem); // Move to the same level
                }
            }
        }
    });

    // Add new menu item
    $("#addMenuItem").click(function () {
        menuId = menuCounter++;
        let itemId = "menuItem_" + menuId;
        let menuItem = `
            <li class="list-group-item px-3 py-2" data-id="${itemId}">
                <div class="menu-actions">
                    <span class="menu-label">Menu Item ${menuId}</span>
                    <button class="btn btn-sm btn-danger px-1 py-0 mx-1 float-end deleteMenuItem"><i class='fa fa-trash'></i></button>
                    <button class="btn btn-sm btn-warning px-1 py-0 float-end toggleMenuDetails"><i class='fa fa-pencil-alt'></i></button>
                </div>
                <div class="menu-content mt-2">
                    <div class="mb-2">
                        <label>Label</label>
                        <input type="text" class="form-control menu-label-input" value="Menu Item ${menuId}">
                    </div>
                    <div class="mb-2">
                        <label>ICON</label>
                        <div class="input-group">
                            <div class="input-group-text iconPreview"></div>
                            <input type="text" class="form-control menu-icon-input iconPicker" placeholder="Select Icon">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label>Class</label>
                        <input type="text" class="form-control menu-class-input" placeholder="list of classes">
                    </div>
                    <div class="mb-2">
                        <label>Attributes</label>
                        <input type="text" class="form-control menu-attributes-input" placeholder="list of attributes">
                    </div>
                    <div class="mb-2">
                        <label for="moduleDropdown">Module</label>
                        <select class="form-control module-dropdown menu-module-input select2">
                            <option value="">Select Module</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="permissionDropdown">Permissions</label>
                        <select class="form-control permission-dropdown menu-permissions-input select2" multiple>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>URL</label>
                        <select type="text" class="form-control menu-url-input routeSelector select2" data-selecttype="tags" placeholder="Enter URL">
                        </select>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" class="form-check-input menu-isPopup-checkbox" id="isPopup_${itemId}" name="isPopup">
                        <label class="form-check-label" for="isPopup_${itemId}">Open in Popup</label>
                    </div>

                </div>
                <ul class="nested-sortable sub-menu mt-2"></ul>
            </li>`;
        $("#menuList").append(menuItem);
        fixUi();
    });

    // Toggle menu details (Accordion Style)
    $(document).on("click", ".toggleMenuDetails", function () {
        $(this).closest("li").children(".menu-content").slideToggle();
    });

    // Update menu label on input change
    $(document).on("input", ".menu-label-input", function () {
        // Find the closest list-group-item (menu item) and update only its direct .menu-label
        $(this).closest(".list-group-item").find("> .menu-actions .menu-label").text($(this).val());
    });


    // Delete menu item
    $(document).on("click", ".deleteMenuItem", function () {

        // confirm before delete
        if (!confirm("Are you sure you want to delete this menu item?")) {
            return;
        }

        $(this).closest("li").remove();
    });

    // Save menu structure as JSON
    $("#saveMenu").click(function () {
        let menuData = getMenuStructure($("#menuList"));

        console.log("saving", menuData);
        //save menuData to database
        apiCall('POST', 'api/MenuConfig/save/' + tenantId + '/' + menuLocation, menuData).then(function (response) {
            //do nothing
        }).catch(function (error) {
            console.error('Error:', error);
            alert('An error occurred while saving the menu.');
        });
    });


    // Event listener for the button
    $("#autoAddRoutes").click(function () {
        addMissingRoutes();
        fixUi();
    });

    jQuery("#jsonExportMenu").on("click", function () {
        let menuData = getMenuStructure($("#menuList"));
        let json = JSON.stringify(menuData, null, 2);
        let blob = new Blob([json], { type: "application/json" });
        let url = URL.createObjectURL(blob);
        let a = document.createElement('a');
        let tenantId = jQuery("#tenantIdSelector").val() || 0;
        a.href = url;
        a.download = tenantId + '_' + menuLocation + '_MenuConfig.json';
        a.click();
    });

    jQuery(".restoreDefaultMenu").on("click", function () {

        // confirm
        if (!confirm("Are you sure you want to restore default menu?")) {
            return;
        }


        let tenantId = jQuery("#tenantIdSelector").val() || 0;
        apiCall('GET', 'api/MenuConfig/restoreDefault/' + tenantId + '/' + menuLocation).then(function (response) {
            if (response.message != "") {
                mtplAlerts.show('success', response.message, 'Success');
            }
            if (response.status) {
                $("#menuList").empty();
                renderMenu(response.menu, $("#menuList"));
                fixUi();
            }
        }).catch(function (error) {
            console.error('Error:', error);
            // alert('An error occurred while saving the menu.');
        });
    });

});

// Function to get menu structure as JSON
function getMenuStructure(ul) {
    let items = [];
    ul.children("li").each(function () {
        let li = $(this);
        let subMenu = li.children(".sub-menu").first(); // Get only direct children

        let item = {
            id: li.attr('data-id'),
            label: escapeDoubleQuotes(li.find('.menu-label-input').val()),
            icon: escapeDoubleQuotes(li.find('.menu-icon-input').val()),
            class: escapeDoubleQuotes(li.find('.menu-class-input').val()),
            module: li.find('.menu-module-input').val(),
            permissions: li.find('.menu-permissions-input').val().join(','),
            attributes: escapeDoubleQuotes(li.find('.menu-attributes-input').val()),
            isPopup: li.find('.menu-isPopup-checkbox').is(':checked') ? 1 : 0,
            url: escapeDoubleQuotes(li.find('.menu-url-input').val()),
            children: getMenuStructure(subMenu) // Recursively pick direct children only
        };

        items.push(item);
    });
    return items;
}


// Enable sorting for sub-menus
function updateSortable() {
    $(".nested-sortable").each(function () {
        new Sortable(this, {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65
        });
    });
}

// Recursively render the menu
function renderMenu(menuData, parentElement) {
    menuData.forEach(item => {

        item.label = escapeDoubleQuotes(item.label);
        item.icon = escapeDoubleQuotes(item.icon);
        item.class = escapeDoubleQuotes(item.class);
        item.attributes = escapeDoubleQuotes(item.attributes);
        item.url = escapeDoubleQuotes(item.url);
        item.isPopup = parseInt(item.isPopup) ? 1 : 0;

        menuCounter++;

        let listItem = `
                <li class="list-group-item px-3 py-2" data-id="menuItem_${item.id}">
                    <div class="menu-actions">
                        <span class="menu-label">${item.label}</span>
                        <button class="btn btn-sm btn-danger px-1 py-0 mx-1 float-end deleteMenuItem"><i class='fa fa-trash'></i></button>
                        <button class="btn btn-sm btn-warning px-1 py-0 float-end toggleMenuDetails"><i class='fa fa-pencil-alt'></i></button>
                    </div>
                    <div class="menu-content mt-2">
                        <div class="mb-2">
                            <label>Label</label>
                            <input type="text" class="form-control menu-label-input" value="${item.label}">
                        </div>
                        <div class="mb-2">
                            <label>ICON</label>
                            <div class="input-group">
                                <div class="input-group-text iconPreview"><i class="${item.icon}"></i></div>
                                <input type="text" class="form-control menu-icon-input iconPicker" placeholder="Select Icon" value="${item.icon}">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Class</label>
                            <input type="text" class="form-control menu-class-input" placeholder="list of classes" value="${item.class}">
                        </div>
                        <div class="mb-2">
                            <label>Attributes</label>
                            <input type="text" class="form-control menu-attributes-input" placeholder="list of attributes" value="${item.attributes}">
                        </div>
                        <div class="mb-2">
                            <label for="moduleDropdown">Module</label>
                            <select class="form-control module-dropdown menu-module-input select2" data-setvalue="${item.module}">
                                <option value="">Select Module</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="permissionDropdown">Permissions</label>
                            <select class="form-control permission-dropdown menu-permissions-input select2" multiple data-setvalue="${item.permissions}">
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>URL</label>
                            <select type="text" class="form-control menu-url-input routeSelector select2" data-selecttype="tags" placeholder="Enter URL" data-setvalue="${item.url}">
                            </select>
                        </div>
                        <div class="mb-2 form-check">
                            <input type="checkbox" class="form-check-input menu-isPopup-checkbox" id="isPopup_${menuCounter}" name="isPopup" ${item.isPopup ? 'checked' : ''} data-setvalue="${item.isPopup}">
                            <label class="form-check-label" for="isPopup_${menuCounter}">Open in Popup</label>
                        </div>
                    </div>
                    <ul class="nested-sortable sub-menu mt-2"></ul>
                </li>
            `;

        let listItemElement = $(listItem);
        parentElement.append(listItemElement);

        // If the menu has children, render them recursively
        if (item.children && item.children.length > 0) {
            renderMenu(item.children, listItemElement.find(".sub-menu"));
        }
    });
}

function escapeDoubleQuotes(text) {
    if (typeof text !== 'string') {
        return text;
    }
    return text.replace(/"/g, '&quot;');
}

function populateRouteDropdown() {

    // Populate route dropdown on .routeSelector
    let routeSelector = $(".routeSelector");
    routeSelector.empty();
    routeSelector.append('<option value="">Select Route</option>');
    routes.forEach(route => {
        routeSelector.append(`<option value="${route}">${route}</option>`);
    });

    // set value from elements data attribute
    routeSelector.each(function () {
        let value = $(this).attr('data-setvalue');
        if (value && !$(this).find(`option[value="${value}"]`).length) {
            $(this).append(`<option value="${value}">${value}</option>`);
        }
        $(this).val(value);
    });

    applyUiLibrary();
}

function menuTextToIcon(menuText) {
    let icon = 'fas fa-circle';

    // if menu contains a specific text, assign a specific icon
    if (menuText.includes('dashboard')) {
        icon = 'fas fa-tachometer-alt';
    }

    // if menu starts with a specific text, assign a specific icon
    if (menuText.startsWith('add')) {
        icon = 'fas fa-plus';
    }

    if (menuText.startsWith('manage')) {
        icon = 'fas fa-list';
    }


    return icon;
}

// Function to generate a structured menu based on routes
function generateMenuTree(routes) {
    let menuTree = {}; // Stores all menu nodes
    let rootNodes = new Set(); // Stores top-level parent routes

    routes.forEach(route => {
        let parts = route.split('/');
        let key = route;
        let parentKey = parts.length > 1 ? parts.slice(0, -1).join('/') : null;

        let myLabel = camelToHuman(parts[parts.length - 1]);
        let myIcon = menuTextToIcon(parts[parts.length - 1]);

        // Ensure the current node exists
        if (!menuTree[key]) {
            menuTree[key] = { id: key, label: myLabel, icon: myIcon, url: key, children: [] };
        }

        // Automatically create parent nodes if they are missing
        for (let i = 1; i < parts.length; i++) {
            let parentPath = parts.slice(0, i).join('/');
            let parentLabel = camelToHuman(parts[i - 1]);
            let parentIcon = menuTextToIcon(parts[i - 1]);

            if (!menuTree[parentPath]) {
                menuTree[parentPath] = { id: parentPath, label: parentLabel, icon: parentIcon, url: parentPath, children: [] };

                // If this is the first part of the path (a missing top-level parent), add it to rootNodes
                if (i === 1) {
                    rootNodes.add(parentPath);
                }
            }
        }

        // Assign this route under its immediate parent
        if (parentKey) {
            if (!menuTree[parentKey].children.some(child => child.id === key)) {
                menuTree[parentKey].children.push(menuTree[key]);
            }
        } else {
            // If there's no parent, it's a top-level node
            rootNodes.add(key);
        }
    });

    // Collect only top-level parent nodes
    return Array.from(rootNodes).map(root => menuTree[root]);
}



// Function to add missing routes to menu
function addMissingRoutes() {

    // copy routes
    myRoutes = routes.slice();

    $("#menuList li").each(function () {
        url = $(this).find(".menu-url-input").val();
        if (url) {
            //remove from my routes
            myRoutes = myRoutes.filter(item => item !== url);
        }
    });

    // remove all routes containing /add , /edit, /delete from myRoutes
    myRoutes = myRoutes.filter(item => !item.includes('/add'));
    myRoutes = myRoutes.filter(item => !item.includes('/edit'));
    myRoutes = myRoutes.filter(item => !item.includes('/delete'));



    let newMenuData = generateMenuTree(myRoutes);
    console.log(newMenuData);

    // Recursively add new items to the menu
    function addItems(items, parentElement) {
        items.forEach(item => {

            menuId = menuCounter++;
            itemId = "menuItem_" + menuId;

            url = item.url.split("/");
            module = "";
            permission = "";
            if (permissions[url[0]]) {
                module = url[0];
            }

            $permissionNotFoundClass = "bg-warning";
            if (permissions[module] && permissions[module]['view']) {
                permission = 'view';
                $permissionNotFoundClass = "";
            }

            // if url[1]  and it contains add , ignore this.
            if (url[1] && url[1].startsWith('add')) {
                return false;
            }

            // remove "manage" from item label
            item.label = item.label.replace('Manage', '');

            // 


            let listItem = `
                        <li class="list-group-item px-3 py-2 ${$permissionNotFoundClass}" data-id="${itemId}">
                            <div class="menu-actions">
                                <span class="menu-label">${item.label}</span>
                                <button class="btn btn-sm btn-danger px-1 py-0 mx-1 float-end deleteMenuItem"><i class='fa fa-trash'></i></button>
                                <button class="btn btn-sm btn-warning px-1 py-0 float-end toggleMenuDetails"><i class='fa fa-pencil-alt'></i></button>
                                
                            </div>
                            <div class="menu-content mt-2">
                                <div class="mb-2">
                                    <label>Label</label>
                                    <input type="text" class="form-control menu-label-input" value="${item.label}">
                                </div>
                                <div class="mb-2">
                                    <label>ICON</label>
                                    <div class="input-group">
                                        <div class="input-group-text iconPreview"></div>
                                        <input type="text" class="form-control menu-icon-input iconPicker" placeholder="Select Icon" value="${item.icon}">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label>Class</label>
                                    <input type="text" class="form-control menu-class-input" placeholder="list of classes">
                                </div>
                                <div class="mb-2">
                                    <label>Attributes</label>
                                    <input type="text" class="form-control menu-attributes-input" placeholder="list of attributes">
                                </div>
                                <div class="mb-2">
                                    <label for="moduleDropdown">Module</label>
                                    <select class="form-control module-dropdown menu-module-input select2" data-setvalue="${module}">
                                        <option value="">Select Module</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="permissionDropdown">Permissions</label>
                                    <select class="form-control permission-dropdown menu-permissions-input select2" multiple  data-setvalue="${permission}">
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label>URL</label>
                                    <select type="text" class="form-control menu-url-input routeSelector select2" data-selecttype="tags" placeholder="Enter URL" data-setvalue="${item.url}">
                                    </select>
                                </div>
                                <div class="mb-2 form-check">
                                    <input type="checkbox" class="form-check-input menu-isPopup-checkbox" id="isPopup_${itemId}" name="isPopup" data-setvalue="${item.isPopup}">
                                    <label class="form-check-label" for="isPopup_${itemId}">Open in Popup</label>
                                </div>
                            </div>
                            <ul class="nested-sortable sub-menu mt-2"></ul>
                        </li>
                    `;

            let listItemElement = $(listItem);
            parentElement.append(listItemElement);

            if (item.children.length > 0) {
                addItems(item.children, listItemElement.find(".sub-menu"));
            }

        });
    }

    addItems(newMenuData, $("#menuList"));
}


function getReady() {

    apiCall('GET', 'api/MenuConfig/getPermissions/' + tenantId).then(function (response) {

        if (response.status) {
            permissions = response.permissions;
            // console.log(permissions);
        }

        //now load routes,
        apiCall('GET', 'api/MenuConfig/getRoutes').then(function (response) {

            if (response.status) {
                routes = response.routes;
                console.log("Routes: ", routes);
            }

            // now load saved menus.
            apiCall('GET', 'api/MenuConfig/get/' + tenantId + '/' + menuLocation).then(function (response) {

                if (response.message != "") {
                    mtplAlerts.show('success', 'Menu loaded successfully!', 'Success!');
                }

                if (response.status) {
                    $("#menuList").empty();
                    renderMenu(response.menu, $("#menuList"));
                    fixUi();
                }

            }).catch(function (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the menu.');
            });

        }).catch(function (error) {
            console.error('Error:', error);
            alert('An error occurred while saving the menu.');
        });

    }).catch(function (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the menu.');
    });
}

function populateModules() {
    $(".module-dropdown").each(function () {
        let dropdown = $(this);
        const setValue = dropdown.attr('data-setvalue');
        dropdown.empty();
        dropdown.append(`<option value="">Select Module</option>`);

        Object.keys(permissions).forEach(module => {
            dropdown.append(`<option value="${module}">${module}</option>`);
        });

        if (setValue) {
            dropdown.val(setValue);
            // trigger change event
        }
    });

    updatePermissions();
}

function updatePermissions() {
    $(".module-dropdown").each(function () {
        let moduleDropdown = $(this);
        let permissionDropdown = moduleDropdown.parent().next("div").find(".permission-dropdown");
        const setValue = permissionDropdown.attr('data-setvalue');
        const selectedModule = moduleDropdown.val();

        if (selectedModule && permissions[selectedModule]) {
            permissionDropdown.empty();
            Object.keys(permissions[selectedModule]).forEach(permission => {
                permissionDropdown.append(`<option value="${permission}">${permission}</option>`);
            });

            if (setValue) {
                permissionDropdown.val(setValue.split(','));
                // trigger change event
                permissionDropdown.trigger('change');
            }
        }


        moduleDropdown.on("change", function () {
            let selectedModule = $(this).val();
            permissionDropdown.empty();
            console.log(selectedModule);
            console.log(permissions[selectedModule]);
            console.log(permissionDropdown.length);
            if (selectedModule && permissions[selectedModule]) {
                Object.keys(permissions[selectedModule]).forEach(permission => {
                    permissionDropdown.append(`<option value="${permission}">${permission}</option>`);
                });
            }
        });
    });
}

// Initialize on page load
function fixUi() {
    updateSortable();
    // applyUiLibrary();
    populateRouteDropdown();
    populateModules();
    // updatePermissions();
}