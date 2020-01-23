var datetimepickerobject= {
    SetDateTimePicker: function () {
        try {
            $(".app-date-picker:not(.added-picker)").datetimepicker({
                pickTime: false,
                timepicker: false,
                useStrict: true,
                format: "Y-m-d",
                scrollInput: false,
                onSelectDate: function (ct, $i) {
                    $i.trigger("input");
                    $i.trigger("keyup");
                },
                onShow: function (ct, elem) {
                    var thisval = elem.val();
                    var dv = new Date(thisval.replace(/-/g, "/"));
                    var md = dv.getMonth() + 1;
                    if (md < 10) {
                        md = "0" + md;
                    }
                    thisval = dv.getFullYear() + "/" + md + "/" + dv.getDate();

                    var data_min_date = elem.data('min-date');
                    var data_min_elem = elem.data('min-elem');
                    var data_min_elem_data = $(data_min_elem).val();
                    if (data_min_elem_data) {
                        var dt = new Date(data_min_elem_data.replace(/-/g, "/"));
                        var m = dt.getMonth() + 1;
                        if (m < 10) {
                            m = "0" + m;
                        }
                        data_min_elem_data = dt.getFullYear() + "/" + m + "/" + dt.getDate();
                    }

                    var data_max_date = elem.data('max-date');
                    var data_max_elem = elem.data('max-elem');
                    var data_max_elem_val = $(data_max_elem).val();
                    if (data_max_elem_val) {
                        var d = new Date(data_max_elem_val.replace(/-/g, "/"));
                        var m = d.getMonth() + 1;
                        if (m < 10) {
                            m = "0" + m;
                        }
                        data_max_elem_val = d.getFullYear() + "/" + m + "/" + d.getDate();
                    }
                    var opt_min_date = false;
                    if (data_min_elem && data_min_elem_data != "") {
                        opt_min_date = data_min_elem_data;
                        if (thisval == opt_min_date && opt_min_date != data_min_date) {
                            opt_min_date = data_min_date;
                        }
                    } else if (data_min_date && data_min_date != "") {
                        opt_min_date = data_min_date;
                    }

                    var opt_max_date = false;
                    if (data_max_elem && data_max_elem_val != "") {
                        opt_max_date = data_max_elem_val;
                        if (thisval == opt_max_date && opt_max_date != data_max_date) {
                            opt_max_date = data_max_date;
                        }
                    } else if (data_max_date && data_max_date != "") {
                        opt_max_date = data_max_date;
                    }

                    if (opt_min_date) {
                        opt_min_date = opt_min_date.replace(/-/g, "/");
                    }
                    if (opt_max_date) {
                        opt_max_date = opt_max_date.replace(/-/g, "/");
                    }
                    //console.log(opt_min_date+" "+opt_max_date);
                    this.setOptions({
                        minDate: opt_min_date,
                        maxDate: opt_max_date

                    });
                }
            });
            $(".app-date-picker:not(.added-picker)").addClass("added-picker");
        } catch (e) {
        }
        try {
            $(".app-datetime-picker:not(.added-picker)").datetimepicker({
                pickTime: false,
                useStrict: true,
                step: 15,
                format: "Y-m-d H:i",
                onSelectDate: function (ct, $i) {
                    $i.trigger("input");
                    $i.trigger("keyup");
                },
                onSelectTime: function (ct, $i) {
                    $i.trigger("input");
                    $i.trigger("keyup");
                }
            });
            $(".app-datetime-picker:not(.added-picker)").addClass("added-picker");
        } catch (e) {
        }
        try {
            $(".app-time-picker:not(.added-picker)").datetimepicker({
                datepicker: false,
                format: 'H:i',
                step: 15,
                //mask:'23:59',disabled for some problem
                useStrict: true,
                onSelectDate: function (ct, $i) {
                    $i.trigger("input");
                    $i.trigger("keyup");
                },
                onSelectTime: function (ct, $i) {
                    $i.trigger("input");
                    $i.trigger("keyup");
                }
            });
            $(".app-time-picker:not(.added-picker)").addClass("added-picker");
        } catch (e) {
        }
    },
    SetDateGridPicker: function () {
        try {
            $(".gs-date-picker-grid-options").each(function (e) {
                if (!$(this).hasClass("addedDate")) {
                    $(this).addClass("addedDate");
                    var pickerObj = $(this).find(">input");
                    var type = $(this).attr("data-type");
                    var config = {
                        pickTime: true,
                        timepicker: false,
                        useStrict: true,
                        format: "Y-m-d",
                        onChangeDateTime: function (ct, $i) {
                            pickerObj.val(ct.dateFormat('Y-m-d'));
                        }
                    };
                    if (type == "date" || type == "daterange") {
                        config.pickTime = false;
                        config.timepicker = false;
                        config.format = "Y-m-d";

                    } else if (type == "time" || type == "timerange") {
                        config.pickTime = true;
                        config.timepicker = true;
                        config.datepicker = false;
                        config.format = "H:i";
                        config.onChangeDateTime = function (ct, $i) {
                            pickerObj.val(ct.dateFormat('H:i'));
                        }
                    } else if (type == "datetimerange") {
                        config.pickTime = true;
                        config.timepicker = true;
                        config.onChangeDateTime = function (ct, $i) {
                            pickerObj.val(ct.dateFormat('Y-m-d H:i'));
                        }
                    }
                    //console.log(config);
                    $(this).datetimepicker(config);
                }
            });

        } catch (e) {
            gsl(e);
        }
    },
    UnsetDateGridPicker: function () {
        try {
            $(".gs-date-picker-grid-options.addedDate").each(function (e) {
                $(this).removeClass("addedDate").datetimepicker('destroy');
            });

        } catch (e) {
            gsl(e);
        }
    }
}

module.exports=datetimepickerobject;
