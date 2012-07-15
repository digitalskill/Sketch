var AdminPanelTab = new Class({
    Implements 		: [Options],
    options : {
	showSave	: true,
	showCancel	: true,
	showReEdit	: true,
	showPreview	: true,
	showPublish	: true,
	pluginURL	: '',
	expose		: false,
	logout		: false,
	image		: false
    },
    initialize : function(obj,controller,options){
	this.setOptions(options);
	this.object 	= obj;
	this.controller = controller;
	this.setupOps	= '';
	if($(this.object).hasClass("select_bg")){
		return false;	
	}
	this.options.pluginURL = $(this.object).get("href") || this.options.pluginURL;
	if($(this.object).get("class")){
	    Array.from($(this.object).get("class").split(" ")).each(function(item,index){
		if(item.contains(":")){
		    this.setupOps += ((this.setupOps=='')?'' :',') + item;
		}
	    },this);
	    if(this.setupOps != ''){
		this.setOptions(JSON.decode("{"+this.setupOps+"}"));
	    }
	}
	$(this.object).addEvent("click",this.getPluginHandler.bind(this));
    },
    getPluginHandler: function(event){
	if(event){
	    new Event(event).stop();
	}
	$(this.object).toggleClass("up");
	this.controller.loadPlugin(this.options,this.object);
	if(this.options.image){
		$(this.object).toggleClass("current");	
	}else{
		if(!$(this.object).getParent(".editing")){
			$(this.object).addClass("current");
		}	
	}
    },
    removeClass: function(){
	$(this.object).removeClass("current");
    }
});

var AdminPanelController = new Class({
    preview 	: false,
    alreadyEdit	: false,
    lastExposed : "",
    imagedown	: false,
    setup		: false,
    parent		: null,
    initialize : function(){
		if(!$('admin_panel')){
			return false;
		}
	this.object = $('admin_panel');
	$(this.object).setOpacity(0);
	
	$(this.object).setStyles({
	    "top":0
	});
	
	$(this.object).removeClass('hide');
	$(this.object).fade("in");
	this.allTabs = Array();
	$(this.object).getElements('a').each(function(item,index){
		this.allTabs.push(new AdminPanelTab(item,this));
	},this);
    },
    loadImages: function(url){
		if($(this.windowMask)){
			if(this.imagedown==false){
			this.imagedown = true;
			this.imageElement = new Element("div",{
				"id":"imageSide"
			});
			$(this.imageElement).inject($(this.windowMask),"bottom");
			$(this.imageElement).setOpacity(0);
			$(this.imageElement).set("morph",{
				onComplete: function(){}
			});
			$(this.loadbox).set("tween",{
				onComplete: function(){}
			});
	
			try{
			$(this.imageElement).setStyles({
			"height":$(this.windowMask).getSize().y-120,
			"background":"#FFF"
			});
			}catch(e){}
			$(this.imageElement).set("load",{
			onComplete: this.slidedownImage.bind(this)
			});
			$(this.imageElement).load(url);
		}else{
			this.imagedown = false;
			$(this.imageElement).set("morph",{
			onComplete: function(){}
			});
			$(this.loadbox).set("tween",{
			onComplete: this.slideup.bind(this)
			});
			$(this.loadbox).tween("margin-right",0);
		}
    }
},
slideup: function(){
    if(this.imagedown == false){
	$(this.loadbox).set("tween",{
	    onComplete: function(){}
	});
	$(this.imageElement).set("morph",{
	    onComplete: this.removeImageLoad.bind(this)
	    });
	$(this.imageElement).morph({
	    "opacity":0
	});
    }
},
slidedownImage:function(){
    if(this.imagedown == true){
	$(this.imageElement).morph({
	    "opacity":1
	});
	$(this.loadbox).tween("margin-right",242);
    }
},
removeImageLoad: function(){
    $(this.imageElement).destroy();
},
loadPlugin: function(objOptions,theobj){
    if(objOptions.image==true){
	this.loadImages(objOptions.pluginURL);
	return false;
    }
    $("assetlink").fade("out");
	$("helplink").fade("out");
    if(this.alreadyEdit){
	return false;
    }
    var nocahcebit = Math.random();
    var urlToCall = ''
    if(objOptions.pluginURL.contains("?")){
	urlToCall = objOptions.pluginURL + "&noc="+nocahcebit;
    }else{
	urlToCall = objOptions.pluginURL + "?noc="+nocahcebit;
    }
    if(objOptions.logout){
	this.closeup();
	$(this.object).load(urlToCall);
	
	$(this.object).setStyle("display","none");
	$(document.body).setStyles({
	    "padding-top":0
	});
	
	return false;
    }
    // Setup page elements
	if($(this.windowMask)){
		$(this.windowMask).getElements("form").each(function(item,index){
			$(item).fireEvent("removeMCE");
		});
	}
    $(document.body).fireEvent("closepopup");
    $(document.body).removeEvents("updatepreview");
    $(document.body).removeEvents("publishnew");
    $(document.body).removeEvents("closepopup");
    $(document.body).removeEvents("getlive");     
    this.allTabs.each(function(item){
	item.removeClass();
    }); 
    if(objOptions.expose != false && $("section_"+objOptions.expose)){
	if($(this.lastExposed)){
	    $(this.lastExposed).addClass("hide");
	}
	$("section_"+objOptions.expose).removeClass("hide");
	if("section_"+objOptions.expose==this.lastExposed){
	    $("sub-nav").addClass("hide");
	    this.closeup();
	    this.lastExposed = "";
	    $(theobj).removeClass("current");
	}else{
	    this.closeup();
	    this.lastExposed = "section_"+objOptions.expose;
	    $("sub-nav").removeClass("hide");
	    $(theobj).addClass("current");
	    this.parent = theobj;
	    if($("section_"+objOptions.expose).getElements("a").length < 2){
		$("section_"+objOptions.expose).getElement("a").fireEvent("click");
	    }
	}
	return false;
    }
    if($(this.parent)){
	$(this.parent).addClass("current");
    }
    this.setup 		= false;
    this.imagedown 	= false;
    if($(this.windowMask)){
	$(this.windowMask).destroy();
    }
    $(document.body).getElements(".adminMask").each(function(item,index){
	$(item).unspin();
	$(item).destroy();
    });
    
    $(document.body).setStyle("overflow","hidden");
    
    this.windowMask = new Mask($(document.body),{
	"id":"adminMask",
	"useIframeShim":true,
	"hideOnClick":false,
	"destroyOnHide":true,
	"class":'adminMask'
    });
    this.windowMask.show();
    if($(this.loadbox)){
	$(this.loadbox).destroy();
	this.loadbox = null;
    }
    $("assetlink").fade("in");
	$("helplink").fade("in");
    
    this.loadbox 	= new Element("div",{
	"id":"load-box",
	"class":"container",
	"styles":{"height":$(this.windowMask).getSize().y}
    });
    $(this.loadbox).inject(this.windowMask,"top");
	this.windowSpinner = new Spinner($(this.loadbox));
    this.windowSpinner.show(true);
    $(this.loadbox).removeEvents("loadup");
    $(this.loadbox).addEvent("loadup",this.showLoad.bind(this));
    if($(this.bottombox)){
	$(this.bottombox).destroy();
	this.bottombox = null;
    }

    this.bottombox 	= new Element("div",{
	"id":"bottom-box",
	"html":'<div class="link"><a class="button"><span class="icons logo"></span> &copy; 2007-2011</a></div>'
    });
    $(this.bottombox).inject(this.windowMask,"bottom");
    if(objOptions.showCancel){
	this.cancelElement = new Element("button",{
	    "class":"butn-cancel negative primary",
	    "id":"butn-cancel",
	    "html":"<span class='cross icons'></span>Cancel &amp; Close Editor"
	});
	$(this.cancelElement).addEvent("click",this.closeup.bind(this));
	$(this.cancelElement).inject($(this.bottombox));
    }
    
    if(objOptions.showSave){
	this.saveElement = new Element("button",{
	    "class":"butn-save positive",
	    "id":"butn-save",
	    "html":"<span class='check icons'></span>Save changes"
	});
	$(this.saveElement).addEvent("click",this.saveForm.bind(this));
	$(this.saveElement).inject($(this.bottombox));
    }

    if(objOptions.showPreview){
	this.previewElement = new Element("button",{
	    "class":"hide",
	    "html":"<span class='magnifier icons'></span>Preview changes"
	});
	$(this.previewElement).addEvent("click",this.saveForm.bind(this));
	$(this.previewElement).inject($(this.bottombox));
	$(this.previewElement).setOpacity(0);
    }
    if(objOptions.showReEdit){
	this.reeditElement = new Element("button",{
	    "class":"hide negative",
	    "html":"<span class='loop icons'></span>Re-edit changes"
	});
	$(this.reeditElement).addEvent("click",this.reEdit.bind(this));
	$(this.reeditElement).inject($(this.bottombox));
	$(this.reeditElement).setOpacity(0);
    }
    if(objOptions.showPublish){
	this.publishElement = new Element("button",{
	    "class":"hide positive primary",
	    "html":"<span class='book icons'></span>Make changes live"
	});
	$(this.publishElement).addEvent("click",this.publish.bind(this));
	$(this.publishElement).inject($(this.bottombox));
	$(this.publishElement).setOpacity(0);
    }

    $(document.body).addEvent("buttonsave",this.allowSave.bind(this));
    $('sub-nav').removeClass("hide");
    $(this.loadbox).set("load",{
	"url":urlToCall,
	onComplete:this.showLoad.bind(this)
	});
    $(this.loadbox).load();
    window.removeEvents("resize");
    window.addEvent("resize",this.resize.bind(this));
},
publish: function(){
    if($(this.publishElement)){
	$(this.publishElement).spin();
	$(document.body).fireEvent("publishnew");
	$(document.body).removeEvents("publishnew");
	$(document.body).removeEvents("updatepreview");
	$(document.body).removeEvents("getlive");
	this.completeSave.bind(this).delay(1000);
    }
},
completeSave: function(){
    $(this.publishElement).unspin();
    this.closeup();
},
reEdit:function(){
    if($(this.reeditElement)){
	if(!$(this.reeditElement).hasClass("hide")){
	    this.preview=false;
	    $(document.body).setStyles({
		"overflow":"hidden",
		"padding-bottom":0
	    });
	    $(this.reeditElement).addClass("hide");
	    if($(this.previewElement)){
		$(this.previewElement).addClass("hide");
	    }
	    if($(this.saveElement)){
		$(this.saveElement).removeClass("hide");
	    }
	    if($(this.publishElement)){
		$(this.publishElement).addClass("hide");
	    }
	}
	$('sub-nav').removeClass("hide");
	$(document.body).removeEvents("updatepreview");
	$(document.body).removeEvents("publishnew");
	this.resize();
    }
},
allowPreview: function(){
    if($(this.saveElement)){
	this.saveElement.unspin();
    }
    if($(this.previewElement)){
	$(this.previewElement).removeClass("hide");
	$(this.previewElement).fade("in");
	$(this.previewElement).removeEvents("click");
	$(this.previewElement).addEvent("click",this.previewChanges.bind(this));
	if($(this.reeditElement)){
	    $(this.reeditElement).addClass("hide");
	}
    }
},
previewChanges:function(){
    $(this.loadbox).setStyles({
	"height":0,
	"overflow":"hidden",
	"margin-top":17
    });
    $('sub-nav').addClass('hide');
    if($(this.imageElement)){
	$(this.imageElement).destroy();
    }
    $(document.body).setStyles({
	"overflow":"auto",
	"padding-bottom":60
    });
    $(document.body).fireEvent("updatepreview");
    $(document.body).removeEvents("updatepreview");
    $(document.body).removeEvents("publishnew");
    $(this.object).addClass("editing");
    this.preview 	 = true;
    this.alreadyEdit = true;
    this.resize();
    this.showPubs.bind(this).delay(1000);
    if($(this.previewElement)){
	$(this.previewElement).spin();
    }
},
showPubs:function(){
    if($(this.previewElement)){
	$(this.previewElement).unspin();
    }
    if($(this.reeditElement)){
	$(this.reeditElement).removeClass("hide");
	$(this.reeditElement).fade("in");
    }
    if($(this.publishElement)){
	$(this.publishElement).removeClass("hide");
	$(this.publishElement).fade("in");
    }
},
closeup:function(){
    $(document.body).fireEvent("closepopup");
    $(document.body).fireEvent("getlive");
    $('sub-nav').addClass('hide');
    if($(this.lastExposed)){
	$(this.lastExposed).addClass("hide");
    }
    this.lastExposed = null;
    window.removeEvents("resize");
    $(document.body).removeEvents("updatepreview");
    $(document.body).removeEvents("publishnew");
    $(document.body).removeEvents("closepopup");
    $(document.body).removeEvents("getlive");
    this.alreadyEdit 	= false;
    this.imagedown 		= false;
    this.preview 		= false;
    this.setup			= false;
    $(this.object).removeClass("editing");
    $(document.body).setStyle("overflow","visible");
    this.removeMask();
},
allowSave: function(){
    if($(this.saveElement)){
	$(this.saveElement).removeClass("hide");
	$(this.saveElement).fade("in");
    }
},
saveForm:function(){
    if($(this.saveElement)){
	$(this.windowMask).getElements("form").each(function(item,index){
	    $(item).fireEvent("submit");
	});
	this.allowPreview.bind(this).delay(1000);
	if($(this.saveElement)){
	    $(this.saveElement).spin();
	}
    }
    this.alreadyEdit = false;
    $(this.object).removeClass("editing");
},
removeMask: function(){
    if($(this.windowMask)){
		$(this.windowMask).getElements("form").each(function(item,index){
				$(item).fireEvent("removeMCE");
			});
	$(this.loadbox).destroy();
	this.windowMask.hide();
	this.windowMask = null;
	this.loadbox = null;
    }
},
showLoad: function(){
    if($(this.windowMask)){
	$(this.windowMask).fade("in");
    }
    $('sub-nav').removeClass('hide');
    this.setup = false;
    this.setupValidate.bind(this).delay(500);
    this.resize();
},
setupValidate:function(){
    $(this.windowMask).getElements("form").each(function(item){
	if($(item).hasClass("required")){
	    new Validate($(item));
	}
    });
    $$('input[type=file]').each(function(item,index){
	new uploader(item);
    });
    if($(this.windowMask)){
	$(this.windowMask).setStyle("filter","none");
    }
    this.unspinme.bind(this).delay(800);
},
unspinme: function(){
    try{
	if($(this.windowSpinner)){
	    $(this.windowSpinner).destroy();
	}
    }catch(e){}
},
reActivateResizeEvent :function(){
    window.removeEvents("resize");
    window.addEvent("resize",this.resize.bind(this));
},
resize: function(){
    try{
	if($(this.windowMask)){
	    var pos = ($("noteZone"))? "static"  : "fixed";
	    if(this.preview == false){
		$(this.windowMask).setStyles({
		    "height":$(document.body).getSize().y,
		    "position":pos,
		    "top":60
		});
		if($(this.imageElement)){
		    $(this.imageElement).setStyles({
			"height":$(this.windowMask).getSize().y-109
			});
		}
		var he	= ($("noteZone"))? "auto"  : $(this.windowMask).getSize().y-109;
		$(this.loadbox).setStyles({
		    "height": he,
		    "overflow":"auto",
			"margin-top":0
		});
		
		$$(".insideMenuSettings").each(function(item,index){
		    $(item).setStyle("height",$(this.windowMask).getSize().y-113);
		},this);
		
		$(this.windowMask).removeClass("noImage");
	    }else{
		$(this.windowMask).setStyles({
		    "height":"60px",
		    "bottom":0,
		    "top":$(window).getSize().y-60
		});
		$(this.loadbox).setStyles({
			"margin-top":17
		});
		$(this.windowMask).addClass("noImage");
		$(this.bottombox).setStyles({
		    "bottom":0
		});
		window.removeEvents("resize");
		this.reActivateResizeEvent.bind(this).delay(1000);
	    }
	}
    }catch(e){}
}
});
window.addEvent("load",function(){
    new AdminPanelController();
});

function setupOvers(obj){
    $(obj).getElements('.admin_image').each(function(item,index){
	$(item).addEvent("mouseenter",function(){
	    if($(this).getElement(".del")){
		$(this).getElement(".del").setOpacity(0);
		$(this).getElement(".del").removeClass('hide');
		$(this).getElement(".del").setOpacity(0.8);
	    }
	});
	$(item).addEvent("mouseleave",function(){
	    if($(this).getElement(".del")){
		$(this).getElement(".del").setOpacity(0);
	    }
	});
    });
    $$('.ajaxlink').each(function(item,index){
	new Ajaxlinks(item);
    });
}

function imageTabs(){
    new Tab($('directory_file_list'));
    new Tab($('asset_menu'));
    new Tab($('directory_image_list'));
	
    $("image_search").addEvent("keyup",function(){
	$('search_result').empty();
	if(this.value.trim() != ''){
	    $('directory_f_images').getElements('.admin_image').each(function(item,index){
		var rel =$(item).get('rel').toString().clean();
		if(rel.test(this.value.clean(),"i")){
		    var it = $(item).clone();
		    $(it).inject('search_result');
		    $(it).addClass("files");
		}
	    },this);
	    var breakdiv = new Element("div",{
		styles:{
		    'clear':'both'
		}
	    });
	$(breakdiv).inject('search_result');
	$('directory_i_images').getElements('.admin_image').each(function(item,index){
	    var rel =$(item).get('rel').toString().clean();
	    if(rel.test(this.value.clean(),"i") && !$(item).hasClass("hide")){
		var it = $(item).clone();
		$(it).inject('search_result');
		$(it).addClass("image");
	    }
	},this);
	$('search_result').getElements('.ajaxlink').each(function(item,index){
	    $(item).addEvent("click",function(){
		$(this.getParent('.admin_image')).destroy();
	    });
	    new Ajaxlinks(item);
	});
	setupOvers("search_result");
    }
    });
$$(".admin_image").each(function(item,index){
    if($(item).getElement(".del")){
	$(item).getElement(".del").removeClass("hide");
	$(item).getElement(".del").setOpacity(0);
	$(item).addEvent("mouseenter",function(){
	    $(this).getElement(".del").setOpacity(0.8);
	});
	$(item).addEvent("mouseleave",function(){
	    $(this).getElement(".del").setOpacity(0);
	});
    }
});
$$('.ajaxlink').each(function(item,index){
    new Ajaxlinks(item);
});
}

window.addEvent("domready",function(){
	$(document.body).addEvent("unload",function(){
		$('admin_panel').destroy();
	});
});