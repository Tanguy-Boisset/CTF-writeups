$(function () {

    // ADMIN UPLOAD SCENARIO
    function admin() {

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

        //keyboard events won't fire if the iframe isn't selected first in Full Page view
        $('.upload').on('click', function () {
            upload_file();
        });

        function upload_file() {
            Swal.fire({
                title: 'UPLOAD FILE',
                input: 'file',
                inputAttributes: {
                    name:"upload",
                    id:"upload"
                },
                icon: "info",
                showCancelButton: false,
                confirmButtonText: 'Download',
                customClass: {
                    confirmButton: 'playercount',
                },
                buttonsStyling: false
            }).then(function() {
                var formData = new FormData();
                var file = $('.swal2-file')[0].files[0];
                formData.append('upload', file);

                var currentdate = new Date();
                var datetime = "date: " + currentdate.getDate() + "/"
                    + (currentdate.getMonth()+1)  + "/"
                    + currentdate.getFullYear() + " @ "
                    + currentdate.getHours() + ":"
                    + currentdate.getMinutes() + ":"
                    + currentdate.getSeconds();

                $.ajax({
                    type: "POST",
                    url: "admin.php",
                    data: formData,
                    processData: false,
                    contentType: false,
                    async: false,
                    success: function (resp) {
                        window.swalInit.fire({
                            text: "Upload Success",
                            icon: "success",
                            toast: true,
                            showConfirmButton: false,
                            position: "top-right",
                            customClass: "swallsuccess"
                        });
                        var response = jQuery.parseJSON(resp);
                        $(".admininfo").removeClass('hidden');
                        $(".admininfo").append('<p>'+datetime+' - Upload Success : '+response["identifier"]+' : '+response["password"]+'</p>');
                    },
                    error: function() {
                        window.swalInit.fire({
                            text: "Upload Error",
                            icon: "error",
                            toast: true,
                            showConfirmButton: false,
                            position: "top-right",
                            customClass: "swallerror"
                        });

                        $(".admininfo").removeClass('hidden');
                        $(".admininfo").append('<p>'+datetime+' - Upload error<span class="blink">_</span></p>');
                    }
                });
            });
        }
    }

    window.onload = function () {
        admin();
    };

});