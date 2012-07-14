var accord = new Class({
	Implements : [Options],
		options : {
			bodys: '.accord-body',
			titles: '.accord-title',
			display: -1,
			initialDisplayFx: false,
			alwaysHide:true,
			show: 0
		},
	initialize: function(obj){
			this.object = obj;
			$(this.object).removeClass("accordian");
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
			var headings = $(this.object).getElements(this.options.titles);
			headings.each(function(item,index){
				$(item).addEvent("mouseenter",function(){ $(this).addClass("over"); });
				$(item).addEvent("mouseleave",function(){ $(this).removeClass("over");});
				$(item).addEvent("mouseup",this.refreshme.bind(this));
			},this);
			var bodys	 = $(this.object).getElements(this.options.bodys);
			bodys.setStyle("position","relative");
			new Fx.Accordion(headings,bodys,{onBackground: this.background.bind(this), onActive: this.active.bind(this),display:this.options.display,alwaysHide:this.options.alwaysHide,initialDisplayFx:this.options.initialDisplayFx,show:this.options.show});
	},
	refreshme: function(){
		try{
			Cufon.refresh.delay(10);
		}catch(e){}
	},
	background: function(toggler,element){
		$(toggler).removeClass("open");
		$(toggler).set("html",$(toggler).get("html").replace(/Hide/i,"View"));
	},
	active: function(toggler,element){
		$(toggler).addClass("open");
		$(toggler).set("html",$(toggler).get("html").replace(/View/i,"Hide"));
	}
});
window.addEvent('domready', function(){
	accordRefresh();
});
function accordRefresh(){
    $$('.accordian').each(function(item,index){
	    new accord(item);
    });
	try{
		Cufon.refresh();
	}catch(e){}
}