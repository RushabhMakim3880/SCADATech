/**
 * Notification Wrapper
 * This wrapper supports SweetAlert2, Notyf, and Toastr notification libraries.
 * Simply change the library name in the configuration to switch between them.
 */

const mtplAlerts = (function () {
    // Default configuration
    const defaultConfig = window.appSettings;
    let currentConfig = { ...defaultConfig };

    // Initialize libraries
    let notyfInstance = null;
    if (typeof Notyf !== 'undefined') {
        notyfInstance = new Notyf({
            duration: currentConfig.notificationDelay,
            position: {
                x: currentConfig.notificationPositionX,
                y: currentConfig.notificationPositionY,
            },
        });
    }

    //set position class for toastr
    toastrPositionClass = 'toast-top-right';
    if (currentConfig.notificationPositionX === 'left') {
        if (currentConfig.notificationPositionY === 'top') {
            toastrPositionClass = 'toast-top-left';
        }
        else {
            toastrPositionClass = 'toast-bottom-left';
        }
    }
    else if (currentConfig.notificationPositionX === 'right') {
        if (currentConfig.notificationPositionY === 'top') {
            toastrPositionClass = 'toast-top-right';
        }
        else {
            toastrPositionClass = 'toast-bottom-right';
        }
    }
    else {
        if (currentConfig.notificationPositionY === 'top') {
            toastrPositionClass = 'toast-top-center';
        }
        else {
            toastrPositionClass = 'toast-bottom-center';
        }
    }



    // Notification methods
    const notify = {
        /**
         * Set global configuration
         * @param {object} config - Configuration object to override defaults
         */
        setConfig(config) {
            currentConfig = { ...currentConfig, ...config };
        },

        /**
         * Show notification with optional temporary configuration
         * @param {string} type - Notification type ('info', 'success', 'warning', 'error')
         * @param {string} message - Notification message
         * @param {string} [title] - Optional title for the notification
         * @param {object} [tempConfig] - Temporary configuration for this notification
         */
        show(type, message, title = '', tempConfig = {}) {
            const config = { ...currentConfig, ...tempConfig };

            switch (config.notificationLibrary) {
                case 'SweetAlert2':
                    Swal.fire({
                        title: title,
                        text: message,
                        icon: type,
                        timer: config.notificationDelay,
                        showConfirmButton: config.notificationCloseButton,
                        timerProgressBar: config.notificationProgressBar,
                        // position: config.position.replace('-', ' '),
                    });
                    break;

                case 'Notyf':
                    if (!notyfInstance) return;
                    notyfInstance.open({
                        type: type,
                        message: message,
                        duration: config.notificationDelay,
                    });
                    break;

                case 'Toastr':
                    toastr.options = {
                        closeButton: config.notificationCloseButton,
                        progressBar: config.notificationProgressBar,
                        positionClass: toastrPositionClass,
                        timeOut: config.notificationDelay,
                    };
                    toastr[type](message, title);
                    break;

                default:
                    console.error('Unsupported library selected.');
            }

            // Play alarm sound
            if (parseInt(config.notificationPlaySound) && typeof playAlarm === 'function')
                playAlarm(type);
        },

        /**
         * Show confirmation dialog
         * @param {string} message - Confirmation message
         * @param {function} onConfirm - Callback for when user confirms
         * @param {function} onCancel - Callback for when user cancels
         * @param {object} [tempConfig] - Temporary configuration for this confirmation
         */
        confirm(message, onConfirm, onCancel, tempConfig = {}) {
            const config = { ...currentConfig, ...tempConfig };

            if (config.notificationLibrary === 'SweetAlert2') {
                Swal.fire({
                    title: 'Confirm',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    position: config.position.replace('-', ' '),
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (onConfirm) onConfirm();
                    } else {
                        if (onCancel) onCancel();
                    }
                });
            } else {
                console.error('Confirmation dialogs are only supported with SweetAlert2 in this wrapper.');
            }
        },
    };

    return notify;
})();