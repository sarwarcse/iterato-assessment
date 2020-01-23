var core=require('./core');
var notification=require('./notification');
var confirmAjax= {
    SetConfirm: function () {
        $("body").on("click", ".Confirm,.confirm", function (e) {
            if($(this).closest(".sa-confirm-button-container").length>0){
                return;
            }
            var msg = $(this).attr('msg');
            if (confirm(msg) == false) {
                e.stopPropagation();
                e.preventDefault();
            }
        });

        $("body").on("click", ".ConfirmAjaxWR,.confirmAjaxWR,.confirmajaxwr", function (e) {
            e.stopPropagation();
            e.preventDefault();
            var msg = $(this).data('msg');
            var $thisObj = $(this);

            var callAfterProcess = $(this).attr('oncompleted');
            if (!callAfterProcess || callAfterProcess == "") {
                callAfterProcess = $(this).data('on-complete');
            }
            var thisobj = $(this);
            var url = thisobj.attr("href");
            if (typeof (url) == "undefined" || url == "") {
                alert("Target url is empty");
                return;
            }
            if (typeof (swal) == "function") {
                if (msg != "") {
                    var yesText = "";
                    var noText = "";
                    if(typeof (appGlobalLang.yesText) !="undefined") {
                        try {
                            yesText = appGlobalLang.yesText;
                            noText = appGlobalLang.noText;
                        } catch (e) {
                            yesText = "Yes";
                            noText = "No";
                        }
                    }else{
                        yesText = "Yes";
                        noText = "No";
                    }
                    swal({
                        title: "",
                        text: msg,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: yesText,
                        cancelButtonText: noText,
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        showLoaderOnConfirm: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            confirmAjax.process_confirm_ajax(thisobj, url, callAfterProcess);
                        }
                    });
                } else {
                    confirmAjax.process_confirm_ajax(thisobj, url, callAfterProcess);
                }
            } else {
                if (msg != "") {
                    if (confirm(msg) == false) {
                        return;
                    }
                }
                confirmAjax.process_confirm_ajax(thisobj, url, callAfterProcess);
            }

        });

    },
    process_confirm_ajax: function (thisobj, url, callAfterProcess) {
        var lastHtml = "";
        $.ajax({
            url: url,
            type: "GET",
            scriptCharset: "utf-8",
            dataType: "json",
            beforeSend: function () {
                //ShowWait();
                lastHtml = thisobj.html();
                thisobj.html('<i class="conf-loader fa fa-spinner fa-spin"></i> ');
            },
            success: function (rdata) {
                try {
                    if (callAfterProcess) {
                        var com = eval(callAfterProcess);
                        if (typeof com == 'function') {
                            setTimeout(function () {
                                com(rdata, thisobj);
                            }, 50);
                            return;
                        }

                    }
                } catch (e) {
                    gcl(e);
                }
                try {
                    if (typeof (swal) == "function") {
                        swal(rdata.status ? "Success" : "Failed", rdata.msg, rdata.status ? "success" : "error");
                    } else {
                        notification.ShowGritterMsg(rdata.msg, rdata.status, rdata.is_sticky, rdata.title, rdata.icon);
                    }

                } catch (e) {}
                if (rdata.status) {
                    APPSBDAPPJS.core.SetAjaxChangeStatus(true);
                    APPSBDAPPJS.core.ReloadAll();
                }


            },
            complete: function (jqXHR, textStatus) {
                thisobj.html(lastHtml);
            }
        });
    },

    SetAjaxForm: function () {
        $("form.apbd-module-form,form.apbd-module-form").each(function () {
            var Ajaxbostrapvalidator = $(this).bootstrapValidator({
                excluded: ':disabled',
                message: 'This value is not valid',
                feedbackIcons: {
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                },
                fields: {
                    cc_exp_date: {
                        validators: {
                            callback: {
                                message: 'Invalid MMYY',
                                callback: function (value, validator) {
                                    var m = new moment(value, 'MMYY', true);
                                    if (!m.isValid()) {
                                        return false;
                                    }
                                    var m2 = moment();
                                    // US independence day is July 4
                                    return m > m2;
                                }
                            }
                        }
                    }
                },
                submitHandler: function (validator, form, submitButton) {
                    var rtype = form.attr("request-type");
                    var htmlBeforeLoading = "";
                    if (!rtype) {
                        rtype = "json";
                    }
                    var isMultiPart = false;
                    if (form.data("multipart")) {
                        try {
                            form.find("input[type=file]").each(function () {
                                if ($(this).val() != "") {
                                    isMultiPart = true;
                                }
                            });
                        } catch (e) {
                            isMultiPart = true;
                        }
                    }
                    if (isMultiPart) {
                        var formData = new FormData(form[0]);
                        formData = APPSBDAPPJS.core.SetCsrfParam(formData);
                        var contentType = false;
                        var processData = false;
                        var async = true;

                    } else {
                        var formData = APPSBDAPPJS.core.SetCsrfParam(form.serialize());
                        var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
                        var processData = true;
                        var async = true;
                    }
                    var method = form.attr("method");
                    $.ajax({
                        type: method,
                        url: form.attr('action'),
                        data: formData,
                        processData: processData,
                        dataType: rtype,
                        contentType: contentType,
                        cache: false,
                        async: async,
                        beforeSend: function () {
                            //	ShowWait();
                            htmlBeforeLoading = form.find("[type=submit]").html();
                            form.find("[type=submit]").html('<i class="fa fa-spinner fa-spin"></i>');
                            form.find("[type=submit]").addClass("Loading");
                            form.find("[type=submit]").attr("disabled", "disabled");
                            form.addClass("form-loader");
                            try {
                                var beforesend = form.data("beforesend");
                                if (beforesend) {
                                    eval(beforesend + "(form);");
                                    return;
                                }
                                beforesend = form.attr("beforesend")
                                if (beforesend) {
                                    eval(beforesend + "(form);");
                                    return;
                                }
                                beforesend = form.data("on-beforesend")
                                if (beforesend) {
                                    eval(beforesend + "(form);");
                                    return;
                                }
                            } catch (e) {

                            }
                        },
                        success: function (rdata) {
                            try {
                                var oncomplete = form.data("oncomplete");
                                if (oncomplete) {
                                    eval(oncomplete + "(rdata,form);");
                                    return;
                                }
                                oncomplete = form.attr("oncomplete")
                                if (oncomplete) {
                                    eval(oncomplete + "(rdata,form);");
                                    return;
                                }
                                oncomplete = form.data("on-complete")
                                if (oncomplete) {
                                    eval(oncomplete + "(rdata,form);");
                                    return;
                                }
                            } catch (e) {

                            }
                            if (rtype != "json") return;
                            //ShowWait(false);

                            if (rdata.status) {
                                APPSBDAPPJS.core.ReloadAll();
                            }
                            notification.ShowGritterMsg(rdata.msg,rdata.status,rdata.isSticky,rdata.title,rdata.icon);
                        },
                        complete: function (jqXHR, textStatus) {
                            form.removeClass("form-loader");
                            form.find("[type=submit]").removeClass("Loading");
                            form.find("[type=submit]").removeAttr("disabled");
                            form.find("[type=submit]").html(htmlBeforeLoading);
                            //console.log(textStatus);
                            if (jqXHR.status == "500" || jqXHR.status == "403" || textStatus == "error") {
                                form.find(".state-loading").removeClass("state-loading");
                                try {
                                    notification.ShowGritterMsg(jqXHR.responseJSON.msg, jqXHR.responseJSON.status, jqXHR.responseJSON.is_sticky, jqXHR.responseJSON.title, jqXHR.responseJSON.icon);
                                } catch (e) {
                                    notification.ShowGritterMsg("Unwanted Error", false, false, "Error !!", "times-circle-o");
                                }
                            }


                        }
                    });
                }
            });
            // Init iCheck elements
            try {
                Ajaxbostrapvalidator.find('.cbox-control').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                })
                // Called when the radios/checkboxes are changed
                    .on('ifChanged', function (e) {
                        // Get the field name
                        try {
                            var field = $(this).attr('name');
                            var fromobj = $(this).closest("form");
                            fromobj
                            // Mark the field as not validated
                                .bootstrapValidator('updateStatus', field, 'NOT_VALIDATED')
                                // Validate field
                                .bootstrapValidator('validateField', field);
                        } catch (e) {
                        }
                    });
            } catch (e) {
            }
        }) ;


    },
    ConfirmWRChange: function (rdata, element) {
        if (typeof (swal) == "function") {
            notification.ShowResponseSwal(rdata);
        } else {
            notification.ShowGritterMsg(rdata.msg, rdata.status, rdata.is_sticky, rdata.title, rdata.icon);
        }
        if (rdata.status) {
            element.html(rdata.data);
        }
    },
    ConfirmWReload: function (rdata, element) {
        if (typeof (swal) == "function") {
            notification.ShowResponseSwal(rdata);
        } else {
            notification.ShowGritterMsg(rdata.msg, rdata.status, rdata.is_sticky, rdata.title, rdata.icon);
        }
        if (rdata.status) {
            core.CallOnCloseLightbox();
        }
    }
}
module.exports=confirmAjax;


