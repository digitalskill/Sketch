/* Created by Kevin Dibble 
 * Uses Mootools
 * Add a class of "toolTip" to dom elements - thats all
 * To use the arrows place the class of lb or lt in the dom element for the tooltip
 * To position the tooltip the default is top right
 * add a class of centerTip = to center the tool tip 
 * add a class of bottomTip to align the til to the bottom (under) the element
 * add a class of leftTip to align the tip to the left
 */
var toolTips = new Class({
	Implements : [Options],
	options : {
		zIndex 		: 99,
		className	: "tip", 	// [lb|lt]
		leftOffset	: 5,
		topOffset	: 5,
		delay		: 500,
		arrow		: 10,
		tipwidth	: 145,
		inject		: "auto",	// Can be [auto | before | after | id]
		followMouse	: true,
		message		: '',
		title		:	'',
		hover		: true,
		hasArrow	: true
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object  = obj;
		this.setupOps = '';
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
		this.timer = null;											// Set up the timer container
		this.pos   = $(obj).getPosition();			// Get the position of the object
		this.size  = $(obj).getSize();
		if(this.options.message=='' && $(this.object).get("title")){
				var title = $(this.object).get("title").split(":");
				this.options.message 	= (title[1])? title[1]  : title[0];
				this.options.title 		= (title[1])? title[0]	: '' ;
		}
		if(this.options.message==""){
			return false;
		}
		var t 		= (this.options.title!='')? "<div class='tip-heading'>"+this.options.title+"</div>" : '';
		this.html = '<div class="tip-top"><div class="tip_tleft"></div><div class="tip_tright"></div></div>'+
								"</div><div class='tip-text'>"+ t +"<div>" + this.options.message + "</div></div><div class='tip-bottom'><div class='tip_bleft'></div><div class='tip_bright'></div></div>";
		$(this.object).set("title","");
		this.elm  = new Element("div",{
						'class': this.options.className,
						'html' : this.html, 
						'opacity' : 0,
						'styles' : 
							{
							  'width'    	: this.options.tipwidth,
							  'z-index'		: this.options.zIndex,
							  'left' 		: this.pos.x + this.size.x + this.options.leftOffset,
							  'top' 		: (this.pos.y + this.options.topOffset)
							  }
						  });
		$(this.elm).inject($(document.body),"top");
		this.elmLeft = this.pos.x + this.size.x + this.options.leftOffset; 
		var show = this.doShow.bind(this);
		$(obj).removeClass("tool-tip");
		if(this.options.hover==true){
			$(obj).addEvent('mouseenter',function(event){show(event)});
			$(obj).addEvent('mouseleave',this.doHide.bind(this));
		}else{
			window.addEvent("resize",this.resized.bind(this));
		}
		if(this.options.hasArrow){
			this.attachArrow();
		}
	},
	attachArrow:function(){
		var arrow = new Element("div",{'class':'tip-arrow ' +"arrow_"+this.options.arrow});
		$(arrow).inject($(this.elm),"top");
	},
	doHide: function(){
		clearTimeout(this.timer);
		var hide = this.hide.bind(this);
		this.timer = hide.delay(this.options.delay);
	},
	doShow: function(event){
		clearTimeout(this.timer);
		var show = this.show.bind(this);
		this.timer = show.delay(this.options.delay);
		this.mouseStick(event);
	},
	hide: function(){
		clearTimeout(this.timer);
		if($(this.elm)){
		    $(this.elm).fade("out");
		    var show = this.mouseStick.bind(this);
		    $(this.object).removeEvent("mousemove",function(event){show(event);});
		}
	},
	mouseStick: function(event){
		var ev 	   = new Event(event);
		this.pos 		= ev.page;
		this.size  	= {x:2,y:2};
		$(this.elm).setStyles(this.getPosition());
	},
	getPosition : function(){
		var left,top;
		switch(this.options.arrow.toString()){
			case "2":
				left = (this.options.followMouse)? (this.pos.x - ($(this.elm).getSize().x + this.options.leftOffset) - 15) : (this.pos.x - ($(this.elm).getSize().x + this.options.leftOffset) - 5) ;
				top  = (this.options.followMouse)? ((this.pos.y - this.options.topOffset - 18)) : ((this.pos.y - this.options.topOffset - 5));
				break;
			case "3":
				left = (this.pos.x - ($(this.elm).getSize().x - this.options.leftOffset) - 27);
				top  = ((this.pos.y + (this.size.y/2)) - ($(this.elm).getSize().y/2));
				break;
			case "4":
				top  = ((this.pos.y + this.size.y + this.options.topOffset - this.elm.getSize().y) + 8);
				left = (this.pos.x - ($(this.elm).getSize().x +this.options.leftOffset) - 15);
				break;
			case "5":
				top  = (this.pos.y - ($(this.elm).getSize().y +this.options.topOffset)-15);
				left = (this.pos.x - ($(this.elm).getSize().x +this.options.leftOffset) + 22);
				break;
			case "6":
				top = (this.pos.y - ($(this.elm).getSize().y + this.options.topOffset) - 15);
				left = (((this.pos.x) + (this.size.x/2)) - $(this.elm).getSize().x/2);
				break;
			case "7":
				top = (this.pos.y - ($(this.elm).getSize().y +this.options.topOffset) - 15);
				left = ((this.pos.x + this.size.x) + (this.options.leftOffset) - 25);
				break;
			case "8":
				top  = ((this.pos.y + this.size.y + this.options.topOffset - this.elm.getSize().y) + 5);
				left = (this.options.followMouse)? ((this.pos.x + this.size.x) + (this.options.leftOffset) + 18)  : ((this.pos.x + this.size.x) + (this.options.leftOffset));
				break;
			case "9":
				top  = ((this.pos.y + (this.size.y/2)) - ($(this.elm).getSize().y/2));
				left = (this.options.followMouse)? ((this.pos.x + this.size.x) + (this.options.leftOffset) + 18)  : ((this.pos.x + this.size.x) + (this.options.leftOffset));
				break;
			case "10":
				top 	= (this.options.followMouse)? ((this.pos.y - this.options.topOffset - 18)) : ((this.pos.y - this.options.topOffset - 5));
				left 	= (this.options.followMouse)? ((this.pos.x + this.size.x) + (this.options.leftOffset) + 18)  : ((this.pos.x + this.size.x) + (this.options.leftOffset)); 
				break;
			case "11":
				top = ((this.pos.y + this.size.y + this.options.topOffset));
				left = (this.pos.x + ($(this.elm).getSize().x +this.options.leftOffset) + 27);
				break;
			case "12":
				top = ((this.pos.y + this.size.y + this.options.topOffset) + 15);
				left = (((this.pos.x) + (this.size.x/2)) - $(this.elm).getSize().x/2);
				break;
			case "1":
				top = ((this.pos.y + this.size.y + this.options.topOffset + 15));
				left = ((this.pos.x + this.size.x - $(this.elm).getSize().x) + (this.options.leftOffset) + 10);
				break;
		}
		if($(this.elm).getPosition().y < 0){
			top = 0;	
		}
		if($(this.elm).getPosition().x + $(this.elm).getSize().x > $(document.body).getSize().x){
			left =  $(this.elm).getPosition().x - (($(this.elm).getPosition().x + $(this.elm).getSize().x) - $(document.body).getSize().x);
		}
		return {'left': left, 'top':top};
	},
	resized : function(){
		this.pos   = $(this.object).getPosition();
		this.size  = $(this.object).getSize();
		$(this.elm).setStyles(this.getPosition());
	},
	show: function(){
		clearTimeout(this.timer);
			if($(this.elm)){
			if(this.options.followMouse){
				var show = this.mouseStick.bind(this);
				$(this.object).removeEvent("mousemove",function(event){show(event);});
				$(this.object).addEvent("mousemove",function(event){show(event);});
			}else{
				this.pos   = $(this.object).getPosition();
				this.size  = $(this.object).getSize();
				$(this.elm).setStyles(this.getPosition());
			}
			$(this.elm).fade("in");
		}
		$(this.elm).setStyles(this.getPosition());
	}
});

window.addEvent('domready',function(){
	$$('.tool-tip').each(function(item,index){
		new toolTips(item);							 
	});
});