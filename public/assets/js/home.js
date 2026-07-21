// console.log("Home page script loaded");



// Load status on page load
window.addEventListener('load', function () {
    getCallPopupStatus();
});

function logOutput(text) {
    document.getElementById('output').textContent = text;
}

function updateStatus(enabled) {
    const statusElement = document.getElementById('callPopupStatus');
    statusElement.textContent = 'Status: ' + (enabled ? 'ENABLED' : 'DISABLED');
}

async function getContacts() {
    if (window.flutter_inappwebview) {
        const contacts = await window.flutter_inappwebview.callHandler('NativeChannel', { action: 'getContacts' });

        let parsed;
        try {
            parsed = JSON.parse(contacts);
        } catch (e) {
            parsed = contacts;
        }

        const output = document.getElementById('output');
        output.innerHTML = '<pre>' + JSON.stringify(parsed, null, 2) + '</pre>';
    } else {
        alert("Flutter bridge not available");
    }
}

async function getCallLogs() {
    if (window.flutter_inappwebview) {
        const callLogs = await window.flutter_inappwebview.callHandler('NativeChannel', { action: 'getCallLogs' });
        document.getElementById('output').innerText = callLogs;
    } else {
        alert("Flutter bridge not available");
    }
}

async function getDeviceInfo() {
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'getDeviceInfo'
    });
    logOutput(result);
}

async function getPermissionList() {
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'getPermissionList'
    });
    logOutput(result);
}

async function getContactPermission() {
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'getContactPermission'
    });
    logOutput(result);
}

async function getPhonePermission() {
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'getPhonePermission'
    });
    logOutput(result);
}

async function getIgnoreBatteryOptimizationPermission() {
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'getIgnoreBatteryOptimizationPermission'
    });
    logOutput(result);
}

async function getSystemAlertWindowPermission() {
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'getSystemAlertWindowPermission'
    });
    logOutput(result);
}

async function sendTokens() {
    const jwtToken = 'sample_jwt_token';
    const refreshToken = 'sample_refresh_token';
    const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
        action: 'tokens',
        jwtTokens: jwtToken,
        refreshTokens: refreshToken
    });
    logOutput('Tokens sent.\nJWT: ' + jwtToken + '\nRefresh: ' + refreshToken);
}

async function enableCallPopup() {
    try {
        const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
            action: 'setCallPopupEnabled',
            enabled: true
        });
        const response = JSON.parse(result);
        if (response.success) {
            updateStatus(true);
            logOutput('Call popup ENABLED successfully');
        } else {
            logOutput('Failed to enable call popup');
        }
    } catch (error) {
        logOutput('Error enabling call popup: ' + error);
    }
}

async function disableCallPopup() {
    try {
        const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
            action: 'setCallPopupEnabled',
            enabled: false
        });
        const response = JSON.parse(result);
        if (response.success) {
            updateStatus(false);
            logOutput('Call popup DISABLED successfully');
        } else {
            logOutput('Failed to disable call popup');
        }
    } catch (error) {
        logOutput('Error disabling call popup: ' + error);
    }
}

async function getCallPopupStatus() {
    try {
        const result = await window.flutter_inappwebview.callHandler('NativeChannel', {
            action: 'getCallPopupStatus'
        });
        const response = JSON.parse(result);
        updateStatus(response.enabled);
        logOutput('Call popup status: ' + (response.enabled ? 'ENABLED' : 'DISABLED'));
    } catch (error) {
        logOutput('Error getting call popup status: ' + error);
        updateStatus(false);
    }
}