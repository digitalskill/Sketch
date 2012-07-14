/* Created by Kevin Dibble 
 * Uses Mootools
 * Add a class of "toolTip" to dom elements - thats all
 * To use the arrows place the class of lb or lt in the dom element for the tooltip
 * To position the tooltip the default is top right
 * add a class of centerTip = to center the tool tip 
 * add a class of bottomTip to align the til to the bottom (under) the element
 * add a class of leftTip to align the tip to the left
 */
var Magnify = new Class({
	Implements : [Options],
	options : {
		zIndex 		: 9999,
		className	: "magnify", 	// [lb|lt]
		mousePoint	: 'center',
		delay		: 500,
		inject		: $(document.body),	// Can be [auto | before | after | id]
		img			: 'auto',
		magWidth	: 200,
		magHeight	: 200
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object  = obj;
		this.setupOps = '';
		this.loaded	 = false;
		this.options.img = $(this.object).get("src");
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
		this.pos   = $(obj).getPosition();			// Get the position of the object
		this.size  = $(obj).getSize();
		this.elm  = new Element("div",{
						'class': this.options.className + " shadow round" ,
						'opacity' : 0,
						'styles' : 
							{
							  'width'    	: this.options.magWidth,
							  'height'		: this.options.magHeight,
							  'z-index'		: this.options.zIndex,
							  'left' 		: this.pos.x + (this.size.x/2),
							  'top' 		: this.pos.y + (this.size.y/2),
							  'background-image':'url('+this.options.img+')',
							  'background-color' : 'transparent',
							  'background-repeat' : 'no-repeat',
							  'border'		: '1px solid #778899',
							  'position'	: 'absolute'
							  }
						  });
		this.Imagebg = new Asset.image(this.options.img);
		$(this.elm).inject($(document.body));
		$(obj).addEvent('mouseenter',this.show.bind(this));
		$(obj).addEvent("mousemove",this.mouseStick.bind(this));
		$(this.elm).addEvent('mousemove',this.mouseStick.bind(this));
	},
	hide: function(){
		$(this.elm).setStyle("visibility","hidden");
	},
	mouseStick: function(event){
		var ev 	    = new Event(event);
		this.pos 	= ev.page;
		this.size  	= {x:2,y:2};
		$(this.elm).setStyles(this.getPosition());
	},
	newImage: function(img){
		this.options.img = img;
		this.Imagebg = new Asset.image(img);
		$(this.elm).setStyle('background-image','url('+img+')');
	},
	getPosition : function(){
		var left,top;
		top  = this.pos.y - (this.options.magHeight/2);
		left = this.pos.x - (this.options.magWidth/2);
		var pos = $(this.object).getPosition();
		var size = $(this.object).getSize();
		
		// move Image background
		var imgHeight = parseInt(((pos.y + size.y) - this.pos.y) - size.y) *-1; //	= 242    = mouseFromTop
		var imgWidth  = parseInt(((pos.x + size.x) - this.pos.x) - size.x) *-1;//   = 292	 = mouseFromLeft
		
		// Work out Ratio
		imgHeight = (imgHeight * (this.Imagebg.height /size.y)) - (this.options.magHeight/2); 
		imgWidth  = (imgWidth * (this.Imagebg.width /size.x)) - (this.options.magWidth/2);
		$(this.elm).setStyles({
			'background-position' : -parseInt(imgWidth)+'px' +' '+ -parseInt(imgHeight)+'px'	
		});
		if(top < (pos.y - (this.options.magHeight/2)) 
				|| top >( pos.y + size.y - (this.options.magHeight/2))
				|| left < (pos.x - (this.options.magWidth/2)) 
				|| left > (pos.x + size.x - (this.options.magHeight/2))){
			this.hide();	
		}else{
			this.show();	
		}
		return {'left': left, 'top':top};
	},
	show: function(){
		$(this.elm).setStyle("visibility","visible");
		$(this.elm).setOpacity(1);
	}
});

window.addEvent('domready',function(){
	$$('.magnify-me').each(function(item,index){
		new Magnify(item);							 
	});
});