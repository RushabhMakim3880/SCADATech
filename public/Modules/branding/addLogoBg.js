$(document).ready(function () {

    $('.brandingForm').submit(function (e) {
        e.preventDefault();
        // alert('before getFormDataWithFiles');

        var $form = $(this);
        var { jsonData, fileData, fileFound } = getFormDataWithFiles($form[0]);
        // alert('after getFormDataWithFiles');

        postData = jsonData;
        // alert('after postdata set');
        // console.log(postData);

        var endpoint = 'api/system/getLogoAndBg';

        apiCall('POST', endpoint, postData, fileFound = false).then(function (response) {
            // alert('inside api call');
            console.log(response);

            if (response.message != "") {
                mtplAlerts.show('success', response.message, 'Success');
                // alert('inside api if');
            }
            if (!recordId) {
                $form[0].reset();
            }
        }).catch(function (error) {
            // console.error('Error in GET:', error);
            // console.error('Submission error:', error.textStatus, error.errorThrown);
        });
    });

});
