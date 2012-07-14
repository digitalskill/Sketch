/* Created by Kevin Dibble 
 * Uses Mootools
 * Add a class of "alert" to dom elements - thats all
 * To use the arrows place the class of lb or lt in the dom element for the tooltip
 * To position the tooltip the default is top right
 * add a class of centerTip = to center the tool tip 
 * add a class of bottomTip to align the til to the bottom (under) the element
 * add a class of leftTip to align the tip to the left
 */
var sketchAlert = new Class({
	Implements : [Options],
	options : {
		alertzIndex	: 99999,
		className	: "sketch-alert", 	// [lb|lt]
		inject		: $(document.body),	// Can be [auto | before | after | id]
		alertWidth	: 305,
		alertHeight	: 200,
		modal		: true,
		text		: '',
		title		: '',
		isLoading	: false,
		timed		: 5000
	},
 	initialize : function(obj,options){
		if($(obj).hasClass("alertProcessed")){
		    return false;
		}
		this.object   = obj;
		this.setOptions(options);
		$(this.object).addClass("alertProcessed");
		this.setupOps = '';
		this.canHide  = true;
		this.closeHTML= "<div class='alert-close'><button>Return to page</button></div>";
		this.mask = new Mask($(document.body),{'useIframeShim':true,'maskMargins':true});
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
		this.elm  = new Element("div",{
						'class': this.options.className,
						'opacity': 0,
						'styles' : 
							{
							  'width'    	: this.options.alertWidth,
							  'height'	: this.options.alertHeight,
							  'position'	: 'absolute'
							  }
						  });
		var loadClass = "";
		if(this.options.isLoading==true){
			loadClass = "alert-processing";
			this.options.text = $(this.object).get("title");
			this.options.text = this.options.text || 'Processing';
			$(this.object).set("title","");
			this.closeHTML = "";
			$(this.object).addEvent("processing",this.show.bind(this));
		}else{
			this.options.text = $(this.object).get("title");
			this.options.text = this.options.text || 'Alert:Your action has been completed';
			if(this.options.text.contains(":")){
			    var items = this.options.text.split(":");
			    this.options.text = items[1];
			    this.options.title = items[0] || "";
			}
			$(this.object).set("title","");
			$(this.object).addEvent('doAlert',this.show.bind(this));
		}
		$(this.elm).set("html","<div class='alert-container shadow round alert'><div class='alert-title "+ loadClass +"'>"+this.options.title+"</div><div class='alert-text'>"+this.options.text+"</div>"+this.closeHTML+"</div>");
		$(this.object).addEvent('doAlertHide',this.hide.bind(this));
	},
	hide: function(){
		clearTimeout(this.timer);
		this.hideMask.bind(this).delay(600);
		this.canHide  = true;
		$(this.elm).morph({"opacity":0,"margin-top":-50});
	},
	hideMask: function(){
	    this.mask.hide();
	},
	setAlertText: function(text){
		$(this.elm).getElement(".alert-text").set("html",text);
	},
	setAlertTitle: function(text){
		$(this.elm).getElement(".alert-title").set("html",text);
		$(this.elm).getElement(".alert-title").removeClass("alert-processing");
	},
	show: function(){
		if($(this.object).get("title") && (this.options.text != $(this.object).get("title"))){
		    this.options.text = $(this.object).get("title");
		    if(this.options.text.contains(":")){
			var items	    = this.options.text.split(":");
			this.options.text   = items[1];
			this.options.title  = items[0] || "";
		    }
		    this.setAlertTitle(this.options.title);
		    this.setAlertText(this.options.text);
		    $(this.object).set("title","");
		}
		this.mask.show();
		this.mask.position();
		var alert_bg = new Element("div",{'class':'alert-mask'});
		$(this.mask).setStyles({width:window.getSize().x,top:0,left:0,height:window.getSize().y,"position":"fixed",'z-index':99999});
		$(alert_bg).inject($(this.mask));
		$(alert_bg).setStyles({width:window.getSize().x,top:0,left:0,height:window.getSize().y,'opacity':0.6});
		$(this.elm).inject($(this.mask));
		if($(this.elm).getElement(".alert-close")){
			$(this.elm).getElement(".alert-close").addEvent("click",this.hide.bind(this));
		}
		this.canHide  = false;
		if(this.options.isLoading==true){
			$(this.elm).setStyles({'visibility':'visible','top':50,'left':($(window).getSize().x/2) - ($(this.elm).getSize().x/2)});
			$(this.elm).morph({opacity:1,"margin-top":[-50,0]});
		}else{
			$(this.elm).setStyles({'visibility':'visible','top':50,'left':($(window).getSize().x/2) - ($(this.elm).getSize().x/2)});
			$(this.elm).morph({opacity:1,"margin-top":[-50,0]});
		}
		this.timer = this.hide.bind(this).delay(this.options.timed);
	}
});

window.addEvent('domready',function(){
	$$('.alert').each(function(item,index){
		new sketchAlert(item);							 
	});
});