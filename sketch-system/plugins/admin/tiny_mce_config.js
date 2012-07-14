var cmsURL = window.location.toString();
cmsURL = cmsURL.split("?");
cmsURL = cmsURL[0];
cmsURL = cmsURL.split("index.php");
cmsURL = cmsURL[0];
cmsURL = cmsURL + "index.php";

var configArray = [{
		mode : "exact",
		theme : "advanced",
		plugins : "safari,style,layer,table,advhr,advimage,advlink,searchreplace,contextmenu,paste,fullscreen,noneditable,nonbreaking",
		theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
		theme_advanced_buttons3_add_before : "spellchecker,removeformat",
		theme_advanced_buttons3_add : "tablecontrols,ltr,rtl,fullscreen",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_path_location : "bottom",
		extended_valid_elements : "a[name|href|target|title|onclick|class],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : false,
		nonbreaking_force_tab : true,
		apply_source_formatting : true,
		content_css : cmsURL + "index.php/tinystyles.php",
		remove_script_host : false,
		external_link_list_url : cmsURL + "/pages.php",
		external_image_list_url: cmsURL + "/images.php",
		auto_reset_designmode : true,
		width: "97%",
		height: "500px",
		skin : "o2k7",
		skin_variant : "silver",
		convert_urls : false
},{
	mode : "exact",
	theme : "advanced",
	plugins : "safari,style,layer,table,advhr,advimage,advlink,searchreplace,contextmenu,paste,fullscreen,noneditable,nonbreaking",
	theme_advanced_buttons1 : "bold,italic,|,link,unlink,cut,copy,paste,pastetext,pasteword,separator,formatselect",
	theme_advanced_buttons2 : "styleselect,|,image,|,code,removeformat,fullscreen",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons3_add : "tablecontrols,ltr,rtl,fullscreen",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	extended_valid_elements : "a[name|href|target|title|onclick|class],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : false,
	nonbreaking_force_tab : true,
	apply_source_formatting : true,
	content_css :  cmsURL + "/tinystyles.php",
	remove_script_host : true,
	convert_urls : false,
	external_link_list_url : cmsURL + "/pages.php",
	external_image_list_url: cmsURL + "/images.php",
	auto_reset_designmode : true,
	skin : "o2k7",
	skin_variant : "silver",
	width: "97%",
	height: "200"
},
	{
		// CONFIG 2
		mode : "exact",
		skin : "o2k7",
		skin_variant : "silver",
		theme : "simple"
	},
	{
	// CONFIG 3
	mode : "exact",
	theme : "advanced",
	plugins : "safari,spellchecker,style,layer,table,advhr,advimage,advlink,searchreplace,contextmenu,paste,fullscreen,noneditable,nonbreaking",
	theme_advanced_buttons1 : "bold|italic|styleselect,|,image,|,code,removeformat,fullscreen",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "none",
	extended_valid_elements : "a[name|href|target|title|onclick|class],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : false,
	remove_script_host : false,
	content_css :  cmsURL + "/tinystyles.php",
	convert_urls : false,
	external_link_list_url : cmsURL + "/pages.php",
	external_image_list_url: cmsURL + "/images.php",
	auto_reset_designmode : true,
	width: "25",
	skin : "o2k7",
	skin_variant : "silver",
	height: "20"
	},{
		// CONFIG 4
	mode : "exact",
	theme : "advanced",
	plugins : "safari,style,layer,table,advhr,advimage,advlink,searchreplace,contextmenu,paste,fullscreen,noneditable,nonbreaking",
	theme_advanced_buttons1 : "image,|,removeformat,code",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	extended_valid_elements : "a[name|href|target|title|onclick|class],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	theme_advanced_resize_horizontal : false,
	theme_advanced_resizing : true,
	nonbreaking_force_tab : true,
	apply_source_formatting : true,
	content_css :  cmsURL + "/tinystyles.php",
	remove_script_host : true,
	convert_urls : false,
	external_link_list_url : cmsURL + "/pages.php",
	external_image_list_url: cmsURL + "/images.php",
	auto_reset_designmode : true,
	skin : "o2k7",
	skin_variant : "silver",
	width: "97%",
	height: "300"
	},
	{
		// CONFIG 5
        mode : "exact",
        theme : "advanced",
        plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,removeformat,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
		extended_valid_elements : "a[name|href|target|title|onclick|class],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        apply_source_formatting : true,
		content_css :  cmsURL + "/tinystyles.php",
		remove_script_host : true,
		convert_urls : false,
		external_link_list_url : cmsURL + "/pages.php",
		external_image_list_url: cmsURL + "/images.php"
	}];