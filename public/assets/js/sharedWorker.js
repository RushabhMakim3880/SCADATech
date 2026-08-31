// assets/js/main.js
$(document).ready(function () {
    if (window.SharedWorker) {
        // Create the Shared Worker instance
        var worker = new SharedWorker(base_url + 'assets/js/sharedWorkerScript.js');

        console.log('Shared Worker created:', worker);

        // Handle messages received from the worker
        worker.port.onmessage = function (event) {
            console.log('Received from worker:', event.data);
            // Append the message to the #swMessages div
            $('#swMessages').append($('<p>').text(event.data));
        };

        // Start communication with the worker
        worker.port.start();
    } else {
        console.error('Shared Workers are not supported in this browser.');
    }
});
