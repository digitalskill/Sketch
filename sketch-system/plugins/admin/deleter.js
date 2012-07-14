var Deleter = new Class({
	Implements 		: [Options],
	ajax			: null,
	options : {
		url			: 'Administration/delete.php',
		remove		: true,
		parentTag	: 'li',								// The parent tag or class name to destroy
		message		: 'Could not delete item',
		table		: '',
		id			: '',
		data		: {
			table	: '',								// the database table to remove
			id		: ''								// The table row id to remove
			}
	},
	removeit: function(){
		if(confirm("Are You Sure?\r\nThis action cannot be undone")){
			var failed	 = this.failed.bind(this);
			var complete = this.complete.bind(this);
			$(this.object).set("load",{
				url : this.options.url,
				method : "post",
				data: this.options.data,
				onFailure: failed,
				onSuccess: complete
			});
			$(this.object).load();
		}
	},
 	initialize : function(obj,options){
		this.setOptions(options);
		this.object = obj;
		if($(this.object).get("rel") && $(this.object).get("rel").contains('{') && $(this.object).get("rel").contains('}')){
			try{
				eval("this.setOptions("+($(this.object).get("rel").substring($(this.object).get("rel").indexOf('{'),$(this.object).get("rel").lastIndexOf('}')+1))+")");
			}catch(e){};
		}
		if(this.options.data.table==""){
			this.options.data.table = this.options.table;
		}
		if(this.options.data.id == ""){
			this.options.data.id = this.options.id;	
		}
		var removeit = this.removeit.bind(this);
		$(this.object).removeEvents();
		$(this.object).addEvent("mousedown",function(event){new Event(event).stop(); removeit();});
		$(this.object).addEvent("click",function(){return false;});
	},
	complete: function(){
		$(this.object).getParent(this.options.parentTag).destroy();
	},
	failed: function(){
		if(this.options.message != ''){
			alert(this.options.message);
		}
	}
});
window.addEvent('domready',function(){
	var deleters = Array();
	$$(".deleter").each(function(item,index){
		deleters.push(new Deleter(item));
	});
});