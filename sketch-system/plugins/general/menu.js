// JavaScript Document
var DropMenu = new Class({
	Implements 	: [Options],
	canHide		: false,
	mouseOver	: false,
	options : {
		delayShow	: false,					// Enter a millisecond delay if required
		delay		: 500,
		toggleClass	: 'hover',
		useclass	: true,
		activeClass	: 'active',
		parents		: []
		},
	initialize : function(obj,options){
		this.setOptions(options);
		this.canHide 	= false;
		this.object 	=  obj;	
		this.setupOps 	= '';
		$(this.object).addEvent("mouseenter",this.show.bind(this));
		$(this.object).getElement('a').addEvent("focus",this.show.bind(this));
		$(this.object).getElements('a').getLast().addEvent("blur",this.hide.bind(this));
		$(this.object).addEvent("blur",this.hide.bind(this));
		$(this.object).addEvent("mouseleave",this.hide.bind(this));
		this.timer = null;
		this.rClass = this.removeClass.bind(this);
		this.aClass = this.addClass.bind(this);
	},
	show : function(){
		clearTimeout(this.timer);
		if(this.options.delayShow != false){
			this.timer = this.addClass.delay(this.options.delayShow,this);
		}else{
			this.addClass();
		}
		this.mouseOver = true;
	},
	hide: function(){
		clearTimeout(this.timer);
		this.timer = this.removeClass.delay(this.options.delay,this);
		this.mouseOver = false;
	},
	removeClass: function(){
		clearTimeout(this.timer); 									// Stop any showing timer
		if(this.mouseOver == false){
			$(this.object).getElement("a").removeClass(this.options.activeClass);
			if(this.options.useclass){
				$(this.object).removeClass(this.options.toggleClass);
			}else{
				if($(this.object).getElement("ul")){
					$(this.object).getElement("ul").setStyles({"display":"none","position" : "absolute", left : "-99999em"});
				}
			}
			this.mouseOver = false;
		}
	},
	addClass : function(){
		clearTimeout(this.timer); 									// Stop any Hiding timer in the wings
		$(this.object).getElement("a").addClass(this.options.activeClass);
		if(this.options.useclass){
			$(this.object).addClass(this.options.toggleClass);
		}else{
			if($(this.object).getElement("ul")){
				$(this.object).getElement("ul").setStyles({"visibility":"visible","display":"block","left":0});
			}
		}
	}
});

var MenuController = new Class({
	allDropMenus : [],
	scanClass	 : '.menubar,#menu,.menu',
	parent		 : null,
	initialize : function(){
		var allSpans = this.scanClass.split(",");
		allSpans.each(function(itm){
			$$(itm).each(function(it){
				$(it).addEvent("mouseleave",this.resetCufon);
				var doesuseclass = $(it).hasClass('noclass')? false : true;
				it.getElements("li").each(function(item,index){
					var show = this.show.bind(this);
					item.addEvent("mouseenter",function(){show(this);});
					this.allDropMenus.push(new DropMenu(item,{useclass : doesuseclass}));	
				},this);
			},this);
		},this);
	},
	show: function(obj){
		for(var i=0;i<this.allDropMenus.length;i++){
			if(this.allDropMenus[i].object != obj){
				this.allDropMenus[i].removeClass();
			}
		}
	},
	resetCufon: function(){
		try{
			Cufon.refresh.delay(100);
		}catch(e){}
	}
});
window.addEvent("domready",function(){
	new MenuController();
});