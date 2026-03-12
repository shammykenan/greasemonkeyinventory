const ws = new WebSocket("ws://192.168.100.11:8081");

ws.onopen = () => {
    console.log("WebSocket connected");
};

ws.onmessage = (event) => {
    console.log("Received:", event.data);

    if (event.data === "refresh_inventory") {
        if (typeof window.fetchProducts === 'function') {
            window.fetchProducts();
        } else {
            location.reload();
        }
    }
};

ws.onclose = () => {
    console.log("WebSocket disconnected");
    setTimeout(() => {
        if (typeof window.fetchProducts === 'function') {
            window.fetchProducts();
        } else {
            location.reload();
        }
    }, 3000);
};

ws.onerror = (err) => {
    console.error("WebSocket error", err);
};

window.wsBroadcast = function(message) {
    if (ws.readyState === WebSocket.OPEN) {
        ws.send(message);
    }
};