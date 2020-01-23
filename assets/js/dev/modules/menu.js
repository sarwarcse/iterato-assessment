'use strict';
var sideMenu={
	SetMenuSidebar : function() {
		$('#apd-main-btn').on('click', function () {
			var cookieId=$(this).closest(".APPSBDWP").data("cookie-id");
			$(this).removeClass("on-pre-mini")
			$(this).toggleClass('mini-menu');
			$('#apd-sidebar').toggleClass('active');
			if($('#apd-sidebar').hasClass("active")){
				$('#apd-sidebar .app-tooltip').tooltip('enable');
				APPSBDAPPJS.core.SetCookie(cookieId+"_sel_menu",1,30,'/');
			}else{
				$('#apd-sidebar .app-tooltip').tooltip('disable');
				APPSBDAPPJS.core.SetCookie(cookieId+"_sel_menu",0,30,'/');
			}
			setTimeout(function(){
				try {
					$(window).trigger("resize");
				}catch(e){}
				//APPSBDAPPJS.core.CallOnWindowResize();
			},500);
		});
	}
}
jQuery(document).ready(function ($) {
		if (!$('#apd-sidebar').hasClass("active")) {
			setTimeout(function() {
				$('#apd-sidebar .app-tooltip').tooltip('disable');
			},500);
		}

	if($(window).width() < 576 && $('#apd-main-btn').hasClass("mini-menu") && $('#apd-main-btn').hasClass("on-pre-mini")){
		$('#apd-sidebar').removeClass("active");
		$('#apd-main-btn').removeClass("mini-menu").removeClass("on-pre-mini");
		setTimeout(function(){
			$(window).trigger('resize');
		},1000)
	}
});
module.exports=sideMenu;