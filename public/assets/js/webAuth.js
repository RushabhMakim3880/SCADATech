jQuery(document).ready(function () {
    jQuery("#webAuthRegister").on("click", function () {
        const userId = jQuery("#webAuthUserId").val();
        if (userId) {
            startWebAuthnRegistration(userId);
        } else {
            alert("Please enter a valid user ID.");
        }
    });

    $('#loginWithBiometricBtn').on('click', loginWithBiometric);
});

// --- 1. Trigger registration (store credential) ---
async function startWebAuthnRegistration(userId) {
    const endpoint = "api/webAuth/register";
    const fingerprint = await getDeviceFingerprintUid();

    const data = {
        userId: userId,
        fingerprint: fingerprint
    };

    apiCall('POST', endpoint, data).then(function (response) {
        const publicKey = response.publicKey;

        // ✅ Decode base64url fields
        publicKey.challenge = base64urlToBuffer(response.challenge);
        publicKey.user.id = base64urlToBuffer(publicKey.user.id);
        publicKey.excludeCredentials = publicKey.excludeCredentials.map((cred) => ({
            id: base64urlToBuffer(cred.id), // ✅ convert to ArrayBuffer
            type: cred.type,
            transports: cred.transports || ['internal']
        }));


        // ✅ Prompt user for biometric/face unlock
        navigator.credentials.create({ publicKey: publicKey }).then(function (cred) {
            const credential = {
                id: cred.id,
                rawId: bufferToBase64url(cred.rawId),
                type: cred.type,
                response: {
                    clientDataJSON: bufferToBase64url(cred.response.clientDataJSON),
                    attestationObject: bufferToBase64url(cred.response.attestationObject)
                }
            };

            // ✅ Send result to backend to verify and store
            const verifyEndpoint = "api/webAuth/verify";

            apiCall('POST', verifyEndpoint, {
                credential: credential,
                challenge: response.challenge,
                deviceName: getFriendlyDeviceName(),
                fingerprint: fingerprint
            }).then(function (result) {
                if (result.success) {
                    alert("✅ Biometric login registered!");
                    localStorage.setItem('biometricUserId', userId);
                } else {
                    alert("❌ Registration failed");
                }
            });

        }).catch(err => {
            console.error('Biometric prompt failed:', err);
            alert('❌ Biometric prompt was cancelled or failed');
        });

    }).catch(err => {
        console.error('Challenge request failed:', err);
        alert('❌ Unable to initiate registration');
    });
}


function loginWithBiometric() {
    apiCall('POST', 'api/webAuth/loginStart').then((res) => {
        if (!res || !res.challenge) {
            alert('❌ No challenge received from server');
            return;
        }

        const publicKey = {
            challenge: base64urlToBuffer(res.challenge),
            timeout: 60000,
            userVerification: 'required'
        };

        navigator.credentials.get({ publicKey }).then(cred => {
            const assertion = {
                id: cred.id,
                rawId: bufferToBase64url(cred.rawId),
                type: cred.type,
                response: {
                    clientDataJSON: bufferToBase64url(cred.response.clientDataJSON),
                    authenticatorData: bufferToBase64url(cred.response.authenticatorData),
                    signature: bufferToBase64url(cred.response.signature),
                    userHandle: bufferToBase64url(cred.response.userHandle)
                }
            };

            apiCall('POST', 'api/webAuth/login', {
                assertion: assertion,
                challenge: res.challenge
            }).then(function (resp) {
                if (resp.success) {
                    alert('✅ Biometric login successful!');
                } else {
                    alert('❌ Biometric login failed');
                }
            });
        }).catch(err => {
            console.error('Biometric login error:', err);
            alert('❌ Cancelled or no credential found');
        });
    });
}



// --- Utility functions ---
function base64urlToBuffer(base64url) {
    const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
    const pad = '='.repeat((4 - base64.length % 4) % 4);
    const binary = atob(base64 + pad);
    const buffer = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
        buffer[i] = binary.charCodeAt(i);
    }
    return buffer.buffer;
}

function bufferToBase64url(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.length; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}


async function getDeviceFingerprintUid() {
    const data = [
        navigator.userAgent,
        navigator.language,
        screen.colorDepth,
        screen.width + 'x' + screen.height,
        new Date().getTimezoneOffset(),
        navigator.platform,
        navigator.hardwareConcurrency || '',
        navigator.deviceMemory || '',
        // window.outerWidth + 'x' + window.outerHeight,
    ].join('::');

    return sha256(data);
}

function sha256(message) {
    const encoder = new TextEncoder();
    const data = encoder.encode(message);

    return crypto.subtle.digest('SHA-256', data).then(hashBuffer => {
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    });
}



function getFriendlyDeviceName() {
    const ua = navigator.userAgent;

    const isAndroid = /Android/i.test(ua);
    const isIOS = /iPhone|iPad|iPod/i.test(ua);
    const isWindows = /Windows NT/i.test(ua);
    const isMac = /Macintosh/i.test(ua);
    const isLinux = /Linux/i.test(ua) && !isAndroid;

    const isChrome = /Chrome/i.test(ua) && !/Edge|OPR/i.test(ua);
    const isSafari = /Safari/i.test(ua) && !/Chrome/i.test(ua);
    const isFirefox = /Firefox/i.test(ua);
    const isEdge = /Edg/i.test(ua);

    let os = 'Unknown OS';
    if (isAndroid) os = 'Android';
    else if (isIOS) os = 'iOS';
    else if (isWindows) os = 'Windows';
    else if (isMac) os = 'macOS';
    else if (isLinux) os = 'Linux';

    let browser = 'Unknown Browser';
    if (isChrome) browser = 'Chrome';
    else if (isSafari) browser = 'Safari';
    else if (isFirefox) browser = 'Firefox';
    else if (isEdge) browser = 'Edge';

    return `${browser} on ${os}`;
}
