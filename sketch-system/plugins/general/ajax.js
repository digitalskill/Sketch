var Ajaxlinks = new Class({
	Implements : [Options],
	options : {
		url 			: "",
		value			: "",
		overlay			: true,
		output			: "" 	// [lb|lt]
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object  = obj;
		this.setupOps = '';
		$(this.object).removeClass("ajaxlink");
		this.options.url = $(this.object).get("href");
		if(this.options.url){
		    if(this.options.url.contains("?")){
			    this.options.url += "&ajax=ajax";
		    }else{
			    this.options.url += "?ajax=ajax";
		    }
		}
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
		$(this.object).addEvent("click",this.docall.bind(this));
	},
	unspin:function(){
		if(this.options.overlay){
	    	$(this.options.output).unspin();
		}
	},
	docall:function(event){
		if(event){
			var ev = new Event(event).stop();
		}
		if($(this.object).hasClass('confirm')){
			if(!confirm('this Action cannot be undone. Are you sure?')){
				return false;
			}
		}
		if($(this.options.output) && this.options.url != ''){
			if($(this.options.output).getElement("form")){
				$(this.options.output).getElement("form").fireEvent("removeMCE");
				
			}
			var data = this.options.url.split("?");
			$(this.options.output).set("load",{'url':'',onComplete:this.unspin.bind(this),method:'post','data':data[1]});
			if(this.options.overlay){
				$(this.options.output).spin();
			}
			$(this.options.output).load(data[0] +  "?ajax=ajax");
		}
	}
});
window.addEvent("domready",function(){
	$$('.ajaxlink').each(function(item,index){
		new Ajaxlinks(item);
	});
});