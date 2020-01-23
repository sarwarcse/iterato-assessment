var Inputpicker={
    SetColorPicker:function(){
        try{
            $('.app-color-picker').wpColorPicker();
        }catch (e) {
            console.log("No color picker lib found");
        }
    },
    GetDefaultAjaxOption:function(url){
        return {
            ajax: {
                url: url,
                    data: function () {
                    var params = {
                        q: '{{{q}}}'
                    };
                    return params;
                }
            },
            locale: {
                emptyTitle: 'Search...',
                statusInitialized: 'Start typing a search querye',
                currentlySelected: 'Currently Selected',
                searchPlaceholder: "",
                errorText: 'Unable to retrieve results',
                statusNoResults: appGlobalLang.bs_noneResultsTex,
                statusTooShort: 'Please enter more characters',
                statusSearching: appGlobalLang.bs_seaching
            },
            minLength:3,
            preprocessData: function(data){
                return data;
            },
            preserveSelected: true
        }
    },
    SetSelectPicker:function(){
        try{
            try{
                jQuery.fn.selectpicker.Constructor.DEFAULTS.noneResultsText=appGlobalLang.bs_noneResultsText;
                jQuery.fn.selectpicker.Constructor.DEFAULTS.noneSelectedText=appGlobalLang.bs_noneSelectedText;
            }catch(e){}



            $('.app-select-picker:not(.added-spick)').each(function () {
                if($(this).data('live-url')){
                    var newOption=Inputpicker.GetDefaultAjaxOption($(this).data('live-url'));
                    newOption.ajax.url=$(this).data('live-url');

                   /* try{
                        newOption.locale.emptyTitle= "Select";
                    }catch(e){
                        newOption.locale.emptyTitle= "Select"
                    }*/

                    if($(this).data('src-type-str')){
                        newOption.locale.statusInitialized=$(this).data('src-type-str');
                    }

                    $(this).addClass('added-spick')
                        .selectpicker()
                        .ajaxSelectPicker(newOption);
                }else{
                    $(this).addClass('added-spick')
                        .selectpicker();
                }

            });


            ///$('.app-select-picker').selectpicker();
        }catch (e) {
            console.log(e);
            console.log("No select picker lib found");
        }
    }
}
module.exports=Inputpicker;