/* Created by Kevin Dibble 
 * Uses Mootools 1.2
*/
var Popup = new Class({
	Implements : [Options],
	options : {
		pin 		: true,
		thumbnails 	: true,
		id			: 0,
		height		: 200,
		width		: 200,
		url		: '',
		opacity		: 0.7,
		auto		: false,
		closeOffsetT	: 0,
		closeOffsetL	: 0,
		maskWidth	: 0,
		maskHeight	: 0,
		maskOffsetT	: 0,
		classes		: 'round shadow',
		maskOffsetL	: 0,
		parent		: false,
		iframe		: false,
		zindex		: 299,
		className	: ''
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object  = obj;
		this.setupOps = '';
		this.currentArea = window.getScroll();
		this.options.url = $(this.object).get("href");
		$(this.object).removeClass("popup");
		$(this.object).addClass("popupActivated");
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
		if(this.options.url){
		    this.options.url = this.options.url + ((this.options.url.contains('?'))? '&ajax=ajax'  : '?ajax=ajax');
		}
		$(this.object).addEvent("click",this.popup.bind(this));
	},
	centerPopup : function(){
		if($(this.masterContainer)){
			$(this.mask).setStyles({"height":0,"width":0});
			$(this.mask).setStyles({"height":$(window).getSize().y,"width":$(window).getSize().x,"position":"fixed"});	
			$(this.masterContainer).position({relativeTo:$(this.mask),"position":"center"});
			if($(this.masterContainer).getStyle("top") < 0){
				$(this.masterContainer).setStyle("top",10);
			}
			$(this.surround).setStyles({"height":$(this.mask).getSize().y});
			if($(this.closeButton)){
				$(this.closeButton).setStyles({"margin-top":0,"margin-left":0});
				$(this.closeButton).position({relativeTo:$(this.masterContainer),"position":"topRight","edge":"center"});
				$(this.closeButton).setStyles({"margin-top":this.options.closeOffsetT,"margin-left":this.options.closeOffsetL});
			}
		}
	},
	resetPopup: function(){
	    this.setupNavArrows();
	    $(this.masterContainer).getElements("img").setOpacity(1);
	    $(this.masterContainer).fade("in");
	     $(this.masterContainer).unspin();
	    this.centerPopup();
	},
	closePopup: function(){
		if($(this.mask)){
			$(this.mask).getElements("form").each(function(item,index){
				$(item).fireEvent("removeMCE");
			});
			$(document.body).fireEvent("closepopup");
			$(document.body).removeEvents("closepopup");
			$(document.body).removeEvents("closepop");
			$(document.body).removeEvents("popsize");
			if($(this.closeButton)){
			    $(this.closeButton).destroy();
			}
			this.removePopup();
		}
	},
	removePopup: function(){
	    this.mask.hide();
	    $(this.masterContainer).destroy();
	    $(document.body).setStyle("overflow",this.overflow);
	    window.scrollTo(this.currentArea.x, this.currentArea.y);
		$(window).removeEvent("resize",function(){$(document.body).fireEvent("popsize");});
	},
	popup : function(event){
	    if(event){
			new Event(event).stop();
	    }
	    $$(".pop-current").each(function(item,index){
			$(item).removeClass("pop-current");
	    });
	    $(this.object).addClass("pop-current");
	    if($("master-container")){
			this.masterContainer = $("master-container");
			$(this.masterContainer).spin();
	    }else{
			// remove the bodys ability to scroll
			this.currentArea = window.getScroll();
			window.scrollTo(0, 0);

		// Mask the body
		this.mask = new Mask($(document.body),{useIframeShim:true,hideOnClick:false,destroyOnHide:true,maskMargins:true});
		$(this.mask).setStyles({"z-index":this.options.zindex});
		$(this.mask).addClass(this.options.className);
		this.mask.show();
		$(this.mask).pin();

		// hide the body
		this.surround = new Element("div",{"class":"popup-background"});
		$(this.surround).setOpacity(this.options.opacity);
		$(this.surround).inject($(this.mask),"top");
		$(this.surround).setStyles({"height":$(this.mask).getSize().y});
		
		// Add the popup Container
		this.masterContainer = new Element("div",{"class":"master-container","id":"master-container","overflow":"hidden","styles":{"width":30,"height":30}});
		this.masterContainer.setOpacity(0.01);
		$(this.masterContainer).inject($(this.mask),"top");
		$(this.masterContainer).position({relativeTo:$(this.mask),"position":"center"});
		$(this.masterContainer).set("morph",{onComplete:this.resetPopup.bind(this)});

		// Add the loader animation
		$(this.masterContainer).set("spinner",{"id":"popupSpinner"});
		$(this.masterContainer).spin();
		$('popupSpinner').setStyles({"margin-left":this.options.maskOffsetL,"margin-top":this.options.maskOffsetT});

		// Create close button
		this.closeButton 	= new Element("div",{"class":"popup-closeBtn png"});
		$(this.closeButton).setOpacity(0);
		$(this.closeButton).inject($(this.mask),"top");
		$(this.closeButton).addEvent("click",this.closePopup.bind(this));

		// Load content
		if($(this.options.id)){
		    $(this.masterContainer).set("html",$(this.options.id).get("html"));
		    if($(this.masterContainer).getElement("img")){
			this.images = [];
			$(this.masterContainer).getElements("img").each(function(item,index){
			   this.images.push($(item).get("src"));
			},this);
			new Asset.images(this.images,{onComplete:this.showContent.bind(this),onError:this.showContent.bind(this)});
		    }else{
			this.showContent();
		    }
		}else{
			if(this.options.iframe == true){
				this.options.zindex = (this.options.zindex==299)? 999 : this.options.zindex;
				$(this.mask).setStyle("z-index",this.options.zindex + " !important");
				$(this.masterContainer).set("html","<iframe src='"+this.options.url+"' allowtransparency='1' frameborder='0' style='margin:5px' height='"+(this.options.height-10)+"' width='"+(this.options.width-10)+"' scrolling='auto'></iframe>");
				this.showContent();
			}else{
				if(this.options.iframe ==false && (this.options.url.contains("jpg") || this.options.url.contains("png") || this.options.url.contains("gif") || this.options.url.contains("JPG") || this.options.url.contains("PNG") || this.options.url.contains("GIF"))){
					this.singleImage = new Asset.image(this.options.url,{onLoad:this.showContent.bind(this),onError:this.closePopup.bind(this),onAbort:this.closePopup.bind(this)});
				}else{	
					$(this.masterContainer).set("load",{onComplete: this.showContent.bind(this),method:'post'});
					$(this.masterContainer).load(this.options.url);
				}
			}
			$(document.body).addEvent("closepop",this.closePopup.bind(this));
		}
		
		/*
		if($(this.options.id)){
		    this.showSecondContent();
		}else{
		    $(this.masterContainer).spin();
		    $(this.masterContainer).set("load",{onComplete: this.showSecondContent.bind(this)});
		    $(this.masterContainer).load(this.options.url);
		}
		*/
		
		
		// center the popups
		this.centerPopup();

		// Keep the mask size accurate
		$(document.body).addEvent("popsize",this.centerPopup.bind(this));
		$(window).addEvent("resize",function(){$(document.body).fireEvent("popsize");});
	    }
	},
	showSecondContent: function(){
	    if($(this.masterContainer).getElement("img")){
		this.images = [];
		$(this.masterContainer).getElements("img").each(function(item,index){
		    this.images.push($(item).get("src"));
		},this);
		new Asset.images(this.images,{onComplete:this.loadSecondContent.bind(this),onError:this.loadSecondContent.bind(this)});
	    }else{
		this.loadSecondContent();
	    }
	},
	loadSecondContent:function(){
	    var maskSize 	= $(window).getSize();
	    var size = 0;
	    // Get size of content
	    $(this.masterContainer).setOpacity(0.5);
	    var currentSize = $(this.masterContainer).getSize();
	    $(this.masterContainer).setStyles({"height":"auto","width":"auto"});
	    $(this.masterContainer).set("html",$(this.options.id).get("html"));
	    $(this.masterContainer).getElements("img").setOpacity(0.01);
	    if(this.options.auto==true){
		if($(this.masterContainer).getElement(".sizeto")){
		    size = $(this.masterContainer).getElement(".sizeto").measure(function(){
			return this.getSize();
		    });
		}else{
		    size = $(this.masterContainer).measure(function(){
			return this.getSize();
		    });
		}
		this.options.width = size.x;
		this.options.height = size.y;
	    }
	    $(this.masterContainer).setStyles({"height":currentSize.y,"width":currentSize.x});
	    if(this.options.maskWidth==0){
		this.options.maskWidth = this.options.width;
	    }
	    if(this.options.maskHeight==0){
		    this.options.maskHeight = this.options.height;
	    }
	    if($(this.masterContainer).getSize().y != this.options.height || $(this.masterContainer).getSize().x != this.options.width){
		$(this.masterContainer).morph({"left":((maskSize.x-this.options.maskWidth)/2),"top":((maskSize.y-this.options.maskHeight)/2),"width":this.options.maskWidth,"height":this.options.maskHeight});
	    }else{
		$(this.masterContainer).morph({"opacity":0.5});
	    }
	},
	setupNavArrows: function(){
	    if($(this.options.parent)){
		$(this.masterContainer).getElements(".navarrows").each(function(item,index){
		    var me = this.navarrowclick.bind(this)
		    $(item).addEvent("click",function(){me(this);});
		},this);
	    }
	},
	navarrowclick: function(self){
	    var theIndex = 0;
	    $(this.options.parent).getElements(".popupActivated").each(function(item,index){
		if($(item).hasClass("pop-current")){
		    theIndex = index;
		}
	    },theIndex);
	    if($(self).hasClass("popup-leftarrow")){
		if($(this.options.parent).getElements(".popupActivated")[theIndex-1]){
		    $(this.options.parent).getElements(".popupActivated")[theIndex-1].fireEvent("click");
		}else{
		    $(this.options.parent).getElements(".popupActivated").getLast().fireEvent("click");
		}
	    }else{
		if($(this.options.parent).getElements(".popupActivated")[theIndex+1]){
		    $(this.options.parent).getElements(".popupActivated")[theIndex+1].fireEvent("click");
		}else{
		   $(this.options.parent).getElements(".popupActivated")[0].fireEvent("click");
		}
	    }
	},
	showContent: function(){
		var maskSize 	= $(window).getSize();
		var size = 0;
		
		if(this.options.iframe ==false && (this.options.url.contains("jpg") || this.options.url.contains("png") || this.options.url.contains("gif") || this.options.url.contains("JPG") || this.options.url.contains("PNG") || this.options.url.contains("GIF"))){
			this.singleImage.inject($(this.masterContainer));
			this.options.width = this.singleImage.get("width");
			this.options.height = this.singleImage.get("height");
		}
		
		// Get size of content
		if(this.options.auto==true){
		    $(this.masterContainer).setStyles({"width":"auto","height":"auto","max-width":600,"max-height":600});
		    if($(this.masterContainer).getElement(".sizeto")){
			size = $(this.masterContainer).getElement(".sizeto").measure(function(){
			    return this.getSize();
			});
		    }else{
			size = $(this.masterContainer).measure(function(){
			    return this.getSize();
			});
		    }
		    this.options.width = size.x;
		    this.options.height = size.y;
		}
		if(this.options.maskWidth==0){
		    this.options.maskWidth = this.options.width;
		}
		if(this.options.maskHeight==0){
			this.options.maskHeight = this.options.height;
		}
		$('popupSpinner').set("morph",{onComplete:this.removeAnimation.bind(this)});
		$('popupSpinner').getElement(".spinner-content").setStyles({"top":"50%","left":"50%","margin-left":"-15px","margin-top":"-15px"});
		$('popupSpinner').morph({"left":((maskSize.x-this.options.maskWidth)/2),"top":((maskSize.y-this.options.maskHeight)/2),"width":this.options.maskWidth,"height":this.options.maskHeight});
		var valform = this.validateForms.bind(this);
		valform.delay(500);
		this.centerPopup.bind(this).delay(2000);
	},
	validateForms:function(){
		$('master-container').getElements("form").each(function(item,index){
			new Validate($(item));
		});
	},
	removeAnimation: function(){
		$(this.masterContainer).setOpacity(1);
		var maskSize 	= $(window).getSize();
		$(this.masterContainer).setStyles({"height":this.options.height+"px","width":this.options.width+"px"});
		$(this.masterContainer).position();
		$(this.masterContainer).addClass(this.options.classes);
		this.showResult.bind(this).delay(500);
	},
	showResult: function(){
	    $(this.masterContainer).unspin();
	    if($(this.masterContainer).getElement(".innerpop")){
		this.innerPopSize = $(this.masterContainer).getElement(".innerpop").measure(function(){
			return this.getSize();
		});
		$(this.masterContainer).removeEvents("mouseenter");
		$(this.masterContainer).removeEvents("mouseleave");
		$(this.masterContainer).addEvent("mouseenter",this.showInnerPop.bind(this));
		$(this.masterContainer).addEvent("mouseleave",this.hideInnerPop.bind(this));
	    }
	    this.setupNavArrows();
	    $(this.closeButton).position({relativeTo:$(this.masterContainer),"position":"topRight","edge":"center"});
	    $(this.closeButton).setStyles({"margin-top":this.options.closeOffsetT,"margin-left":this.options.closeOffsetL});
	    $(this.closeButton).fade("in");
	    
	},
	showInnerPop :function(){
	    if($(this.masterContainer).getElement(".innerpop")){
		$(this.masterContainer).getElement(".innerpop").morph({"margin-top":[10,-this.innerPopSize.y],"opacity":0.6,"width":this.options.width});
	    }
	},
	firesubEvent:function(){
	     $(this.object).fireEvent("click");
	},
	hideInnerPop: function(){
	    if($(this.masterContainer).getElement(".innerpop")){
		$(this.masterContainer).getElement(".innerpop").morph({"margin-top":10,"opacity":0,"width":this.options.width});
	    }
	}
});

window.addEvent("domready",function(){															
	$$(".popup").each(function(item,index){
		 new Popup(item,{'id':index});
	});																	
});