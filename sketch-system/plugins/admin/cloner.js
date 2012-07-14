//Cloner - Created by Kevin Dibble

var Cloner = new Class({
	Implements 	: [Options],
	options : {
		form		: false,
		parent 		: "ul",
		delclass	: 'delbtn',
		globalParent: false
	},
	initialize : function(obj,options){
		this.obj = obj;
		$(this.obj).removeClass("cloner");
		$(this.obj).addClass("cloned");
		this.setOptions(options);
		if($(this.obj).get("rel") && $(this.obj).get("rel").contains('{') && $(this.obj).get("rel").contains('}')){
			try{
				eval("this.setOptions("+($(this.obj).get("rel").substring($(this.obj).get("rel").indexOf('{'),$(this.obj).get("rel").lastIndexOf('}')+1))+")");
			}catch(e){};
		}
		if($(this.obj).get("title")){
			if($(this.obj).get("title") && $(this.obj).get("title").contains('{') && $(this.obj).get("title").contains('}')){
				try{
					eval("this.setOptions("+($(this.obj).get("title").substring($(this.obj).get("title").indexOf('{'),$(this.obj).get("title").lastIndexOf('}')+1))+")");
				}catch(e){};
				$(this.obj).set("title","");
			}
		}
		if(this.options.globalParent==false){
			this.options.globalParent = this.options.form;	
		}
		var cloner = this.cloner.bind(this);
		$(this.obj).addEvent("click",cloner);
		var goByeBye = this.goByeBye.bind(this);
		$(this.obj).getParent(this.options.parent).getElements("."+this.options.delclass).each(function(item,index){
			$(item).addEvent("click",function(){
				goByeBye();								  
			});																 
		},this);
		if(this.options.globalParent != false && $(this.options.globalParent)){
			this.hide_btn();
		}
	},
	hide_btn: function(){	
		$(this.options.globalParent).getElements(".cloned").each(function(item,index){
				$(item).setStyle("display","none");							  
		});
		$(this.options.globalParent).getElements(".cloned").getLast().setStyle("display","block");
	},
	 cloner: function(){
		var newobj = $(this.obj).getParent(this.options.parent).clone();
		$(newobj).getElements("input[type=text]").each(function(item,index){
			$(item).value = "";														
		});
		$(newobj).getElements("input[type=hidden]").each(function(item,index){
			$(item).value = "";														
		});
		$(newobj).getElements("select").each(function(item,index){
			$(item).value = "";														
		});
		if($(newobj).getElement("."+this.options.delclass)){
			$(newobj).getElement("."+this.options.delclass).destroy();
		}
		var delBtn = new Element("div");
		$(delBtn).addClass(this.options.delclass);
		$(delBtn).set("html",'delete');
		$(newobj).setStyles({"height":0,"overflow":"hidden"});
		$(delBtn).inject($(newobj),'top');
		$(newobj).inject($(this.obj,"after").getParent(this.options.parent));
		$(newobj).set("tween",{onComplete: function(){
			$(newobj).setStyle("height","auto");											  
		}});
		$(newobj).tween("height",$(this.obj).getParent(this.options.parent).getSize().y);
		$(this.obj).setStyle("display","none");
		if(this.form != false && $(this.form)){
			new Validate($(this.form));
		}
		var btn = $(newobj).getElement(".cloned");
		$(btn).set("title","{form:'"+this.options.form+"',parent:'"+this.options.parent+"',delclass:'"+this.options.delclass+"',globalParent:'"+this.options.globalParent+"'}");
		new Cloner($(btn));
	},
	goByeBye: function(){
		var removeanddie = $(this.obj).getParent(this.options.parent);
		$(removeanddie).set("tween",{onComplete: function(){
			var parent = $(removeanddie).getParent(this.options.globalParent);
			$(removeanddie).destroy();										  
			$(parent).getElements('.cloned').getLast().setStyle("display","block");
			if(this.form != false && $(this.form)){
				new Validate($(this.form));
			}
		}});
		$(removeanddie).tween('height',[$(removeanddie).getSize().y,0]);
	}
});
function setupClones(){
	$$(".cloner").each(function(item,index){
		new Cloner(item);						
	});
}
window.addEvent("domready",setupClones);