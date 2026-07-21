
/* code to autopopulat item price/gst/description from dropdown selection */
jQuery(document).ready(function () {
    jQuery(document).on('change', '[name="manyRows[cityId]"]', function (e) {
        var test = getSelect2Attr(e, 'description');
        var price = getSelect2Attr(e, 'price');
        // console.log(test);
        // var description = jQuery(this).find('option:selected').data('description');
        // console.log(description);
        jQuery(this).closest('tr').find('[name="manyRows[discription]"]').val(description);
    });

    jQuery(".previewInvoice").click(function () {
        loadPdf();
    });

    jQuery(".downloadInvoice").click(function () {
        loadPdf(1);
    });

});


function loadPdf(download = 0) {
    apiCall('GET', 'api/newSample/generatePdf/' + download).then(function (response) {
        // console.log(response);
        if (!download) {
            var popupWindow = window.open('', '_blank', 'width=800,height=600,toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');
            popupWindow.document.write(response.data);
            popupWindow.document.close();
        }
    });
}


window.onApiReady(function () {
    // console.log("All API calls are completed. Now executing dependent logic.");
    // Your post-load logic here
});

// Another late subscription (Executes immediately if API is already ready)
setTimeout(() => {
    window.onApiReady(() => {
        // console.log("Late registered callback executed.");
    });
}, 2000);




function testCallbackForPopupResponse($data) {
    // console.log($data);
}