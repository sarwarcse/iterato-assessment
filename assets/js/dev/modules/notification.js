var notification= {
    ShowGritterMsg: function (msg, IsSuccess, IsSticky, title, icon, timeouttime) {
        try {
            if (typeof (IsSuccess) == 'undefined') {
                IsSuccess = true;
            }

            if (typeof (IsSticky) == 'undefined') {
                IsSticky = false;
            }
            if (typeof (title) == 'undefined' || !title) {
                title = "Notification";
            }
            if (typeof (icon) == 'undefined') {
                icon = "";
            }
            if (typeof (timeouttime) == 'undefined') {
                timeouttime = 5000;
            }
            if(!msg){
                msg="Message doesn't set";
            }
            try {
                var options = {
                    title: title,
                    style: IsSuccess ? 'success' : 'error',
                    theme: 'right-bottom.css',
                    timeout: timeouttime,
                    message: msg,
                    icon: icon
                };
                if (IsSticky) {
                    options.timeout = null;
                }
                var n = new notify(options);
                n.show();
            } catch (e) {
                console.log(e);
                try {
                    $.gritter.add({
                        position: 'bottom-left',
                        // (string | mandatory) the heading of the notification
                        //title: 'This is a regular notice!',
                        // (string | mandatory) the text inside the notification
                        text: msg,
                        /*// (string | optional) the image to display on the left*/
                        image: IsSuccess ? base_url + 'images/statusOk.png' : base_url + 'images/statuserror.png',

                        // (bool | optional) if you want it to fade out on its own or just sit there
                        sticky: IsSticky,
                        // (int | optional) the time you want it to be alive for before fading out
                        time: 5000
                    });
                }catch (e) {
                    alert(e.message);
                }
            }
        } catch (e) {
            console.log(e);
        }
    },
    ShowWaitinglight: function (isShow,callback,msg) {
        if (typeof (isShow) == "undefined") {
            isShow = true;
        }
        if (typeof (callback) == "undefined") {
            callback = function(){}
        }
        var defaultData=$(".lightboxWraper #waiting h4").data("default-msg");
        if (typeof (msg) != "undefined") {
            $(".lightboxWraper #waiting h4").text(msg);
        }else{
            if(defaultData) {
                $(".lightboxWraper #waiting h4").text(defaultData);
            }
        }
        if (isShow) {
            //$("#waiting").fadeIn();
            $(".lightboxWraper").fadeIn('fast',callback);
        } else {
            //$("#waiting").fadeOut();
            $(".lightboxWraper").fadeOut('fast',callback);
        }
    },
    ShowAppLoader: function (isShow,callback,msg) {
        if (typeof (isShow) == "undefined") {
            isShow = true;
        }
        if (typeof (callback) == "undefined") {
            callback = function(){}
        }
        var defaultData=$("#apbd-app-loader #apbd-app-waiting h4").data("default-msg");
        if (typeof (msg) != "undefined") {
            $("#apbd-app-loader #apbd-app-waiting h4").text(msg);
        }else{
            if(defaultData) {
                $("#apbd-app-loader #apbd-app-waiting h4").text(defaultData);
            }
        }
        if (isShow) {
            //$("#waiting").fadeIn();
            $("#apbd-app-loader").fadeIn('fast',callback);
        } else {
            //$("#waiting").fadeOut();
            $("#apbd-app-loader").fadeOut('fast',callback);
        }
    },
    ShowSwal:function(rdata){
        if (typeof (swal) == "function") {
            swal({
                title: "",
                text:  rdata.msg,
                type: (rdata.status ? "success" : "error"),
                showCancelButton: false,
                confirmButtonClass: "btn-success",
                confirmButtonText: appGlobalLang.okText,
                closeOnConfirm: false,
                closeOnCancel: false
            });

        } else {
            notification.ShowGritterMsg(rdata.msg, rdata.status, rdata.is_sticky, rdata.title, rdata.icon);
        }
    },
    ShowResponseSwal:function(rdata){
        notification.ShowSwal(rdata);
    }
}
module.exports=notification;


