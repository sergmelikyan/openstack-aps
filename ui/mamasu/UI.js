define([
    "dijit/registry",
    "aps/Message",
    "mamasu/Settings.js",
    "mamasu/maximizer.js"
], function (registry, Message, settings, maximizer) {
    var UI = {
        _showMessage: function (message, typeMsg) {
            UI.log("MESSAGE", message);
            var containerId = settings.mainPageId;
            var page = registry.byId(containerId);
            if (page === undefined) {
                containerId = "page";
                page = registry.byId(containerId);
            }
            var messages = page.get("messageList");
            aps.apsc.cancelProcessing();
            messages.removeAll();
            messages.addChild(new Message({
                description: message,
                type: typeMsg
            }));
        },
        showSuccess: function (msg) {
            UI._showMessage(msg, "info");
        },
        showInfo: function (msg) {
            UI._showMessage(msg, "");
        },
        showWarning: function (msg) {
            UI._showMessage(msg, "warning");
        },
        showError: function (error) {
            UI._showMessage(UI.getErrorMsg(error), "error");
        },
        getErrorMsg: function (error) {
            var errMsg = "";
            try {
                var errData = JSON.parse(error.response.text);
                errMsg = errData.message;
            } catch (e) {
                errMsg = error;
            }
            return errMsg;
        },
        log: function () {
            if (settings.environment !== "PROD") {
                console.log("[LOG]", arguments);
            }
        },
        plainLog: function (log) {
            if (settings.environment !== "PROD") {
                console.log(log);
            }
        },
        clearConsole: function () {
            if (settings.environment !== "PROD") {
                console.clear();
            }
        },
        maximizer: maximizer
    };
    return UI;
});