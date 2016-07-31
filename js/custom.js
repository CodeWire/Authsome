// JavaScript Document
jQuery(document).ready(function(){
	jQuery('.providersetup select').change(function(){
		if(parseInt(this.value)==0)
		{
			jQuery(this).parents('ul').removeClass("showli");
			jQuery(this).parents('ul').addClass("hiddenli");
		}
		else
		{
			jQuery(this).parents('ul').removeClass("hiddenli");
			jQuery(this).parents('ul').addClass("showli");
		}
	});
	jQuery(".providersetup_inline").colorbox({inline:true, width:"565", height:"360"});
	
jQuery('.check_tags').click(function()
{
if (jQuery(this).attr("checked") == "checked"){
jQuery(this).val('1');
}
else
{
	jQuery(this).val('0');
}
});
	
});