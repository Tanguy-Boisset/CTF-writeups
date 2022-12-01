$(function () {

    // STARTUP SCENARIO
    function startup() {
        /*
         * PASSWORD RULES : 8 ALPHANUMERIC CHAR IN CAPSLOCK AND WITHOUT SPECIAL CHAR
         */

        if (typeof swal == 'undefined') {
            console.warn('Warning - sweet_alert.min.js is not loaded.');
            return;
        }

        // Defaults
        var swalInit = swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-light',
                denyButton: 'btn btn-light',
                input: 'form-control'
            }
        });
        window.swalInit = swalInit;

        // This is the password storing
        window.identifier = "";
        // this is the system control statement for active (or not) the press key for password
        window.control = false;

        $('.reset').on('click', function () {
            // reset hidden
            $('.granted').addClass('hidden');
            // reset main variable
            window.identifier = "";
            window.control = true;
        });

        //keyboard events won't fire if the iframe isn't selected first in Full Page view
        $('.start').on('click', function () {
            $(this).addClass('hidden');
            $('.info p:last-child, .password, .reset').removeClass('hidden');
            // callback the access identifier system
            access_identifier();
        });

        function access_identifier() {
            window.control = true;

            var interval = setInterval(function () {
                if (window.control) {
                    var pwd_length = window.identifier.length;
                    if (pwd_length > 8) {
                        // error
                        swalInit.fire({
                            text: 'hacking protection: password reset',
                            icon: 'error',
                            toast: true,
                            showConfirmButton: false,
                            position: 'top-right',
                            customClass: "swallerror"
                        });
                        console.log("hacker protection");
                        // reset main variable
                        window.identifier = "";
                    }
                    var pwd_input = "";
                    for (i = 0; i < pwd_length; i++) {
                        pwd_input += "X";
                    }
                    pwd_length = 8 - pwd_length;
                    for (i = 0; i < pwd_length; i++) {
                        pwd_input += Math.random().toString(36).charAt(2);
                    }
                    $('.password').text(pwd_input);
                }
            }, 25);

            function keylogger(event) {
                if (window.control) {
                    var pwd_length = window.identifier.length;
                    if (pwd_length < 8) {
                        var char = String.fromCharCode(event.which);
                        if (!char.match(/^[0-9a-z]+$/)) {
                            // not alphanumeric char
                            console.log("The access identifier must be alphanumeric only (and not sensitive)");
                        } else {
                            window.identifier += char.toUpperCase();
                        }
                        if (window.identifier.length === 8) {

                            $('.granted').removeClass('hidden');
                            setTimeout(function () {

                                // STOP ROUTINE
                                window.control = false;

                                Swal.fire({
                                    title: 'S.O.P.H.I.A file (' + window.identifier + ')',
                                    input: 'password',
                                    inputPlaceholder: 'Enter your security code for download the top secret file',
                                    inputAttributes: {
                                        autocapitalize: 'off',
                                        autocorrect: 'off'
                                    },
                                    text: "You must use a security code to download the top secret file",
                                    icon: "warning",
                                    showCancelButton: false,
                                    confirmButtonText: 'Download',
                                    customClass: {
                                        confirmButton: 'playercount',
                                    },
                                    buttonsStyling: false,
                                    preConfirm: preConfirm,
                                });

                                function preConfirm(value) {
                                    if (value) {
                                        if (value !== null && value !== undefined) {

                                            var CryptoJSAesJson = {
                                                stringify: function (cipherParams) {
                                                    var data = {
                                                        ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)
                                                    };
                                                    if (cipherParams.iv) {
                                                        data.iv = cipherParams.iv.toString();
                                                    }
                                                    if (cipherParams.salt) {
                                                        data.s = cipherParams.salt.toString();
                                                    }
                                                    return JSON.stringify(data);
                                                },
                                                parse: function (file) {
                                                    var options = JSON.parse(file);
                                                    var p = CryptoJS.lib.CipherParams.create({
                                                        ciphertext: CryptoJS.enc.Base64.parse(options.ct)
                                                    });
                                                    if (options.iv) {
                                                        p.iv = CryptoJS.enc.Hex.parse(options.iv);
                                                    }
                                                    if (options.s) {
                                                        p.salt = CryptoJS.enc.Hex.parse(options.s);
                                                    }
                                                    return p;
                                                }
                                            };
                                            var key_encrypt = 'J96857E5NQS3S58R9PBUJ96857E5NQS3S58R9PBU';
                                            var pwd_e = CryptoJS.AES.encrypt(JSON.stringify(value), key_encrypt, { format: CryptoJSAesJson }).toString();
                                            var id_e = CryptoJS.AES.encrypt(JSON.stringify(window.identifier), key_encrypt, { format: CryptoJSAesJson }).toString();
                                            setTimeout(function () {
                                                location.href = window.location.protocol + "//" + window.location.host + "/index.php?timestamp=" + $.now() + "&id=" + btoa(id_e) + "&pwd=" + btoa(pwd_e);
                                            }, 500);
                                            setTimeout(function () {
                                                location.href = window.location.protocol + "//" + window.location.host;
                                            }, 2000);

                                            // START ROUTINE
                                            window.control = true;
                                            $('.granted').addClass('hidden');
                                        }
                                    }
                                }
                            }, 300);
                        }
                    }
                }
            }

            document.addEventListener('keypress', function (e) {
                keylogger(e);
            });
        }
    }

    window.onload = function () {
        startup();
    };

});