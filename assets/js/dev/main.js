'use strict';

global.jQuery = global.$ = jQuery;
window.gcl=function (obj,$isShow) {
    console.log(obj);
};
window.APPSBDAPPJS={
    core : require("./modules/core"),
    sidemenu : require("./modules/menu"),
    lightbox : require("./modules/lightbox"),
    confirmAjax : require("./modules/confirm_ajax"),
    notification : require("./modules/notification"),
    InputPicker : require("./modules/inputpicker"),
    datetimepicker : require("./modules/datatimepicker"),
    dependable : require("./modules/dependable_input"),
    treeview : require("./modules/tree_view"),

    Initialize:function () {
        APPSBDAPPJS.lightbox.SetLightbox();
        APPSBDAPPJS.lightbox.SetLightBoxAjax();
        APPSBDAPPJS.confirmAjax.SetConfirm();
        APPSBDAPPJS.confirmAjax.SetAjaxForm();
        APPSBDAPPJS.InputPicker.SetColorPicker();
        APPSBDAPPJS.InputPicker.SetSelectPicker();
        APPSBDAPPJS.datetimepicker.SetDateTimePicker();
        APPSBDAPPJS.dependable.SetDependable();
        APPSBDAPPJS.core.SetPopover();
        APPSBDAPPJS.core.SetTooltip();
        APPSBDAPPJS.core.SetNoticeLink();
        APPSBDAPPJS.core.SetTabView();
        APPSBDAPPJS.core.SetLazyLoader();
        APPSBDAPPJS.core.SetBVForm();



    }
};
window.AddOnCloseMethod=APPSBDAPPJS.core.AddOnLoadLightbox;
//ready function
jQuery(document).ready(function ($) {
    //initilize
    APPSBDAPPJS.core.CallOnInitApp();
    $(window).on("resize",function (e) {
        APPSBDAPPJS.core.CallOnWindowResize();
    });
    try {
        var hashlink=window.location.hash.replace("#","").split(",");
        for (var hi in hashlink) {
            $("#"+hashlink[hi]).click();
            console.log("called : #"+hashlink[hi]);
        }
    } catch (e) {
    }
    APPSBDAPPJS.sidemenu.SetMenuSidebar();
    APPSBDAPPJS.Initialize();
    //add on lighboxload
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.lightbox.SetLightbox);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.lightbox.SetLightBoxAjax);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.confirmAjax.SetConfirm);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.InputPicker.SetColorPicker);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.InputPicker.SetSelectPicker);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.datetimepicker.SetDateTimePicker);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.dependable.SetDependable);
    //APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.core.SetPopover);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.core.SetTooltip);
    //APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.WPEditor.Init);
    APPSBDAPPJS.core.AddOnLoadLightbox(APPSBDAPPJS.confirmAjax.SetAjaxForm);
    APPSBDAPPJS.treeview.init();
});

