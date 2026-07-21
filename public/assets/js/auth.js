let captchaKey = '';
let deviceToken = localStorage.getItem('deviceToken');
if (!deviceToken) {
    deviceToken = generateUUID(); // Or any random string
    localStorage.setItem('deviceToken', deviceToken);
}

function generateUUID() {
    if (window.crypto?.randomUUID) return crypto.randomUUID();

    // fallback
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

document.addEventListener('keydown', function (e) {
    if (e.ctrlKey && e.altKey && e.shiftKey && e.code === 'Backquote') {

        jQuery("#devPasswordInput").parent().show();
        jQuery("#devPasswordInput").focus();
    }
});

function refreshCaptcha() {

    if (jQuery("#captchaImage").length == 0) {
        return;
    }

    fetch(base_url + 'tools/captcha')
        .then(response => {
            captchaKey = response.headers.get('X-Captcha-Key');
            return response.blob();
        })
        .then(blob => {
            const url = URL.createObjectURL(blob);
            document.getElementById('captchaImage').src = url;
        });
}

jQuery(document).ready(function () {
    console.log('Login page loaded');
    // const ifBioLogin = localStorage.getItem('biometricUserId');
    // if (ifBioLogin !== null) {
    jQuery('#loginWithBiometricBtn')
        .show()
        .off('click')
        .on('click', loginWithBiometric);
    // }



    loginCheck();
    refreshCaptcha();
    jQuery(".fa-spinner").hide();

    jQuery(".loginAgain").click(function () {
        jQuery('#forgotPasswordForm').hide();
        jQuery('#loginForm').show();
        refreshCaptcha();
    });

    jQuery(".forgotPassword").click(function () {
        jQuery('#loginForm').hide();
        jQuery('#forgotPasswordForm').show();
    });


    // hande form submission with ajax api request.

    jQuery('#loginForm').submit(function (e) {
        e.preventDefault();

        jQuery(".fa-spinner").show();

        jQuery.ajax({
            url: base_url + 'api/auth/login',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Captcha-Key': captchaKey
            },
            data: JSON.stringify({
                username: jQuery('#username').val(),
                password: jQuery('#password').val(),
                devModePassword: jQuery("#devPasswordInput").val(),
                rememberMe: jQuery('#rememberMe').is(':checked'),
                captcha: jQuery('#captcha').val(),
                deviceToken: deviceToken,
            }),
            success: function (response) {
                // console.log(response);
                var token = response.data.token;
                var refreshToken = response.data.refreshToken;

                // set cookie
                // document.cookie = "jwt=" + token + "; path=/; Secure; SameSite=Lax";
                // document.cookie = "refreshToken=" + refreshToken + "; path=/; Secure; SameSite=Lax";

                // set local storage
                // localStorage.setItem('jwt', token);
                localStorage.setItem('userProfile', JSON.stringify(response.data.user));
                // localStorage.setItem('refreshToken', refreshToken);

                //check if totp is enabled and required verification
                if (response.data.totpEnabled) {

                    mtplAlerts.show('warning', 'Enter your secret code', '2FA Required');

                    jQuery('#loginForm').hide();
                    jQuery('#totpVerification').show();
                    return;
                }

                mtplAlerts.show('success', 'Login Successfull!', 'Success');

                if (window.flutter_inappwebview) {
                    window.flutter_inappwebview.callHandler('NativeChannel', {
                        action: 'tokens',
                        jwtTokens: token,
                        refreshTokens: refreshToken
                    });
                    // mtplAlerts.show('warning', 'Tokens sent to Flutter InAppWebView.');
                } else {
                    // alert('Flutter InAppWebView not found. Tokens not sent to Flutter.');
                }

                // redirect to home page
                setTimeout(function () {
                    //get url parameter redirect
                    var urlParams = new URLSearchParams(window.location.search);
                    var redirect = urlParams.get('redirect');
                    if (redirect) {
                        window.location.href = redirect;
                    }
                    else {
                        window.location.href = base_url;
                    }
                }, 1000);
            },
            error: function (response) {

                $msg = "";
                for (var key in response.responseJSON.messages) {
                    $msg += response.responseJSON.messages[key] + "\n";
                };
                console.log($msg);

                mtplAlerts.show('error', $msg, 'Error');
            },
            complete: function () {
                jQuery(".fa-spinner").hide();
            }
        });
    });


    jQuery('#forgotPasswordForm').submit(function (e) {
        e.preventDefault();
        jQuery.ajax({
            url: base_url + 'api/auth/resetPassword',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Captcha-Key': captchaKey
            },
            data: JSON.stringify({
                username: jQuery('#resetUser').val()
            }),
            success: function (response) {
                console.log(response);

                mtplAlerts.show('success', 'Password reset link sent to your email.', 'Success');

                jQuery(".loginAgain").click();
            },
            error: function (response) {

                $msg = "";
                for (var key in response.responseJSON.messages) {
                    $msg += response.responseJSON.messages[key] + "\n";
                };

                mtplAlerts.show('error', $msg, 'Error');
            }
        });
    });

    jQuery('#totpVerification').submit(function (e) {
        e.preventDefault();
        jwtToken = localStorage.getItem('jwt');
        jQuery.ajax({
            url: base_url + 'api/auth/verifyTotp',
            type: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + jwtToken
            },
            data: JSON.stringify({
                totp: jQuery('#totpInput').val()
            }),
            success: function (response) {
                var token = response.data.token;

                mtplAlerts.show('success', 'Login Successfull!', 'Success');

                // set cookie
                // document.cookie = "jwt=" + token + "; path=/; Secure; SameSite=Strict";

                // set local storage
                // localStorage.setItem('jwt', token);

                // redirect to home page
                setTimeout(function () {
                    window.location.href = base_url;
                }, 1000);
            },
            error: function (response) {

                $msg = "";
                for (var key in response.responseJSON.messages) {
                    $msg += response.responseJSON.messages[key] + "\n";
                };

                mtplAlerts.show('error', $msg, 'Error');
            }
        });
    });

});


function loginCheck() {

    jQuery("body").hide();

    endpoint = 'api/system/loginCheck';
    apiCall('GET', endpoint).then(function (response) {
        if (response.status) {
            // Get the current URL from the browser
            const currentUrl = window.location.href;

            // Create a URL object to easily parse the current URL
            const url = new URL(currentUrl);

            // Get the value of the 'redirect' query parameter
            const redirectUrl = url.searchParams.get('redirect');

            // Check if a redirect URL was found and if it's a valid URL
            if (redirectUrl) {
                try {
                    // Decode the URL-encoded redirect URL
                    const decodedRedirectUrl = decodeURIComponent(redirectUrl);

                    // Optional: Basic validation to ensure it's a "safe" redirect within your domain
                    // This is important to prevent open redirect vulnerabilities.
                    // Assuming your base domain is 'new.test'. Adjust as needed.
                    const redirectHost = new URL(decodedRedirectUrl).host;
                    const currentHost = window.location.host;

                    if (redirectHost === currentHost) {
                        console.log("Redirecting to:", decodedRedirectUrl);
                        window.location.href = decodedRedirectUrl;
                    } else {
                        console.warn("Redirect URL points to a different host, preventing redirect:", decodedRedirectUrl);
                        // You might want to redirect to a default page or show an error here
                        // window.location.href = '/default-dashboard';
                    }
                } catch (e) {
                    console.error("Error decoding or parsing redirect URL:", e);
                    // Handle malformed redirect URL, maybe redirect to a default page
                    // window.location.href = '/error-page';
                }
            } else {
                console.warn("No 'redirect' parameter found in the URL.");
                // Handle cases where there's no redirect parameter
                // Maybe redirect to a default dashboard or homepage
                // window.location.href = '/default-homepage';
            }
        }
        else {
            jQuery("body").show();
        }
    }).catch(function (error) {
        jQuery("body").show();
    });
}

function loginWithBiometric() {
    console.log('Login with biometric');
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
            }).then(function (response) {
                if (response.status) {
                    var token = response.data.token;
                    var refreshToken = response.data.refreshToken;

                    // set cookie
                    // document.cookie = "jwt=" + token + "; path=/; Secure; SameSite=Lax";
                    // document.cookie = "refreshToken=" + refreshToken + "; path=/; Secure; SameSite=Lax";

                    // set local storage
                    // localStorage.setItem('jwt', token);
                    localStorage.setItem('userProfile', JSON.stringify(response.data.user));
                    // localStorage.setItem('refreshToken', refreshToken);

                    //check if totp is enabled and required verification
                    if (response.data.totpEnabled) {

                        mtplAlerts.show('warning', 'Enter your secret code', '2FA Required');

                        jQuery('#loginForm').hide();
                        jQuery('#totpVerification').show();
                        return;
                    }

                    mtplAlerts.show('success', 'Login Successfull!', 'Success');

                    // redirect to home page
                    setTimeout(function () {
                        //get url parameter redirect
                        var urlParams = new URLSearchParams(window.location.search);
                        var redirect = urlParams.get('redirect');
                        if (redirect) {
                            window.location.href = redirect;
                        }
                        else {
                            window.location.href = base_url;
                        }
                    }, 1000);
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