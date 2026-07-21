var blankImg = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQImWNgYGBgAAAABQABh6FO1AAAAABJRU5ErkJggg==";
var selectedImageInput = null;
var selectedType = "square";
var selectedHeight = 500;
var selectedWidth = 500;
var selectedWebcam = false;
var freeCrop = false;

function getRoundedCanvas(sourceCanvas) {
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    //removing this to fix resize (down scale) image to 500x500 px size... 

    if (freeCrop) {
        var width = sourceCanvas.width;
        var height = sourceCanvas.height;

        //resize to max 500 px h or w.
        if (width > height) {
            width = selectedWidth;
            height = width * (sourceCanvas.height / sourceCanvas.width);
        }
        else {
            height = selectedHeight;
            width = height * (sourceCanvas.width / sourceCanvas.height);
        }


    } else {
        var width = selectedWidth;
        var height = selectedHeight;
    }


    canvas.width = width;
    canvas.height = height;
    context.imageSmoothingEnabled = true;
    context.drawImage(sourceCanvas, 0, 0, width, height);
    context.globalCompositeOperation = 'destination-in';
    if (selectedType !== "circle") {
        return canvas;
    }

    context.beginPath();
    context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
    context.fill();
    return canvas;
}

jQuery(document).ready(function () {

    jQuery('body').append('<!-- Modal --><div id="camModal" class="modal fade" role="dialog"><div class="modal-dialog"><!-- Modal content--><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Camera Preview</h5><button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button></div><div class="modal-body"><div id="cam_container"></div></div><div class="modal-footer"><button type="button" class="btn btn-success capture_btn">Capture New</button><button type="button" class="btn btn-warning browse_btn">Browse</button><button type="button" class="btn btn-warning clear_btn">Clear Old</button><button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button></div></div></div></div><div id="cropModal" class="modal fade" role="dialog"><div class="modal-dialog"><!-- Modal content--><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Preview</h5><button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"><div class="alert alert-info">Use mouse wheel or bouble finger (touch screen) to zoom in/out image inside crop area.</div><div class="crop-img-container"></div></div><div class="modal-footer"><button class="btn btn-info rotateAC"><i class="fa fa-undo"></i> (-10)</button><button class="btn btn-info rotateC"><i class="fa fa-repeat"></i> (+5)</button><button type="button" class="btn save btn-primary" data-bs-dismiss="modal">Crop</button></div></div></div></div>');


    if (jQuery(".user_pic_container").attr("src") == "") {
        //jQuery(".user_pic_container").attr("src", blankImg);
    }


    source = document.querySelector('.crop-img-container');
    save = document.querySelector('.save');

    jQuery("body").on("click", ".user_pic_container_box", function () {


        selectedImageInput = jQuery(this).find(".user_pic_container");
        selectedType = "square";
        if (selectedImageInput.data("type") == "circle")
            selectedType = "circle";

        selectedWidth = 500;
        if (parseInt(selectedImageInput.data("width")) > 0)
            selectedWidth = parseInt(selectedImageInput.data("width"));

        freeCrop = false;
        console.log("asdf", selectedImageInput.data("freecrop"));
        if (parseInt(selectedImageInput.data("freecrop")) == 1)
            freeCrop = true;

        selectedHeight = 500;
        if (parseInt(selectedImageInput.data("height")) > 0 && selectedType !== "circle")
            selectedHeight = parseInt(selectedImageInput.data("height"));

        selectedWebcam = false;
        if (selectedImageInput.data("src") == "webcam")
            selectedWebcam = true;


        if (is_mobile() || selectedWebcam == false) {
            jQuery("#file_browse").trigger("click");
        } else {
            jQuery("#camModal").modal('show');
        }

    })

    $('#camModal').on('shown.bs.modal', function () {

        Webcam.set({
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
        });
        Webcam.attach('#cam_container');
    })

    $("body").on("click", ".browse_btn", function () {
        jQuery("#camModal").modal('hide');
        jQuery("#file_browse").trigger("click");
    })

    $('#camModal').on('hide.bs.modal', function () {
        Webcam.reset();
    })

    $("body").on("click", ".capture_btn", function () {
        // take snapshot and get image data
        Webcam.snap(function (data_uri) {

            let img = document.createElement('img');
            img.id = 'image';
            img.src = data_uri;
            // clean result before
            source.innerHTML = '';
            // append new image
            source.appendChild(img);
            Webcam.reset();
            jQuery("#camModal").modal('hide');
            $("#cropModal").modal("show");
        });
    })

    $("body").on("click", ".clear_btn", function () {
        selectedImageInput.attr("src", blankImg);
        jQuery("#camModal").modal('hide');
        selectedImageInput.next(".selectedImageInput").val("");
    })


    var clean = function () {
        selectedImageInput.attr("src", blankImg);
        jQuery("#camModal").modal('hide');
        selectedImageInput.next(".selectedImageInput").val("");
    };

    jQuery("body").on("click", ".imgremovebtn", function () {
        jQuery(this).parent().find(".user_pic_container").attr("src", blankImg);
        jQuery(this).parent().find(".value_container").val("");
        return false;
    });
    //on change file uplaoder
    jQuery("body").on("change", "#file_browse", function (e) {
        cropper_img = 1;
        if (e.target.files.length) {
            // start file reader
            const reader = new FileReader();
            reader.onload = e => {
                if (e.target.result) {
                    // create new image
                    let img = document.createElement('img');
                    img.id = 'image';
                    img.src = e.target.result;
                    // clean result before
                    source.innerHTML = '';
                    // append new image
                    source.appendChild(img);
                    $("#cropModal").modal("show");

                }
            };
            reader.readAsDataURL(e.target.files[0]);
        }

        jQuery(this).val("");
        jQuery(this).prop("required", false);
    });
    var cropBoxData;
    var canvasData;
    var cropper;
    var croppable = false;
    jQuery("body").on("click", ".rotateAC", function () {
        cropper.rotate(-10);
    });
    jQuery("body").on("click", ".rotateC", function () {
        cropper.rotate(5);
    });
    $('#cropModal').on('shown.bs.modal', function () {

        aspectRatio = selectedWidth / selectedHeight;
        cropBoxResizable = false;
        if (freeCrop) {
            aspectRatio = NaN;
            cropBoxResizable = true;
        }

        cropper = new Cropper(document.getElementById('image'), {
            aspectRatio: aspectRatio,
            viewMode: 0,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            ready: function () {
                //Should set crop box data first here
                croppable = true;
                cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);

                if (selectedType != "circle") {
                    jQuery(".cropper-view-box").css("border-radius", "0");
                }
            }
        });



    }).on('hidden.bs.modal', function () {
        var croppedCanvas;
        var roundedCanvas;
        var roundedImage;
        if (!croppable) {
            return;
        }

        // Crop
        croppedCanvas = cropper.getCroppedCanvas();
        if (croppedCanvas.width < 100 || croppedCanvas.height < 100) {
            //alert("");
            alert('Selected Or Cropped image is too small, please choose little big image');
            cropper.destroy();
            clean();
            return false;
        }


        // Round
        roundedCanvas = getRoundedCanvas(croppedCanvas);
        console.log(croppedCanvas);
        console.log(roundedCanvas);
        // Show
        roundedImage = document.createElement('img');
        roundedImage.src = roundedCanvas.toDataURL()
        roundedImage.classList.add('img-fluid');
        $("#base64_result_single").val(roundedImage.src);
        jQuery(".ace-file-placeholder").addClass("d-none");
        jQuery(".ace-file-item").removeClass("d-none");
        jQuery(".ace-file-item").addClass("d-flex");
        jQuery(".removebtn").removeClass("remove");
        var imageUrl = roundedImage.src;
        selectedImageInput.attr("src", imageUrl);
        selectedImageInput.parents(".imageUploader").find(".value_container").val(imageUrl);
        //        jQuery(".thumbnail-large img").css("background-image", "url(" + imageUrl + ")");



        //        document.getElementById(crop_save_target).innerHTML = '';
        //        document.getElementById(crop_save_target).appendChild(roundedImage);
        cropper.destroy();
    });
});

function is_mobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}
