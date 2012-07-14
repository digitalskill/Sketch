var ScrollerNews = new Class({
	Implements 	: [Options],
	current		: 0,
	options : {
		backid 			: "",
		nextid			: ""
	},
	initialize : function(obj,options){
		this.object = obj;
		this.setOptions(options);
		this.fx = new Fx.Scroll($(this.object));
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
		
		if($(this.options.backid)){
			$(this.options.backid).addEvent("click",this.prev.bind(this));
		}
		if($(this.options.nextid)){
			$(this.options.nextid).addEvent("click",this.next.bind(this));
		}
	},
	prev: function(event){
		event.stop();
		this.current--;
		var elm = $(this.object).getElements("li")[this.current];
		if(elm){
			this.fx.toElement(elm);
		}else{
			this.current++;	
		}
	},
	next: function(event){
		event.stop();
		this.current++;
		var elm = $(this.object).getElements("li")[this.current];
		if($(this.object).getElements("li").length - 5 > this.current){
			this.fx.toElement(elm);
		}else{
			this.current--;
		}
	}
});
window.addEvent("domready",function(){
	$$(".scroller").each(function(item,index){
		new ScrollerNews(item);
	});
});