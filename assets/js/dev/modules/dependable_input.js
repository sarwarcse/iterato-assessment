'use strict';
var dependable= {
    SetDependable: function () {
        var in_added_event_list = [];
        jQuery(".has_depend_fld:not(.added-dpnds)").each(function () {
            var thisObj = jQuery(this);
            var inputtype = thisObj.attr("type");
            var class_prefix = thisObj.data("class-prefix");
            var name = thisObj.attr("name");
            if (!class_prefix) {
                class_prefix = "fld-" + name.replace(/[\[\]\_]/g, "-").replace(/\-$/g, "");
            }
            thisObj.addClass("added-dpnds");
            //console.log(class_prefix);//<"fld">-<field_name>
            if (in_added_event_list.indexOf("[name=" + name + "]") == -1) {
                in_added_event_list.push("[name=" + name + "]");
                jQuery("[name='" + name + "']").on("change", function (e) {
                    dependable.setDependableSettings(thisObj, class_prefix);
                });
                dependable.setDependableSettings(thisObj, class_prefix);
            }
        });
        jQuery(".has_depend_fld2:not(.added-dpnds)").each(function () {
            var thisObj = jQuery(this);
            var inputtype = thisObj.attr("type");
            var class_prefix = thisObj.data("class-prefix");
            var name = thisObj.attr("name");
            if (!class_prefix) {
                class_prefix = "fld-" + name.replace(/[\[\]\_]/g, "-").replace(/\-$/g, "");
            }
            thisObj.addClass("added-dpnds");
            //console.log(class_prefix);//<"fld">-<field_name>
            if (in_added_event_list.indexOf("[name=" + name + "]") == -1) {
                in_added_event_list.push("[name=" + name + "]");
                jQuery("[name='" + name + "']").on("change", function (e) {
                    dependable.setDependableSettings2(thisObj, class_prefix);
                });
                dependable.setDependableSettings2(thisObj, class_prefix, true);
            }
        });
    },
    setDependableSettings: function (elem, class_prefix) {

        try {
            var name = elem.attr("name");
            var type = elem.attr("type");
            if (type == "checkbox" || type == "radio") {
                var selectedAction = jQuery("[name='" + name + "']:checked").val();
                if (selectedAction == undefined) {
                    selectedAction = jQuery("[name='" + name + "'][type=hidden]").val();
                }
            } else {
                var selectedAction = jQuery("[name='" + name + "']").val();
            }
            if (selectedAction) {
                selectedAction = selectedAction.toLowerCase();
                //console.log(selectedAction);
                //console.log("." + class_prefix);//fld-config-is-enable_paypal
                var hiddenFlields =jQuery("." + class_prefix + ":not(." + class_prefix + "-" + selectedAction + ")");
                if (hiddenFlields.length > 0) {
                    hiddenFlields.fadeOut('fast', function () {
                        hiddenFlields.find("input,select,textarea").prop("disabled", true);
                        dependable.showDependableSettings(class_prefix, selectedAction);
                    });
                } else {
                    dependable.showDependableSettings(class_prefix, selectedAction);
                }
                elem.closest("form").find("[type=submit]").prop("disabled", false);
            }
        } catch (e) {
            // gcl(e.message);
        }
    },
    setDependableSettings2: function (elem, class_prefix, is_first_load) {
        try {
            var name = elem.attr("name");
            var type = elem.attr("type");
            if (type == "checkbox" || type == "radio") {
                var selectedAction = jQuery("[name='" + name + "']:checked").val();
                if (selectedAction == undefined) {
                    selectedAction = jQuery("[name='" + name + "'][type=hidden]").val();
                }
            } else {
                var selectedAction = jQuery("[name='" + name + "']").val();
            }
            if (selectedAction) {
                selectedAction = selectedAction.toLowerCase();
                var hiddenFlields = jQuery("." + class_prefix + ":not(." + class_prefix + "-" + selectedAction + ")");
                try{ elem.closest("form").find("[type=submit]").prop("disable",false);  }catch (e) { }
                if (hiddenFlields.length > 0) {
                    hiddenFlields.prop("disabled", true);
                    var elems=hiddenFlields.find("input,select,textarea");
                    try {
                        var bvform=  elem.closest('form').data('bootstrapValidator');
                        if(elems.length>0) {elems.each(function () {  bvform.updateStatus($(this).attr("name"), 'NOT_VALIDATED');  });}
                        try{ bvform.updateStatus(hiddenFlields.attr("name"), 'NOT_VALIDATED');}catch (e) {}
                    }catch (e) {}
                    elems.prop("disabled", true);
                    dependable.showDependableSettings2(class_prefix, selectedAction, is_first_load);
                } else {
                    dependable.showDependableSettings2(class_prefix, selectedAction, is_first_load);
                }
                elem.closest("form").find("[type=submit]").prop("disabled", false);
            }
        } catch (e) {
             gcl(e.message);
        }
    },
    showDependableSettings: function (class_prefix, selectedAction) {
        //gcl("." + class_prefix + "-" + selectedAction);
        var activeFlields = jQuery("." + class_prefix + "-" + selectedAction).removeClass("hidden");
        activeFlields.fadeIn();
        activeFlields.find("input,select,textarea").prop("disabled", false);
    },
    showDependableSettings2: function (class_prefix, selectedAction, is_first_load) {
        var activeFlields = jQuery("." + class_prefix + "-" + selectedAction);

        var acFlds = activeFlields.prop("disabled", false).find("input,select,textarea").prop("disabled", false);
        if (!is_first_load) {
            if(activeFlields.is("input") || activeFlields.is("select")){
                activeFlields.focus();
            }else {
                acFlds.first().focus();
            }
        }
    }
}
var main_object= {
    SetDependable: function () {
        var in_added_event_list = [];
        jQuery(".has_depend_fld:not(.added-dpnds)").each(function () {
            var thisObj = jQuery(this);
            var inputtype = thisObj.attr("type");
            var class_prefix = thisObj.data("class-prefix");
            var name = thisObj.attr("name");
            if (!class_prefix) {
                class_prefix = "fld-" + name.replace(/[\[\]\_]/g, "-").replace(/\-$/g, "");
            }
            thisObj.addClass("added-dpnds");
            //console.log(class_prefix);//<"fld">-<field_name>
            if (in_added_event_list.indexOf("[name=" + name + "]") == -1) {
                in_added_event_list.push("[name=" + name + "]");
                jQuery("[name='" + name + "']").on("change", function (e) {
                    dependable.setDependableSettings(thisObj, class_prefix);
                });
                dependable.setDependableSettings(thisObj, class_prefix);
            }
        });
        jQuery(".has_depend_fld2:not(.added-dpnds)").each(function () {
            var thisObj = jQuery(this);
            var inputtype = thisObj.attr("type");
            var class_prefix = thisObj.data("class-prefix");
            var name = thisObj.attr("name");
            if (!class_prefix) {
                class_prefix = "fld-" + name.replace(/[\[\]\_]/g, "-").replace(/\-$/g, "");
            }
            thisObj.addClass("added-dpnds");
            //console.log(class_prefix);//<"fld">-<field_name>
            if (in_added_event_list.indexOf("[name=" + name + "]") == -1) {
                in_added_event_list.push("[name=" + name + "]");
                jQuery("[name='" + name + "']").on("change", function (e) {
                    dependable.setDependableSettings2(thisObj, class_prefix);
                });
                dependable.setDependableSettings2(thisObj, class_prefix, true);
            }
        });
    }
}
module.exports=main_object;