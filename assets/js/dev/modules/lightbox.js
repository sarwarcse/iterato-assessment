'use strict';
var notification=require('./notification');
function onLightboxOpen(){
    if(!$(".mfp-content").hasClass('container')){
        $(".mfp-content").addClass("container");
    }
}
function beforeOpen(){
    $("body").addClass("apd-lg-showing");
}
function onCloseLGOpen(){
    $("body").removeClass("apd-lg-showing");
}
function getLoadingLanguage(){
    try {
        var loadingText = appGlobalLang.Loading;
    } catch (e) {
        var loadingText = "Loading";
    }
    return loadingText+'..';
}

var setPopUpAjax= {
    AppLightboxValidatorObject: null,
    SetLightbox: function () {
        // SetTable();
        // SetValidation();
        try {
            if ($.magnificPopup.instance) {
                $.magnificPopup.instance.popupsCache = {};
            }
            $(".popupform,.Popupform,.popupformWR,.PopupformWR,.popupimg,.Popupimg,.popupinline,.Popupinline,.PopupInline").each(function () {
                var effect = $(this).data("effect");
                if (!effect) {
                    $(this).attr("data-effect", "mfp-move-from-top");
                    $(this).data("effect", "mfp-move-from-top");
                }
            });
            $(".popupform:not(.apopf),.Popupform:not(.apopf)").magnificPopup({
                type: 'ajax',
                preloader: true,
                removalDelay: 500,
                closeOnBgClick: false,
                closeBtnInside: true,
                overflowY: 'auto',
                fixedBgPos: false,
                zoom: {enabled: false},
                tLoading: '<i class="fa fa-circle-o faa-burst animated"></i> &nbsp;'+getLoadingLanguage(),
                callbacks: {
                    beforeOpen: function () {
                        beforeOpen();
                        this.st.mainClass = this.st.el.attr('data-effect');
                    },
                    open: function () {
                        onLightboxOpen();
                    },
                    close: function () {
                        onCloseLGOpen();
                    },
                    updateStatus: function (data) {
                        if (data.status === 'ready') {
                            APPSBDAPPJS.core.CallOnLoadLightbox();
                        }
                    }
                }
            });
            $(".popupimg:not(.apopf),.Popupimg:not(.apopf)").magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                mainClass: 'mfp-img-mobile',
                callbacks: {
                    resize: function () {
                        var img = this.content.find('img');
                        img.css('max-height', $(window).height() - 50);
                        img.css('width', 'auto');
                        img.css('max-width', 'auto');
                    }
                    ,
                    elementParse: function (qw) {
                        try {
                            if (qw.el.context.tagName.toUpperCase() == "IMG") {
                                qw.src = qw.el.attr('src');
                            }
                        } catch (e) {
                            try {
                                if (qw.el[0].nodeName == "IMG") {
                                    qw.src = qw.el[0].src;
                                }
                            } catch (eg) {
                            }
                        }
                    }
                }
            });
            $(".popupinline:not(.apopf),.Popupinline:not(.apopf),.PopupInline:not(.apopf)").magnificPopup({
                type: 'inline',
                preloader: true,
                removalDelay: 500,
                closeBtnInside: true,
                overflowY: 'auto',
                closeOnBgClick: false,
                fixedBgPos: false,
                zoom: {enabled: false},
                tLoading: '<i class="fa fa-circle-o faa-burst animated"></i> &nbsp;'+getLoadingLanguage(),
                callbacks: {
                    beforeOpen: function () {
                        beforeOpen();
                        this.st.mainClass = this.st.el.attr('data-effect');
                        try {
                            $(this.st.el.attr('href')).addClass("mfp-with-anim");
                        } catch (e) {
                        }
                    },
                    open: function () {
                        onLightboxOpen();
                    },
                    close: function () {
                        onCloseLGOpen();
                    },
                    updateStatus: function (data) {
                        if (data.status === 'ready') {
                            APPSBDAPPJS.core.CallOnLoadLightbox();
                        }
                    }
                }
            });


        } catch (e) {
            gcl(e);
        }

        try {
            $(".popupformWR:not(.apopf),.PopupformWR:not(.apopf)").magnificPopup({
                type: 'ajax',
                preloader: true,
                removalDelay: 500,
                closeBtnInside: true,
                overflowY: 'auto',
                closeOnBgClick: false,
                fixedBgPos: false,
                zoom: {enabled: false},
                tLoading: '<i class="fa fa-circle-o faa-burst animated"></i> &nbsp;'+getLoadingLanguage(),
                callbacks: {
                    beforeOpen: function () {
                        beforeOpen();
                        this.st.mainClass = this.st.el.attr('data-effect');
                    },
                    open: function () {
                        onLightboxOpen();
                    },
                    close: function () {
                        onCloseLGOpen();
                        APPSBDAPPJS.core.CallOnCloseLightbox();
                    },
                    updateStatus: function (data) {
                        if (data.status === 'ready') {
                            APPSBDAPPJS.core.CallOnLoadLightbox();
                        }
                    }
                }
            });
        } catch (e) {
            gcl(e);
        }

        try {
            $(".popupformIF:not(.apopf),.PopupformIF:not(.apopf)").magnificPopup({
                type: 'iframe',
                preloader: true,
                removalDelay: 500,
                closeBtnInside: true,
                overflowY: 'auto',
                closeOnBgClick: false,
                fixedBgPos: false,
                zoom: {enabled: false},
                tLoading: '<i class="fa fa-circle-o faa-burst animated"></i> &nbsp;'+getLoadingLanguage(),
                callbacks: {
                    beforeOpen: function () {
                        beforeOpen();
                        this.st.mainClass = this.st.el.attr('data-effect');
                    },
                    open: function () {
                        onLightboxOpen();
                    },
                    close: function () {
                        onCloseLGOpen();
                    },
                    updateStatus: function (data) {
                        if (data.status === 'ready') {
                            APPSBDAPPJS.core.CallOnLoadLightbox();
                        }
                    }
                }
            });
        } catch (e) {
            gcl(e);
        }
        try {
            $(".popupformWIF:not(.apopf),.PopupformWIF:not(.apopf)").magnificPopup({
                type: 'iframe',
                preloader: true,
                removalDelay: 500,
                closeBtnInside: true,
                overflowY: 'auto',
                closeOnBgClick: false,
                fixedBgPos: false,
                zoom: {enabled: false},
                tLoading: '<i class="fa fa-circle-o faa-burst animated"></i> &nbsp;'+getLoadingLanguage(),
                callbacks: {
                    beforeOpen: function () {
                        beforeOpen();
                        this.st.mainClass = this.st.el.attr('data-effect');
                    },
                    open: function () {
                        onLightboxOpen();
                    },
                    close: function () {
                        onCloseLGOpen();
                        APPSBDAPPJS.core.CallOnCloseLightbox()
                    },
                    updateStatus: function (data) {
                        if (data.status === 'ready') {
                            APPSBDAPPJS.core.CallOnLoadLightbox();
                        }
                    }
                }
            });
        } catch (e) {
            gcl(e);
        }
        $(".popupform:not(.apopf),.Popupform:not(.apopf),.popupformWR:not(.apopf),.PopupformWR:not(.apopf),.popupimg:not(.apopf),.Popupimg:not(.apopf),.popupinline:not(.apopf),.Popupinline:not(.apopf),.PopupInline:not(.apopf)").addClass("apopf");

    },
    ShowPopupForm: function (url, isIframe) {
        if (typeof isIframe == "undefined") {
            isIframe = false;
        }
        var obj = $('<a data-effect="mfp-move-from-top">').attr("href", url);
        obj.magnificPopup({
            type: 'ajax',
            preloader: true,
            removalDelay: 500,
            closeBtnInside: true,
            overflowY: 'auto',
            closeOnBgClick: false,
            fixedBgPos: false,
            zoom: {enabled: false},
            tLoading: '<i class="fa fa-circle-o faa-burst animated"></i> &nbsp;'+getLoadingLanguage(),
            callbacks: {
                beforeOpen: function () {
                    beforeOpen();
                    this.st.mainClass = this.st.el.attr('data-effect');
                },
                open: function () {
                },
                close: onCloseLGOpen,
                updateStatus: function (data) {
                    if (data.status === 'ready') {
                        onLightboxOpen();
                    }
                }
            }
        }).click();
    },
    SetLightBoxAjax: function () {
        try {
            var Ajaxbostrapvalidator = $("form.app-lb-ajax-form").bootstrapValidator({
                excluded: ':disabled,:hidden',
                message: 'This value is not valid',
                feedbackIcons: {
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                },
                onChangeStatus: function (validator, form) {

                },
                submitHandler: function (validator, form, submitButton) {
                    if (form.data("multipart")) {
                        $(".lightboxWraper").fadeIn();
                        var formData = new FormData(form[0]);
                        formData = APPSBDAPPJS.core.SetCsrfParam(formData);
                        var contentType = false;
                        var processData = false;
                        var async = false;
                        notification.ShowWaitinglight(true);
                    } else {
                        var formData = APPSBDAPPJS.core.SetCsrfParam(form.serialize());
                        var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
                        var processData = true;
                        var async = true;
                    }
                    $.ajax({
                        type: "POST",
                        url: form.attr('action'),
                        data: formData,
                        processData: processData,
                        contentType: contentType,
                        cache: false,
                        async: async,
                        beforeSend: function () {
                            notification.ShowWaitinglight(true);
                        },
                        success: function (data) {
                            notification.ShowWaitinglight(false,function(){
                                var rData = $('<div/>');
                                rData.html(data);
                                var LightboxB = rData.find('#LightBoxBody');
                                $("#popup-container").attr("class", rData.find('#popup-container').attr("class"));
                                $('#LightBoxBody').html(LightboxB.html());
                                APPSBDAPPJS.core.CallOnLoadLightbox();
                            });
                        },
                        complete: function () {
                            setTimeout(function(){notification.ShowWaitinglight(false);},2000);
                        }
                    });
                }
            });
            Ajaxbostrapvalidator.addClass("Addesdd");
            try {
                // Init iCheck elements
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
            setPopUpAjax.AppLightboxValidatorObject = Ajaxbostrapvalidator;
        } catch (e) {
            console.log(e);
        }

    },
    CloseLightBox:function(){
        try {
            $.magnificPopup.instance.close();
        } catch (e) {}
    }
}
jQuery(document).ready(function ($) {
    $("body").on("click", ".close-pop-up", function (e) {
        setPopUpAjax.CloseLightBox();
    });
});
module.exports=setPopUpAjax;