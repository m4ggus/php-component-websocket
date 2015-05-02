(function(MIB, undefined){

    function htmlEntities(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function createCommandElement(msg, code) {
        var container = document.createElement('p'),
            element   = container;

        if (!!code) {
            element = document.createElement('pre');
            container.appendChild(element);
        }

        // element.textContent
        element.innerHTML = htmlEntities(msg);

        return container;
    }

    function scrollToBottom(element) {
        var scrollTop = element.scrollTop,
            scrollHeight = element.scrollHeight,
            scrollTime = 500,
            scrollSteps = 20,
            scrollInterval = scrollTime / scrollSteps,
            scrollStep = (scrollHeight - scrollTop) / scrollSteps,
            scrollCurrent = 0;

        var fnc = function() {
            if (scrollCurrent == scrollSteps) {
                return;
            }

            element.scrollTop += scrollStep;

            scrollCurrent++;

            setTimeout(fnc, scrollInterval);
        };

        setTimeout(fnc, scrollInterval);


    }

    function WebSocketClient(url)
    {
        var socket = new WebSocket(url);

        socket.onopen = function(event) {
            var msg = 'Connected to: ' + event.currentTarget.url;
            MIB.debug.appendChild(createCommandElement(msg));
        };
        socket.onmessage = function(event) {
            MIB.debug.appendChild(createCommandElement(event.data, true));
            //MIB.debug.scrollTop = MIB.debug.scrollHeight;
            scrollToBottom(document.getElementsByTagName('body')[0]);
        };
        socket.onclose = function (event) {
            var msg = 'Connection closed by: ' + event.currentTarget.url;
            MIB.debug.appendChild(createCommandElement(msg));
        };

        socket.onerror = function (event) {
            var msg = 'Error ' + event.currentTarget.url;
            MIB.debug.appendChild(createCommandElement(msg));
        };

        this.socket = socket;
    }

    WebSocketClient.prototype.send = function(msg) {
        this.socket.send(msg);
    };

    WebSocketClient.prototype.close = function() {
        this.socket.close();
    };

    MIB.WebSocketClient = WebSocketClient;

    function onLoad() {
        MIB.socket = new WebSocketClient('ws://localhost:9999');
        MIB.debug  = document.getElementById('debug');
        MIB.command = document.getElementById('command');
        MIB.clear   = document.getElementById('clear');

        MIB.clear.onclick = function() {
            MIB.debug.innerHTML = '';
        };

        MIB.command.onkeydown = function(event) {
            var value = event.currentTarget.value;

            if (event.keyCode != 13) return;
            if ('' == value.trim()) return;

            event.currentTarget.value = '';

            MIB.socket.send(value);
        }
    }

    if (window.onload) {
        var fnc = window.onload;
        window.onload = function() {
            fnc();
            onLoad();
        };
    } else {
        window.onload = onLoad;
    }
})(MIB = window.MIB || {});