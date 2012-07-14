var Gallery = new Class({
	Implements : [Options,Chain],
		options : {
			opacity	:	0.5,
			popup	:	true,
			elements:	'li',
			menu	:   'ul.gallerynav'
		},
	initialize: function(obj){
		this.object = obj;
		$(this.object).removeClass("gallery");
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
		$(this.object).getElements(this.options.elements).setOpacity(0);
		$(this.object).getElements(this.options.elements).addEvent("mouseenter",this.fadeout.bind(this));
		$(this.object).getElements(this.options.elements).addEvent("mouseleave",this.fadein.bind(this));
		if($(document.body).getElement(this.options.menu)){
			this.menu = $(document.body).getElement(this.options.menu);
			this.menu.getElements("a").addEvent("click",this.clicker.bind(this));	
		}
		this.setup();
		this.style  = $(this.object).getElement(this.options.elements).getStyles("width","height","padding-left","margin-left","padding-top","margin-top","padding-left","margin-left","padding-bottom","margin-bottom","padding-left","margin-left","padding-right","margin-right");
		window.addEvent("resize",this.resetgallery.bind(this));
	},
	clicker: function(event){
		if(event){
			event.stop();
			this.menu.getElements("li").removeClass("selected-1");
			this.currentClass = "." + ($(event.target).getParent("li").get("class").clean().split(" ").getLast());
			if(!$(this.object).getElement(this.currentClass)){
				$(this.object).getElements(this.options.elements).morph(this.style);
				var revert = this.revertToStyles.bind(this);
				revert.delay(1000);
			}else{
				$(this.object).getElements(this.options.elements).morph({height:0,width:0,padding:0,margin:0,overflow:'hidden'});
				$(this.object).getElements(this.currentClass).morph(this.style);
			}
			$(event.target).getParent("li").addClass("selected-1");
		}
	},
	resetgallery : function(){
		$(this.object).getElements(this.options.elements).set("style","");
		this.style  = $(this.object).getElement(this.options.elements).getStyles("width","height","padding-left","margin-left","padding-top","margin-top","padding-left","margin-left","padding-bottom","margin-bottom","padding-left","margin-left","padding-right","margin-right");
	},
	revertToStyles: function(){
		$(this.object).getElements(this.options.elements).set("style","");
		this.style  = $(this.object).getElement(this.options.elements).getStyles("width","height","padding-left","margin-left","padding-top","margin-top","padding-left","margin-left","padding-bottom","margin-bottom","padding-left","margin-left","padding-right","margin-right");
	},
	setup: function(){
		this.counter = 0;
		$(this.object).getElements(this.options.elements).each(function(item,index){
			this.counter += 250;
			this.setupFadeIn.bind(item).delay(this.counter);
		},this);
	},
	setupFadeIn: function(itm){
		$(this).fade("in");
	},
	fadeout : function(event){
		if(event){
			event.stop();
			$(event.target).fade(this.options.opacity);
		}
		
	},
	fadein	: function(event){
		if(event){
			event.stop();
			$(event.target).fade("in");
		}
		
	}
});
window.addEvent('domready', function(){
	GalleryRefresh();
});
function GalleryRefresh(){
    $(document.body).getElements('.gallery').each(function(item,index){
	    new Gallery(item);
    });
}