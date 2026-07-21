// assets/js/sharedWorkerScript.js
const ports = [];
let eventSource;
let eventSourceActive = false;

onconnect = function (e) {
    const port = e.ports[0];
    ports.push(port);
    port.start();
    console.log('A new tab connected to Shared Worker.');

    // Start SSE connection if not already active
    if (!eventSourceActive) {
        startSSE();
    }

    port.onmessage = function (event) {
        console.log('Worker received message from tab:', event.data);
        // You can handle messages from tabs here if needed
    };
};

function startSSE() {
    const origin = self.location.protocol + '//' + self.location.host;
    const sseUrl = origin + '/home/sseStream';

    eventSource = new EventSource(sseUrl);
    eventSourceActive = true;
    console.log('SSE connection started with URL:', sseUrl);

    eventSource.onmessage = function (event) {
        ports.forEach(function (port) {
            port.postMessage(event.data);
        });
    };

    eventSource.onerror = function (error) {
        console.error('SSE error:', error);
    };
}
