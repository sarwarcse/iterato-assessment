'use strict';
let treeView={
    init : function () {
        $('.treeview:not(.added-tv)').addClass("added-tv").treed({openedClass:'glyphicon-chevron-right', closedClass:'glyphicon-chevron-down'});
    }
}

module.exports=treeView;