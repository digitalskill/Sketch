/* Carousel Created by Kevin Dibble
Useage: 
	Add a class of animate to the div area
	insert the elements inside the area to animate with a class of "anime"
	To have images pre-load insert a div tage with a class of "anime" and "image" and use the rel tag to point to the image path
Example:
<div class="animate effect:'slide' delay:5000 effectTime:'long' pauseButton:'pause', nextButton:'next' backButton:'last'">
 <img src="../js/quotes/quote1_03.png" width="443" height="310" alt="quote" class="anime"/>
            <p class="anime image" rel="css/images/icons/arrow_up.png">Image</p>
            <p class="anime image" rel="css/images/icons/bullet_delete.png">image</p>
            <p class="anime image" rel="css/images/icons/error.png">image</p>
            <p class="anime image" rel="css/images/icons/page_excel.png">image</p>
            <p class="anime image" rel="css/images/icons/page_word.png">image</p>
            <p class="anime ajax"  rel="index.php">some text</p>

*/
Animator = new Class({
	Implements 	: [Options],
	objects 	: [],
	images		: [],
	doingfx		: false,
	scrollZone	: null,
    Fx              : null,
	options: {
		className   : 'animate',
		subClass    : 'anime',
		effect      : 'fade',         // [fade | slide | up | nothing ]
		pauseButton : null,
		nextButton  : null,
		backButton  : null,
		autoStart   : true,
		delay       : 5000,
		effectTime  : 'short',
		autoCenter  : true,
		scaleWidth  : true,
		scaleHeight : true,
		playText    : '&gt;',
		pauseText   : '||',
		playOnce    : false,
		autoHeight	: false,
		resizeHeight: false,
		count       : false,            // Enter a dom Id to have that show the current count
		totals      : false,            // Enter a dom Id to show total images in the Carasoul
		directBtn   : false		// Set this to a class to create direct buttons for the animation effect
	},
	initialize: function(obj,options){
		this.setOptions(options);
		this.object = obj;
		this.objects = $(this.object).getElements("."+this.options.subClass);
		if(this.objects.length <= 1){
			return false;	
		}
		this.width    = $(this.object).getSize().x;
		this.height   = $(this.object).getSize().y;
		this.left     = $(this.object).getPosition($(this.object).getParent()).x;
		this.top      = $(this.object).getPosition($(this.object).getParent()).y;
		var position  = ($(this.object).getStyle("position") != "absolute")? 'relative' : "absolute";
		var tmpHeight = this.height;
		this.aniLeft = $(this.object).getStyle("padding-left").toInt();
		this.aniTop  = $(this.object).getStyle("padding-top").toInt();
		this.width	 = this.width - ($(this.object).getStyle("padding-left").toInt() + $(this.object).getStyle("padding-right").toInt());
		this.height	 = this.height - ($(this.object).getStyle("padding-top").toInt() + $(this.object).getStyle("padding-bottom").toInt());
		this.setupOps= '';
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
		$(this.object).setStyles({
			'overflow':"hidden",
			'position' : position,
			'height'  : this.height,
			'width'	  : "100%"
		});
		if(this.options.directBtn != false){
			if($("bannerControlScroll")){
				this.scrollZone = new Fx.Scroll($("bannerControlScroll"));
			}
			var jumpToImage = this.jumpToImage.bind(this);
			$$(this.options.directBtn).each(function(item,index){
				$(item).set("rel",index);
				$(item).set("title","");
				item.addEvent("dblclick",function(event){event.stop(); });
				item.addEvent("click",function(event){
					if(event){
						new Event(event).stop();
					}
					jumpToImage(this.get("rel"));							   
				});
			},this);
		}
		this.timer  = null;
		this.current = 0;
		this.setUpObjects();
		if($(this.options.pauseButton)){
			var pause = this.pause.bind(this);	
			$(this.options.pauseButton).addEvent("click",pause);
			$(this.options.backButton).addEvent("dblclick",function(event){event.stop(); });
			this.options.playText = ($(this.options.pauseButton).get("html").clean() != "")?$(this.options.pauseButton).get("html")  : this.options.playText;
		}
		if($(this.options.nextButton)){
			var next = this.next.bind(this);	
			$(this.options.backButton).addEvent("dblclick",function(event){event.stop(); });
			$(this.options.nextButton).addEvent("click",next);
		}
		if($(this.options.backButton)){
			var last = this.last.bind(this);
			$(this.options.backButton).addEvent("dblclick",function(event){event.stop(); });	
			$(this.options.backButton).addEvent("click",last);
		}
		$(this.object).set("tween",{"duration":this.options.effectTime});
		if(this.options.autoStart){
			this.play();
		}
		this.updateIcons();
		window.addEvent("resize",this.resizeonScale.bind(this));
	},
	jumpToImage : function(num){
		if(this.Fx.isRunning()){
			return false;	
		}
		
		clearInterval(this.timer);
		var dir = this.current < num? true : false;
		if(this.current != num){
		    if(this.object){
			    var objOut = this.current;
			    this.current = parseInt(num);
			    if(this.current > this.objects.length -1){
				    this.current = 0;
			    }
			    var objIn = this.current;
			    if(dir){
				this.doEffect(objIn,objOut);
			    }else{
				this.doReverse(objOut,objIn);
			    }
		    }else{
			    this.remove();
		    }
		    this.updateIcons();
		}
	},
	updateIcons:function(){
		var found=false;
		$$(this.options.directBtn).each(function(item,index){
			if($(item).get("rel")){
				if((this.current == 0 || this.current == $(item).get("rel").toInt()) && found==false){
					item.removeClass("fade");
					item.addClass("current");
					found=true;
					if(this.scrollZone){
						this.scrollZone.toElement($(item));	
					}
				}else{
					item.removeClass("current");
					item.addClass("fade");
				}
			}
		},this);
		if($(this.options.count)){
			$(this.options.count).set("html",this.current + 1);
		}
		if($(this.options.totals)){
			$(this.options.totals).set("html",this.objects.length+1);	
		}
	},
	remove : function(){
		clearInterval(this.timer);
	},
	setUpObjects: function(){
        this.Fx = new Fx.Elements(this.objects,{duration: this.options.effectTime,onComplete:this.restoreStack.bind(this)});
		this.objects.each(function(item,index){
			var opaque = (index==0)? 1 : 0;
			var tmpWidth   = this.width - (item.getStyle("padding-left").toInt() + item.getStyle("padding-right").toInt() + item.getStyle("margin-left").toInt() + item.getStyle("margin-right").toInt());
			var tmpHeight  = this.height - (item.getStyle("padding-top").toInt() + item.getStyle("padding-bottom").toInt() + item.getStyle("margin-top").toInt() + item.getStyle("margin-bottom").toInt());
			
			if(this.options.autoHeight){
				tmpHeight ='auto';
			}
			item.setStyles({
				position 	:'absolute',
				opacity		: opaque,
				top			: this.aniTop,
				left		: this.aniLeft,
				width		: "100%",
				height		: tmpHeight,
				overflow	: "hidden"
			});
		   item.removeClass("hide");
		},this);
	},
	restoreStack:function(){
		this.Fx.cancel();
		this.objects.each(function(item,index){
			if($(item).getStyle("opacity")==0){
				$(item).setStyle("z-index",0);
				if(index==this.current){
					$(item).setStyles({"z-index":1,"opacity":1});
				}
			}else{
				$(item).setStyle("z-index",1);
				if(index!= this.current){
					$(item).setStyles({"z-index":0,"opacity":0});
				}	
			}
		},this);
		if(this.options.autoHeight){
			$(this.object).setStyle("height",$(this.objects[this.current]).getSize().y);
		}
	},
	removeLoader: function(){
		this.removeClass("loader");
	},
    doLast: function(){
		if(this.Fx.isRunning()){
			return false;	
		}
          if(this.object){
             var objOut = this.current;
			this.current--;
			if(this.current < 0){
				this.current = this.objects.length-1;
				if(this.options.playOnce == true){
					clearInterval(this.timer);
				}
			}
            var objIn = this.current;
			this.doReverse(objOut,objIn);
		}else{
			this.remove();
		}
		this.updateIcons();
        },
	doNext: function(){
		if(this.Fx.isRunning()){
			return false;	
		}
		if(this.object){
            var objOut = this.current;
			this.current++;
			if(this.current > this.objects.length -1){
				this.current = 0;
				if(this.options.playOnce == true){
					clearInterval(this.timer);
				}
			}
            var objIn = this.current;
			this.doEffect(objIn,objOut);
		}else{
			this.remove();	
		}
		this.updateIcons();
	},
	next: function(event){
		if(event){
        	new Event(event).stop();
		}
		if(this.Fx.isRunning()){
			return false;	
		}
		this.navPause();
		this.doNext();
	},
	last: function(event){
		if(event){
			new Event(event).stop();	
		}
		if(this.Fx.isRunning()){
			return false;	
		}		
		this.navPause();
		this.doLast();
	},
	doEffect: function(objOut,objIn){
        var obj = {};
		switch(this.options.effect){
			case "slideLeft":
			case "slide":
			case "left":
			case "horizontal":
                   obj[objOut] = {"left":[(this.width + this.left + 10), this.aniLeft],"opacity":[1,1]};
                   obj[objIn]  = {"left":[this.aniLeft, -(this.left + this.width)],"opacity":[1,1]};
			break;
			case "fadeslideLeft":
			case "fadeslide":
			case "fadeleft":
			case "fadehorizontal":
                   obj[objOut] = {"left":[(this.width + this.left + 10), this.aniLeft],"opacity":[0,1]};
                   obj[objIn]  = {"left":[this.aniLeft, -(this.left + this.width)],"opacity":[1,0]};
			break;
			case "vertical":
			case "up":
			case "down":
				obj[objOut] = {"top":[this.top - this.height - 10, this.aniTop],"opacity":[1,1]};
                obj[objIn]  = {"top":[this.aniTop,(this.top + this.height)],"opacity":[1,1]};
			break;
			case "fadevertical":
			case "fadeup":
			case "fadedown":
				obj[objOut] = {"top":[this.top - this.height - 10, this.aniTop],"opacity":[0,1]};
                obj[objIn]  = {"top":[this.aniTop,(this.top + this.height)],"opacity":[1,0]};
			break;
			case "fade":
			default:
                obj[objIn] 	= {"opacity":0};
                obj[objOut] =  {"opacity":1};
			break;
		}
		this.Fx.start(obj);
		if(this.options.resizeHeight){
			$(this.objects[this.current]).setStyle("height","auto");
			if($(this.objects[this.current]).getElement(".getheight")){
				$(this.object).tween("height",$(this.objects[this.current]).getElement(".getheight").getSize().y);
			}else{
				if($(this.objects[this.current]).getElement("img")){
					$(this.object).tween("height",$(this.objects[this.current]).getElement("img").getSize().y);
				}
			}
		}
	},
	doReverse: function(objOut,objIn){
        var obj = {};
		switch(this.options.effect){
			case "slideLeft":
			case "slide":
			case "left":
			case "horizontal":
                               obj[objOut] = {"left":[this.aniLeft,(this.width + this.left)],"opacity":[1,1]};
                               obj[objIn]  = {"left":[-(this.left + this.width+10), this.aniLeft],"opacity":[1,1]};
			break;
			case "fadeslideLeft":
			case "fadeslide":
			case "fadeleft":
			case "fadehorizontal":
                 obj[objOut] = {"left":[this.aniLeft,(this.width + this.left)],"opacity":[1,0]};
                 obj[objIn]  = {"left":[-(this.left + this.width+10), this.aniLeft],"opacity":[0,1]};
			break;
			case "vertical":
			case "up":
			case "down":
				obj[objOut]  = {"top":[this.aniTop, this.top - this.height],"opacity":[1,1]};
                obj[objIn] = {"top":[(this.top + this.height+10),this.aniTop],"opacity":[1,1]};
			break;
			case "fadevertical":
			case "fadeup":
			case "fadedown":
				obj[objOut]  = {"top":[this.aniTop, this.top - this.height],"opacity":[1,0]};
                obj[objIn] = {"top":[(this.top + this.height+10),this.aniTop],"opacity":[0,1]};
			break;
			case "fade":
			default:
                obj[objOut] = {"opacity":0},
                obj[objIn] =  {"opacity":1}
			break;
		}
		this.Fx.start(obj);
		if(this.options.resizeHeight){
			$(this.objects[this.current]).setStyle("height","auto");
			if($(this.objects[this.current]).getElement(".getheight")){
				$(this.object).tween("height",$(this.objects[this.current]).getElement(".getheight").getSize().y);
			}else{
				if($(this.objects[this.current]).getElement("img")){
					$(this.object).tween("height",$(this.objects[this.current]).getElement("img").getSize().y);
				}
			}
		}
	},
	navPause: function(){
		clearInterval(this.timer);
		this.timer = null;
		if($(this.options.pauseButton)){
			$(this.options.pauseButton).removeClass('ani-play');
			$(this.options.pauseButton).addClass('ani-pause');
			$(this.options.pauseButton).set("html",this.options.playText);
		}
	},
	pause: function(){
		if(this.timer){
			this.navPause();
		}else{
			this.doNext();
			this.play();
		}
	},
	play : function(){
		this.timer = this.doNext.periodical(this.options.delay,this);
		if($(this.options.pauseButton)){
			$(this.options.pauseButton).addClass('ani-play');
			$(this.options.pauseButton).removeClass('ani-pause');
			$(this.options.pauseButton).set("html",this.options.pauseText);
		}
		if(this.options.resizeHeight){
			if($(this.objects[this.current]).getElement(".getheight")){
				$(this.object).tween("height",$(this.objects[this.current]).getElement(".getheight").getSize().y);
			}else{
				if($(this.objects[this.current]).getElement("img")){
					$(this.object).tween("height",$(this.objects[this.current]).getElement("img").getSize().y);
				}
			}
		}
	},
	resizeonScale: function(){
		if($(this.objects[this.current]).getElement(".banner-image")){
			$(this.object).tween("height",$(this.objects[this.current]).getElement(".banner-image").getSize().y);
			$(this.object).getElements(".anime").tween("height",$(this.objects[this.current]).getElement(".banner-image").getSize().y);
		}
		this.width    = $(this.object).getSize().x;
		this.height   = $(this.object).getSize().y;
		this.left     = $(this.object).getPosition($(this.object).getParent()).x;
		this.top      = $(this.object).getPosition($(this.object).getParent()).y;
		var position  = ($(this.object).getStyle("position") != "absolute")? 'relative' : "absolute";
		this.aniLeft = $(this.object).getStyle("padding-left").toInt();
		this.aniTop  = $(this.object).getStyle("padding-top").toInt();
		this.width	 = this.width - ($(this.object).getStyle("padding-left").toInt() + $(this.object).getStyle("padding-right").toInt());
		this.height	 = this.height - ($(this.object).getStyle("padding-top").toInt() + $(this.object).getStyle("padding-bottom").toInt());
	}
});
window.addEvent("domready",function(){
	$$('.animate').each(function(item,index){
		if($(item).hasClass("now")){
      		new Animator(item);
      		$(item).removeClass("animate");
		}
  });
});
window.addEvent("load",function(){
  $$('.animate').each(function(item,index){
      new Animator(item);
      $(item).removeClass("animate");
  });
});

var readytoMove = false;
var touchsX = 0;
var touchsY = 0;
window.addEvent("domready",function(){
	if($('bannernextbutton')){
		$('banner').addEvent("touchstart",function(event){
			readytoMove = true;
			touchsX = event.page.x;
			touchsY = event.page.y;
		});	
		$('banner').addEvent("touchmove",function(event){
			if(readytoMove){
				var x = event.page.x;
				var y = event.page.y;
				readytoMove = false;
				if(x > touchsX || y > touchsY){
					$('bannerbackbtn').fireEvent("click",event);
				}else{
					$('bannernextbutton').fireEvent("click",event);
				}
			}
		});	
	}
});
