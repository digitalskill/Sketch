var Slider = new Class({
	Implements : [Options],
	options : {
		backid 			: "",
		nextid			: ""
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object   = obj;
		this.setupOps = '';
		this.marginleft = 0;
		this.margintop  = 0;
		$(this.object).removeClass("slideme");
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
		if($(this.options.nextid) && $(this.options.backid)){
			this.wrapper = new Element("div");
			$(this.wrapper).setStyles({"width":"100%","position":"relative","overflow":"hidden","z-index":0});
			$(this.wrapper).wraps(this.object);
			$(this.options.backid).addEvent("click",this.goBack.bind(this));
			$(this.options.nextid).addEvent("click",this.doNext.bind(this));
		}
	},
	doNext: function (event){
		new Event(event).stop();
		this.marginleft = this.marginleft - $(this.wrapper).getSize().x;
		var maxMargin   = ($(this.object).getSize().x - $(this.wrapper).getSize().x);
		 
		if(this.marginleft < -maxMargin){
			this.marginleft = -maxMargin;	
		}
		$(this.object).tween("margin-left",this.marginleft);
	},
	goBack: function(event){
		new Event(event).stop();
		this.marginleft = this.marginleft + $(this.wrapper).getSize().x;
		if(this.marginleft > 0){
			this.marginleft = 0;
		}
		$(this.object).tween("margin-left",this.marginleft);
	}
});
window.addEvent("domready",function(){
	$$('.slideme').each(function(item,index){
		new Slider(item);
	});
});