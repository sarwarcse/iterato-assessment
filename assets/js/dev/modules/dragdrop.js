var dragdrop= {
    init:function(){
        jQuery(".app-drag-drop:not(.added-dd)").each(function () {
            var thisobj=jQuery(this);
            var thisonly=this;
            thisobj.addClass("added-dd");
            //thisobj.css({"opacity":0,"position":"absolute","left":"0","right":"0","bottom":0,"top":0});
            var wrapper=jQuery('<div class="dd-upload-wrap"><span class="dd-msg">Drag and drop a file or click to select </span></div>');
            thisobj.after(wrapper);
            thisobj.appendTo(wrapper);
            wrapper.bind('dragover', function () {
                wrapper.addClass('dd-file-dropping');
            });
            wrapper.bind('dragleave', function () {
                wrapper.removeClass('dd-file-dropping');
            });
            thisobj.bind('dragover', function () {
                wrapper.addClass('dd-file-dropping');
            });
            thisobj.bind('dragleave', function () {
                wrapper.removeClass('dd-file-dropping');
            });
            thisobj.on("change",function (e) {
                wrapper.removeClass('dd-file-dropping');
                if (this.files && this.files[0]) {
                    wrapper.find(".dd-msg").html(this.files[0].name);
                }
            });
        });
    }
}
module.exports=dragdrop;


