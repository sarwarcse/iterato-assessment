'use strict';
global.onLoadLightbox=[];
global.onAppInit=[];
global.onCloseLightbox=[];
global.onCloseLightboxWithEvent=[];
global.onEventCaller=[];
global.onGridDataLoad=[];
global.onWindowResize=[];
global.onTabActive=[];
global.IsAPBDAjaxChange=false;
global.CurrentAPBDAjaxEvent="";
/*var g=function (fncnames){
    for(var f in fncnames){
        try{
            if(typeof global[fncnames[f]] =="undefined"){
                global[fncnames[f]]=[];
            }
        }catch (e) {}
    }
}(['onLoadLightbox','onAppInit','onCloseLightbox','onGridDataLoad','onWindowResize','onTabActive']);*/
var coreObj= {
    AddOnAppInit: function (func) {
        onAppInit.push(func);
    },
    AddOnLoadLightbox: function (func) {
        onLoadLightbox.push(func);
    },

    AddOnCloseLightbox: function (func) {
        onCloseLightbox.push(func);
    },
    AddOnCloseLightboxWithEvent: function (event,func) {
        if(typeof onCloseLightboxWithEvent[event] =="undefined"){
            onCloseLightboxWithEvent[event]=[];
        }
        onCloseLightboxWithEvent[event].push(func);
    },
    AddOnEvent: function (event,func) {
        if(typeof onEventCaller[event] =="undefined"){
            onEventCaller[event]=[];
        }
        onEventCaller[event].push(func);
    },
    AddOnReload: function (func) {
        onCloseLightbox.push(func);
    },
    AddOnGridDataLoad: function (func) {
        onGridDataLoad.push(func);
    },
    AddOnWindowResize: function (func) {
        onWindowResize.push(func);
    },
    AddOnOnTabActive: function (module_id,func) {
        module_id=module_id.trim();
        if(typeof  onTabActive[module_id] =="undefined"){
            onTabActive[module_id]=[];
        }
        onTabActive[module_id].push(func);
    },
    CallOnTabActive: function (module_id) {
        try {
            for (var i in onTabActive[module_id]) {
                try {
                    onTabActive[module_id][i]();
                } catch (e) {
                }
            }
        } catch (e) {
            console.log(e);
        }
    },
    CallOnInitApp: function (module_id) {
        try {
            for (var i in onAppInit[module_id]) {
                try {
                    console.log("Called"+module_id);
                    onAppInit[module_id][i]();
                } catch (e) {
                }
            }
        } catch (e) {
            console.log(e);
        }
    },
    CallOnWindowResize: function () {
        try {
            for (var i in onWindowResize) {
                try {
                    onWindowResize[i]();
                } catch (e) {
                }
            }
        } catch (e) {
            console.log(e);
        }
    },
    CallOnLoadGridData: function () {
        try {
            for (var i in onGridDataLoad) {
                try {
                    onGridDataLoad[i]();
                } catch (e) {
                }
            }
        } catch (e) {
            console.log(e);
        }
    },
    CallOnLoadLightbox: function () {
        try {
            for (var i in onLoadLightbox) {
                try {
                    onLoadLightbox[i]();
                } catch (e) {
                }
            }
        } catch (e) {
            //console.log(e);
        }
    },
    CallOnReloadWithEvent:function(event){
        try {
            if((typeof onCloseLightboxWithEvent[event] !="undefined") && onCloseLightboxWithEvent[event].length>0) {
                for (var i in onCloseLightboxWithEvent[event]) {
                    try {
                        onCloseLightboxWithEvent[event][i]();
                    } catch (e) {
                    }
                }
            }
        } catch (e) {
            //console.log(e);
        }
    },
    CallOnEvent:function(event){
        var args = [];
        try {
            for (var i = 1; i < arguments.length; i++) {
                args.push(arguments[i]);
            }
        }catch (e) {}

        try {
            if((typeof onEventCaller[event] !="undefined") && onEventCaller[event].length>0) {
                for (var i in onEventCaller[event]) {
                    try {
                        onEventCaller[event][i].apply(this,args);
                    } catch (e) {
                    }
                }
            }
        } catch (e) {
            //console.log(e);
        }
    },
    CallOnCloseLightbox: function () {
        var thisObj = null;
        try {
            if (typeof (this._lastFocusedEl) != "undefined") {
                thisObj = $(this._lastFocusedEl);
            } else if (typeof ($(this)[0].ev) != "undefined") {
                thisObj = $(this)[0].ev;
            } else {
                thisObj = $(this);
            }
        } catch (e) {
            thisObj = $(this);
        }

        var onclosemainevent = thisObj.attr('onclose');
        if (onclosemainevent) {
            eval(onclosemainevent + "()");
            return;
        }

        var onclosemainevent2 = thisObj.data('onclose');
        if (onclosemainevent2) {
            eval(onclosemainevent2 + "()");
            return;
        }
        if (coreObj.IsAjaxChange()) {
            try {
                for (var i in onCloseLightbox) {
                    try {
                        onCloseLightbox[i]();
                    } catch (e) {
                    }
                }
            } catch (e) {
                console.log(e);
            }
            try {
                if(global.CurrentAPBDAjaxEvent!="" && (typeof onCloseLightboxWithEvent[global.CurrentAPBDAjaxEvent] !="undefined") && onCloseLightboxWithEvent[global.CurrentAPBDAjaxEvent].length>0) {
                    for (var i in onCloseLightboxWithEvent[global.CurrentAPBDAjaxEvent]) {
                        try {
                            onCloseLightboxWithEvent[global.CurrentAPBDAjaxEvent][i]();
                        } catch (e) {
                        }
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }else{}


    },

    SetCsrfParam: function (param) {
        try {
            var postValue = coreObj.GetCookie(csrf_ajax_cookie_name);
            if (postValue && postValue != "") {
                if (typeof param == "string") {
                    if (param != "") {
                        param += "&";
                    }
                    param += csrf_ajax_input_name + "=" + postValue;
                } else if (typeof param == "object") {
                    try {
                        if (typeof param.append === 'function') {
                            param.append(csrf_ajax_input_name, postValue);
                        } else {
                            if (param.length == 0) {
                                param[csrf_ajax_input_name] = postValue;
                            } else {
                                param[csrf_ajax_input_name] = postValue;
                            }
                        }
                    } catch (e) {
                    }

                }
            }
            return param;
        }catch(e){
            return param;
        }

    },
    ReloadAll: function () {
        coreObj.CallOnCloseLightbox();
    },
    ReloadSiteUrl: function () {
        window.location = window.location.href;
    },
    RedirectUrl: function (url) {
        window.location = url;
    },
    CallMyAjax: function (url, data, beforeSend, Success, JSONData, Complete) {
        if (!beforeSend) {
            beforeSend = function () {
            }
        }
        if (!Success) {
            Success = function () {
            }
        }
        if (typeof (JSONData) == "undefined") {
            JSONData = true;
        }
        $.ajax({
            url: url,
            data: APPSBDAPPJS.core.SetCsrfParam(data),
            type: "POST",
            scriptCharset: "utf-8",
            dataType: JSONData ? "json" : "html",
            beforeSend: function () {
                beforeSend();
            },
            success: function (rdata) {
                Success(rdata);
            },
            complete: function (jqXHR, textStatus) {
                if (typeof (Complete) != "undefined") {
                    Complete(jqXHR, textStatus);
                }
                if (textStatus == "error") {
                    if (jqXHR.status == "404") {
                        console.log("Error: Page does not found");
                    } else if (jqXHR.status == "408") {
                        console.log("Error: Sarver does not active.");
                    } else {
                        console.log("Error: May be connection lost.");
                    }
                }
            }
        });
    },
    SetFullScreen: function () {
        try {
            $(".full-screen").prepend('<a type="button" href="#" class="full-screen-btn btn-xs btn"><i class="fa"></i></a>');
            $("body").on("click", ".full-screen-btn", function (e) {
                e.preventDefault();
                $("body").toggleClass("full-screen-body");

            });
        } catch (e) {
            gcl(e);
        }
    },
    GetTimeSpendDate: function (date1, date2) {
        var diff = Math.floor(date1.getTime() - date2.getTime());
        var secs = Math.floor(diff / 1000);
        var mins = Math.floor(secs / 60);
        var hours = Math.floor(mins / 60);
        var days = Math.floor(hours / 24);
        var months = Math.floor(days / 31);
        var years = Math.floor(months / 12);
        months = Math.floor(months % 12);
        days = Math.floor(days % 31);
        hours = Math.floor(hours % 24);
        mins = Math.floor(mins % 60);
        secs = Math.floor(secs % 60);
        var message = "";
        if (days <= 0) {
            message += secs + " sec ";
            message += mins + " min ";
            message += hours + " hours ";
        } else {
            message += days + " days ";
            if (months > 0 || years > 0) {
                message += months + " months ";
            }
            if (years > 0) {
                message += years + " years ago";
            }
        }
        return message
    },
    GetCookie: function (w) {
        var cName = "";
        var pCOOKIES = [];
        pCOOKIES = document.cookie.split('; ');
        for (var bb = 0; bb < pCOOKIES.length; bb++) {
            var NmeVal = [];
            NmeVal = pCOOKIES[bb].split('=');
            if (NmeVal[0] == w) {
                cName = unescape(NmeVal[1]);
            }
        }
        return cName;
    },
    PrintCookies: function printCookies(w) {
        var cStr = "";
        var pCOOKIES = [];
        var pCOOKIES = document.cookie.split('; ');
        for (var bb = 0; bb < pCOOKIES.length; bb++) {
            var NmeVal = [];
            NmeVal = pCOOKIES[bb].split('=');
            if (NmeVal[0]) {
                cStr += NmeVal[0] + '=' + unescape(NmeVal[1]) + '; ';
            }
        }
        return cStr;
    },
    DeleteCookie: function (name) {
        try {
            document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT';
        } catch (e) {
        }
    },
    SetCookie: function (name, value, expires, path, domain, secure) {
        var cookieStr = name + "=" + escape(value) + "; ";

        if (expires) {
            expires = coreObj.SetExpiration(expires);
            cookieStr += "expires=" + expires + "; ";
        }
        if (path) {
            cookieStr += "path=" + path + "; ";
        }
        if (domain) {
            cookieStr += "domain=" + domain + "; ";
        }
        if (secure) {
            cookieStr += "secure; ";
        }

        document.cookie = cookieStr;
    },
    SetExpiration: function (cookieLife) {
        var today = new Date();
        var expr = new Date(today.getTime() + cookieLife * 24 * 60 * 60 * 1000);
        return expr.toGMTString();
    },
    SetPopover:function(){
        try {
            $('[data-toggle="popover"]:not(".poad"),.app-popover:not(".poad")').each(function(){
                $(this).addClass('poad');
                var hasElem=$(this).data('element');

                var parent= $(this).closest("#LightBoxBody");
                var containter="#APPSBDWP";
                if(parent.length>0){
                    containter="#LightBoxBody";
                }
                if(hasElem){
                    $(this).popover({
                        container: containter,
                        content:$(hasElem),
                        html:true
                    });
                }else {
                    $(this).popover({
                        container: containter
                    });
                }
            });
        }catch (e) {}
    },
    SetNoticeLink:function(){
        try {
            $("body").on("click",'.apd-notice-tab-link',function(e){
                e.preventDefault();
               var target=$(this).attr("href");
               console.log(target);;
               console.log($(target));;
               $(target).click();
            });
        }catch (e) {}
    },
    SetTooltip:function(){
        try {
           $('[data-toggle="tooltip"]:not(".ttad"),.app-tooltip:not(".ttad")').each(function(){
               $(this).addClass('ttad');
                var parent= $(this).closest("#LightBoxBody");
                var containter="#APPSBDWP";
                if(parent.length>0){
                    containter="#LightBoxBody";
                }
               $(this).tooltip({container: containter, boundary: 'window',trigger:"hover" })
            });
        }catch (e) {}
    },
    SetAjaxChangeStatus:function (status) {
        global.IsAPBDAjaxChange=status;
    },
    SetAjaxChangeEvent:function (evt) {
        global.CurrentAPBDAjaxEvent=evt;
    },
    IsAjaxChange:function () {
        return global.IsAPBDAjaxChange;
    },
    SetTabView:function () {
        try {
            $('.app-tab-viewer:not(.atv-added)').each(function(){
                $(this).addClass("atv-added");
                $(this).on("click",function (e) {
                    e.preventDefault();
                    var target=$(this).attr("href");

                    if(target.startsWith("#")){
                       var module_id=target.replace('#', '');
                       var menu= $('a.nav-link[data-module-id='+module_id+']');
                       if(menu.length>0){
                           menu.click();
                       }else {
                           try {
                               $(target).closest(".tab-content").find(".tab-pane").removeClass("active").removeClass("show");
                               $(target).addClass("active show");
                               APPSBDAPPJS.core.CallOnTabActive(target.replace('#', ''));
                           }catch(e){}
                       }

                    }
                });

            });
        }catch (e) {  }
    },
    SetLazyLoader:function () {
        try {
            $('.apbd-lazy-loader:not(.alzl-added)').each(function(){
                $(this).addClass("alzl-added");
                $(this).on("click",function (e) {
                    var isCalled=$(this).data("app-loaded");
                    if(isCalled!="Y") {
                        var odload = $(this).data("onclick");
                        if (odload) {
                            $(this).data("app-loaded","Y");
                            eval(odload + "()");
                        }
                    }
                });

            });
        }catch (e) {  }
    },
    SetBVForm:function(){
        try {
            $('.apbd-bv-form:not(.app-lb-ajax-form)').each(function() {
                var submitHandler = $(this).data("submit-handler");
                if (submitHandler) {
                    submitHandler = eval(submitHandler);
                }
                $(this).bootstrapValidator({
                    submitHandler: submitHandler,
                    excluded: ':disabled,:hidden:not(.force-bv)',
                    message: 'This value is not valid',
                    feedbackIcons: {
                        valid: 'fa fa-check',
                        invalid: 'fa fa-times',
                        validating: 'fa fa-refresh'
                    }
                });


            });
        } catch (e) {}
    }
};
module.exports=coreObj;