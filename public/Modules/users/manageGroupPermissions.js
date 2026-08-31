window.onApiReady(function () {
    setTimeout(function () {
        loadGroupPermissions();
    }, 100);

    // Save permissions
    // trigger on change of checkbox inside the permissionsContainer
    $('#savePermissions').on('click', function () {
        savePermissions();
    });

    // on change groupId
    $('#groupId').on('change', function () {
        loadGroupPermissions();
    });

});

function loadGroupPermissions() {
    var groupId = $('#groupId').val();

    if (!groupId)
        return;

    var url = 'api/users/loadGroupPermissions/' + groupId;
    apiCall('GET', url).then(function (response) {
        if (response.message != "") {
            // mtplAlerts.show('success', response.message, 'Success');
        }

        generatePermissionCheckboxes(response.data, 'permissionsContainer');

    }).catch(function (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the menu.');
    });
}


function generatePermissionCheckboxes(permissions, containerId) {
    const $container = $('#' + containerId);
    if (!$container.length) return;

    const modules = {};

    // Group permissions by module
    $.each(permissions, function (_, permission) {
        if (!modules[permission.module]) {
            modules[permission.module] = [];
        }
        modules[permission.module].push(permission);
    });

    $container.empty(); // Clear previous content

    const $grid = $('<div class="permission-grid"></div>'); // Masonry-style grid container

    $.each(modules, function (module, perms) {
        const $card = $(`
            <div class="permission-card">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">${camelToHuman(module)}</div>
                    <div class="card-body p-2"></div>
                </div>
            </div>
        `);

        const $cardBody = $card.find('.card-body');

        $.each(perms, function (_, permission) {
            const checked = permission.permissionValue === "1" ? 'checked' : '';

            const $checkbox = $(`
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="perm_${permission.permissionId}"
                        data-permission-id="${permission.permissionId}" ${checked}>
                    <label class="form-check-label" for="perm_${permission.permissionId}">${camelToHuman(permission.permission)}</label>
                </div>
            `);

            $cardBody.append($checkbox);
        });

        $grid.append($card);
    });

    $container.append($grid);
}

// Function to retrieve selected permissions as JSON object
function getSelectedPermissions(containerId) {
    const permissions = [];

    $('#' + containerId + ' input[type="checkbox"]').each(function () {
        if ($(this).is(':checked')) {
            permissions.push($(this).data('permission-id'));
        }
    });

    return permissions;
}


function savePermissions() {
    var groupId = $('#groupId').val();
    var permissions = getSelectedPermissions('permissionsContainer');
    var url = 'api/users/saveGroupPermissions';

    apiCall('POST', url, { groupId: groupId, permissions: permissions }).then(function (response) {
        if (response.message != "") {
            mtplAlerts.show('success', response.message, 'Success');
        }
    }).catch(function (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the menu.');
    });
}