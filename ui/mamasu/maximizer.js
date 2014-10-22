define(["//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js", "mamasu/Settings.js"], function (jquery, globalSettings) {
    var settings = {
        maxOnStart: globalSettings.maximizeOnStart || false,
        available: globalSettings.maximizer || false
    };

    function maximize($button) {
        var oldrows = $(window.parent.parent.master).attr('oldrows') || $(window.parent.parent.master).attr('rows');
        var oldcols = $(window.parent.parent.pemFrames).attr('oldcols') || $(window.parent.parent.pemFrames).attr('cols');

        $(window.parent.parent.master).attr("oldrows", oldrows);
        $(window.parent.parent.master).attr("rows", "0,*");
        $(window.parent.parent.pemFrames).attr("oldcols", oldcols);
        $(window.parent.parent.pemFrames).attr("cols", "0,*");
        $(window.parent.document.body).find(".tabs").css({display: "none"});
        $(window.parent.document.body).find('#pathbar').css({display: "none"});

        $button.data("maximized", "yes");
        $button.css({backgroundImage: "url(/pem/images/icons/i_applications.gif)"});

        $(window.parent.parent).resize(function () {
            maximize($button)
        });
    }

    function minimize($button) {
        console.log("minimize", settings);

        var oldrows = $(window.parent.parent.master).attr('oldrows');
        var oldcols = $(window.parent.parent.pemFrames).attr('oldcols');


        $(window.parent.parent.master).attr("rows", oldrows);
        $(window.parent.parent.pemFrames).attr("cols", oldcols);
        $(window.parent.document.body).find(".tabs").css({display: "block"});
        $(window.parent.document.body).find('#pathbar').css({display: "block"});

        $button.data("maximized", "no");
        $button.css({backgroundImage: "url(/pem/images/icons/i_applications2.gif)"});

        $(window.parent.parent).resize(function () {
            minimize($button);
        });
    }
    
    if (settings.available) {
        var $button = $("<div class='btn apsButton' id='maximizeAPSFrame' data-maximized='no'></div>");
        $button.click(function () {
            console.log("Maximized", $(this).data("maximized"));
            if ($(this).data("maximized") === "no") {
                maximize($(this));
            } else {
                minimize($(this));
            }
        });
        $button.css({backgroundImage: "url(/pem/images/icons/i_applications2.gif)", height: 16, width: 16, position: "relative", float: "right", marginLeft: "9px"});
        $button.find("img").css({display: "none"});

        var $toolbar = $(window.parent.document.body).find("#toolbar-content");
        $toolbar.append($button);

        if (settings.maxOnStart) {
            maximize($button);
        }
    }
});