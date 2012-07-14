/* Created by Kevin Dibble 
 * Uses Mootools
 * Add a class of "calender" to inputs
 * Give it the classes of options desired in the class 
 */
var Tab = new Class({
	Implements : [Options],
	options : {
		tabs: 			'.tab',
		boxes: 			'.box',
		hideClass:	'hide',
		boxcontainer : ''																						
	},
 	initialize : function(obj,options){
		this.setOptions(options);	
		this.object		= $(obj);
		this.setupOps = '';
		this.current 	= 0;
		this.tabs 		= Array();
		this.boxes		= Array(); 
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
		if($(this.object) && $(this.options.boxcontainer)){
			this.tabs = $(this.object).getElements(this.options.tabs);
			this.boxes= $(this.options.boxcontainer).getElements(this.options.boxes);
			var change = this.change.bind(this);
			this.tabs.each(function(item,index){
				$(item).set("rel",index);
				$(item).addEvent("mouseenter",function(){$(this).addClass('over'); });
				$(item).addEvent("mouseleave",function(){$(this).removeClass('over');});
				$(item).addEvent("click",function(){change($(this).get("rel")); });	
			},this);
		}
	},
	change: function(num){
          if($(this.boxes[this.current]) && $(this.boxes[num])){
		$(this.boxes[this.current]).addClass("hide");
		$(this.tabs[this.current]).removeClass("current");
		$(this.boxes[num]).removeClass("hide");
		$(this.tabs[num]).addClass("current");
		this.current = num;
          }
	}
});
window.addEvent("domready",function(){
	$$('.tabme').each(function(item,index){
		new Tab(item);
	});
});