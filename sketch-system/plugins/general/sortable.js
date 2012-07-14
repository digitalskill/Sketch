var Sorter = new Class({
	Implements 		: [Options],
	ajax			: null,
	options : {
		url			: '',
		constrain	: true,
		clone		: false,
		revert		: true,
		handle		: '.mover',
		table		: '',
		page_id		: 0,
		opacity		: 0.5
	},
	canSend: function(){
		this.canSend = true;	
	},
	complete: function(){
		if($(this.object).getParent("form")){
			$(this.object).getParent("form").fireEvent("change");
		}
		this.unloader();			// Stop current sort request
		var order = '';
		this.mySortables.serialize(false,function(item,index){
			try{
				$(item).getElement(".orderhtml").set('html',index);
				$(item).getElement(".orderfield").set('value',index);
			}catch(e){}
			order = order + $(item).get("rel") + ":" + index +";";
		});
		if(this.options.url!= ''){
			this.ajax = new Request.HTML({  		
				'url'			: this.options.url,  
				'method'		: 'post',  
				'autoCancel'	: true,  
				'data'		    : {'order' : order, noshow : this.options.table, 'update' : 'yes', page_id : this.options.page_id, 'ajax':'ajax'},
				'onFailure'		: function(){
					
				}
			}).send();
		}
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object = obj;
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
		var complete = this.complete.bind(this);
		this.mySortables = new Sortables($(this.object), {
			onComplete	: complete,
			snap		: 15,
			revert		: this.options.revert,
			constrain	: this.options.constrain,
			clone			: this.options.clone,
			handle		: this.options.handle,
			opacity		: this.options.opacity
		});
	},
	unloader: function(){
		if(this.ajax){
			this.ajax.cancel();			// Stop loading requests.	
		}
	}
});
window.addEvent('domready',function(){
	$$('.sortable').each(function(item,index){
		new Sorter(item);						  
	});								
});