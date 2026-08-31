let WebSocketClient = null;
let reconnectInterval = 50000; // 5 sec
let isManuallyClosed = false;

(function ($) {
    let wsUrl;
    if (window.isSecureContext && window.location.protocol === "https:") {
        wsUrl = "wss://" + window.location.host + "/wss";
    }
    else {
        wsUrl = "ws://" + window.location.host + "/wss";
    }


    function connectWebSocket() {
        WebSocketClient = new WebSocket(wsUrl);

        WebSocketClient.onopen = function () {
            console.log("✅ WebSocket connected");
            mtplAlerts.show("success", "System Online");
        };

        WebSocketClient.onmessage = function (event) {
            parseMessage(event.data);
        };

        WebSocketClient.onclose = function (event) {
            console.log("❌ WebSocket closed", event);

            if (!isManuallyClosed) {
                mtplAlerts.show("error", "System Offline. Reconnecting... ");
                setTimeout(connectWebSocket, reconnectInterval);
            } else {
                mtplAlerts.show("error", "System Offline");
            }
        };

        WebSocketClient.onerror = function (error) {
            console.error("⚠️ WebSocket error", error);
            WebSocketClient.close();
        };
    }

    // Start initial connection
    connectWebSocket();

    // Optionally expose manual close if needed
    window.closeWebSocket = function () {
        isManuallyClosed = true;
        if (WebSocketClient) WebSocketClient.close();
    };

})(jQuery);


// Function to send a message to the WebSocket server
function sendMessage(message) {
    if (WebSocketClient && WebSocketClient.readyState === WebSocket.OPEN) {
        WebSocketClient.send(JSON.stringify(message));
    } else {
        // mtplAlerts.show("error", "❌ WebSocket is not open. Cannot send message.");

    }
}