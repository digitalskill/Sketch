/* Created by Kevin Dibble 
 * Uses Mootools
 * Add a class of "calender" to inputs
 * Give it the classes of options desired in the class 
 */
var Calender = new Class({
	Implements : [Options],
	options : {
		format		: 'j-n-Y',
		position	: 'center',
		doTime		: false,
		startAt		: 0,
		opacity		: 1,
		minDate		: false,
		maxDate		: false,
	    invalidDates: [],
		doDays		: true			// Set to False to only show month and year																									
	},
	getDateValue : function(){
		var objDates = $(this.object).get("value");
		var local    = '';
		if(objDates.contains("-")){
			var bits 	 = objDates.split(" ");
			objDates = bits[0].split("-");
			local	 = this.options.format.split("-");
		}else{
			if(objDates.contains("/")){
				objDates = objDates.split("/");
				local	 = this.options.format.split("/");
			}else{
				objDates = objDates.split(" ");
				local	 = this.options.format.split(" ");
			}
		}
		if(objDates.length >= 2){
			for(var i=0;i<objDates.length;i++){
				if(!isNaN(objDates[i])){
					if(local[i]=="j" || local[i]=="l" || local[i]=="jS" || local[i]=="lS"){
						objDates[i] = objDates[i].replace(/(th|st|nd|rd)/g,"");
						this.theDate.setDate(objDates[i]);
					}
					if(local[i]=="Y"){
						this.theDate.setYear(objDates[i]);	
					}
					if(local[i]=="y"){
						this.theDate.setYear("20"+ objDates[i]);
					}
					if(local[i]=="n" || local[i]=="m"){
						this.theDate.setMonth(parseInt(objDates[i])-1);	
					}
					if(local[i]=="M" || local[i]=="N"){
						for(j=0;j<this.allMonths.length;j++){
							if(this.allMonths[j].test(objDates[i],"i")){
								this.theDate.setMonth(j);		
							}
						}
					}
				}
			}
		}else{
			if(!$(this.object).get("class").contains("label:")){
				$(this.object).set("value","dd-mm-yyyy");	
			}
		}
	},
 	initialize : function(obj,options){
		this.setOptions(options);	
		this.object		= $(obj);
		$(this.object).set("autocomplete","off");
		try{
		if($(this.object).getParent().getStyle("position")!="absolute"){
		    $(this.object).getParent().setStyle("position","relative");
		}
		}catch(e){}
		$(this.object).removeEvents();	// remove calender events	
		this.allMonths	= Array('january','february','march','april','may','june','july','august','september','october','november','december');
		this.allDays	= Array("sunday","monday","tuesday","wednesday","thursday","friday","saturday"); 
		this.calender 	= null;
		this.theDate 	= new Date();							// Create Date Object
		// Set the date back
		this.theDate.setYear(this.theDate.getFullYear()+this.options.startAt);
		this.getDateValue();
		this.tableCal = new Element("a",{'class':'button positive pill','html':'<span class="icons calendar"></span>Calendar','styles':{'display':'none'}});
		$(this.tableCal).inject($(this.object),"after");
		this.placeCal();
		$(this.object).addEvent("focus",this.placeCal.bind(this));
		$(this.tableCal).addEvent("click",this.showCalender.bind(this));
		this.keyFn 	= this.keypress.bind(this);
	},
	placeCal : function(){
		try{
		var zin = $(this.object).getStyle("z-index").toInt() + 1;
		$(this.tableCal).setStyles({"z-index":zin + " !important;","display":"block"});
		$(this.tableCal).position({'relativeTo':$(this.object),'position':'topRight'});
		$(this.tableCal).setStyles({"margin-left":-($(this.tableCal).getSize().x + 5),"margin-top":(($(this.object).getSize().y/2) - ($(this.tableCal).getSize().y/2))});
		}catch(e){}
	},
	returnDate: function(obj){
		if (!isNaN($(obj).get("html")) || !isNaN($(obj).get("rel"))) {
			if(this.options.doDays==false){
				this.theDate.setDate(1);
			}else{
				this.theDate.setDate($(obj).get("html").toInt());
			}
			var suffix  = 'th';
			switch (this.theDate.getDate()){
				case 1: case 21: case 31:
					suffix = "st";
				break;
				case 2: case 22: case 32:
					suffix = "nd";
				break;
				case 3: case 23:
					suffix = "rd";
				break;
			}
			
			var tmpDate = ""+this.theDate.getFullYear();
			var cleanedDate = this.options.format.replace(/d/g,(this.theDate.getDate() < 10)? "0" + this.theDate.getDate() : this.theDate.getDate());		// Input the Date Number
			cleanedDate = cleanedDate.replace(/j/g,this.theDate.getDate());		// Input the Date Number
			cleanedDate = cleanedDate.replace(/y/g,tmpDate.substring(2,4));		// Get the Short Year
			cleanedDate = cleanedDate.replace(/Y/g,this.theDate.getFullYear());	// Get the Long Year
			cleanedDate = cleanedDate.replace(/m/g,((this.theDate.getMonth()+1) < 10)? "0" + (this.theDate.getMonth()+1): this.theDate.getMonth()+1);	// Input the Month
			cleanedDate = cleanedDate.replace(/n/g,(this.theDate.getMonth()+1));// Input the Month
			cleanedDate = cleanedDate.replace(/t/g,32-new Date(this.theDate.getYear(),this.theDate.getMonth(),32).getDate());				// Return the amount of days in a month
			cleanedDate = cleanedDate.replace(/l/g,this.allDays[this.theDate.getDay()]);					// Input the Full Day of week
			cleanedDate = cleanedDate.replace(/D/g,this.allDays[this.theDate.getDay()].substring(0,3)); 	// Input a short Day of week
			cleanedDate = cleanedDate.replace(/F/g,this.allMonths[this.theDate.getMonth()]);				// Input the Full Month
			cleanedDate = cleanedDate.replace(/M/g,this.allMonths[this.theDate.getMonth()].substring(0,3));	// Input the Short Month
			cleanedDate = cleanedDate.replace(/S/g,suffix);						// Input the suffix
			if(this.options.doTime){
				cleanedDate = cleanedDate + ' '+ this.theDate.getHours() +':'+ this.theDate.getMinutes() + ':' + this.theDate.getSeconds();
			}
			$(this.object).value = cleanedDate.capitalize().replace(/Of/g,"of");// Return the text with words placed into correct case
		}	
		this.hideCalender(); 													//hide the calender
		$(this.object).fireEvent("focus");
	},
	showCalender: function(){
		this.getDateValue();
		if(this.calender==null){
			var html = "<div class='calender-text'><span></span><div class='calbottom'></div></div>";
			this.calender 		= new Element("div",{
				'class' : 'sketchcalender',
				'styles': {"overflow":"hidden","visibility":"visible"},
				'html'	: html
			});
			$(this.calender).set("morph",{onComplete:this.allowOver.bind(this)});
			$(this.calender).getElement('.calbottom').addEvent("click",this.hideCalender.bind(this));
			$(this.calender).getElement('.calbottom').set("opacity",0);
			$(this.calender).set("opacity",0);					// Hide the calender to begin with
			this.buildCalender();
			if(!this.mask){
				this.mask = new Mask($(document.body),{useIframeShim:true,destroyOnHide:true,style:{'z-index':'9999999 !important'}});
				$(this.calender).inject($(this.mask),'top');
				this.mask.show();
			}
			this.setupFunctions();
			var i = (Browser.firefox)?  window  : $(document.body);
			i.addEvent(Browser.firefox ? "keypress" : "keydown",this.keyFn);
			$(this.calender).position({relativeTo: $(this.object),position:this.options.position});
			var thesize = ($(this.object).getSize().x > 250 || $(this.object).getSize().x < 180)? 200 : $(this.object).getSize().x;
			$(this.calender).morph({"opacity":[0,this.options.opacity],"height":[0,227],"width":[0,thesize],"visibility":"visible"});
		}
		return false;															// Stop Propagation of event of focus and click
	},
	allowOver: function(){
	    $(this.calender).setStyle("overflow","visible");
		$(this.calender).getElement(".calbottom").setStyle("visibility","visible");
	    $(this.calender).getElement(".calbottom").fade("in");
	},
	hideCalender: function(){
		this.calender.destroy();
		this.calender = null;
		this.mask.hide();
		this.mask = null;
		var i = (Browser.firefox)?  window  : $(document.body);
		i.removeEvent(Browser.firefox ? "keypress" : "keydown",this.keyFn);
	},
	buildCalender: function(){
		var i =0;
		this.theDate.setDate(1);												//Set the day to the first for the month
		var DaysInMonth=32-new Date(this.theDate.getYear(),this.theDate.getMonth(),32).getDate();
		var DaysInLastMonth = 32-new Date(this.theDate.getYear(),this.theDate.getMonth()-1,32).getDate();
		var startfrom=this.theDate.getDay();
		var goTo=DaysInMonth+startfrom+1;
		DaysInMonth =((DaysInMonth + startfrom)>34)? 41: 34;
		var NextMonth = 1;
		var chDate = [];
		var DisplayMonth="<table cellpadding='0' cellspacing='0'><tr class='calYear'><td colspan='2' class='calenderPreviousYear'>&lt;&lt;&lt;</td><td colspan='3'><div>"+this.theDate.getFullYear();
		DisplayMonth+="</div><td colspan='2' class='calenderYearAdvance'>&gt;&gt;&gt;</td></tr><tr class='calMonth'><td colspan='2' class='monthPrevious'>&lt;&lt;&lt;</td>";
		DisplayMonth+="<td colspan='3' class='month' rel='1'>"+this.allMonths[this.theDate.getMonth()].capitalize()+"</td>";
		DisplayMonth+="<td colspan='2' class='monthAdvance'>&gt;&gt;&gt;</td></tr>";
		if(this.options.doDays){
			DisplayMonth+="<tr class='calDay'><td>S</td><td>S</td><td>M</td><td>T</td><td>W</td><td>T</td><td>F</td></tr><tr>";
			for(i=0;i<=DaysInMonth;i++){
				DisplayMonth+= (i%7==0)? "</tr><tr>" : '';
				var cd = this.options.invalidDates.indexOf(this.theDate.getFullYear() + "-" + (this.theDate.getMonth()+1) + "-" + (i-startfrom));
				var classd = typeOf(this.options.invalidDates[cd]) == "string"  ? "gray" : "day";
				if(classd=="day"){
					if(this.options.minDate!=false){
						chDate = this.options.minDate.split("-");		
						if(chDate[0] == this.theDate.getFullYear() && parseInt(chDate[1]) == (this.theDate.getMonth() + 1) && (i-startfrom) <= parseInt(chDate[2])){
							classd="gray";
						}
					}
					if(this.options.maxDate!=false){
						chDate = this.options.maxDate.split("-");
						if(chDate[0] == this.theDate.getFullYear() && parseInt(chDate[1]) == (this.theDate.getMonth() + 1) && (i-startfrom) >= parseInt(chDate[2])){
							classd="gray";
						}
					}
				}
				DisplayMonth+= (i>startfrom&&i<goTo)? "<td align='center' class='"+classd+"'>"+(i-startfrom)+"</td>" : (i<=startfrom)? "<td class='pday'>"+(DaysInLastMonth-(startfrom-i))+"</td>" : "<td class='nday'>"+(++NextMonth)+"</td>" ;
			}
		}
		DisplayMonth+="</tr></table>";
		$(this.calender).getElement("span").set("html",DisplayMonth);	
	},
	setupFunctions: function(){
		var returnDate  = this.returnDate.bind(this);
		if(this.options.doDays==false){
			$(this.calender).getElement('.month').removeEvents();
			$(this.calender).getElement('.month').addEvent('click',function(){returnDate(this);});
		}
		$(this.calender).getElements('.day').each(function(item,index){
			item.removeEvents();
			item.addEvent('click',function(){returnDate(this)});
		});
		var sety 		= this.setDateYear.bind(this);
		var setYearN 	= this.setDateYearN.bind(this);
		var setMonthN 	= this.setDateMonth.bind(this);
		$(this.calender).getElements('.monthAdvance').each(function(item){
			item.removeEvents();
			item.addEvent('click',function(){setMonthN(1);});
		});
		$(this.calender).getElements('.monthPrevious').each(function(item){
			item.removeEvents();													 
			item.addEvent('click',function(){setMonthN(-1);});
		});
		$(this.calender).getElements('.calenderYearAdvance').each(function(item){
			item.removeEvents();
			item.addEvent('click',function(){setYearN(1);});
		});
		$(this.calender).getElements('.calenderPreviousYear').each(function(item){
			item.removeEvents();
			item.addEvent('click',function(){setYearN(-1);});
		});
		$(this.calender).getElements('.pday').each(function(item,index){
			item.removeEvents();
			item.addEvent('click',function(){setMonthN(-1)});
		});
		$(this.calender).getElements('.nday').each(function(item,index){
			item.removeEvents();
			item.addEvent('click',function(){setMonthN(1)});
		});
	},
	keypress: function(event){
		var ev = new Event(event);
		switch(ev.code){
			case 37:
				ev.stop();
				this.setDateMonth(-1);
				break;
			case 39: 
				ev.stop();
				this.setDateMonth(1);
				break;
			case 38:
				ev.stop();
				this.setDateYearN(1);
				break;
			case 40:
				ev.stop();
				this.setDateYearN(-1);
				break;
			case 13: 
				ev.stop();
				var obj = new Element("div",{html : '1'});
				this.returnDate(obj);
				break;
			case 9:
				this.hideCalender();
				ev.stop();
				break;
			case 17: case 116 :		// CTRL Key or F5 Key
				break;
		}
	},
	setDateYear: function(theYear){
		this.theDate.setYear(theYear);
		this.buildCalender();
		this.setupFunctions();
	},
	setDateYearN: function(theYear){
		if(this.options.minDate!=false){
			var chDate = this.options.minDate.split("-");
			if(chDate[0] > this.theDate.getFullYear()+theYear){
				return false;
			}
		}
		if(this.options.maxDate!=false){
			var chDate = this.options.maxDate.split("-");
			if(chDate[0] < this.theDate.getFullYear()+theYear){
				return false;
			}
		}
		this.theDate.setYear(this.theDate.getFullYear()+theYear);	
		this.setDateMonth(0);
	},
	setDateMonth: function(theMonth){
		if(this.options.minDate!=false){
			var chDate = this.options.minDate.split("-");
			if(chDate[1]-1 > this.theDate.getMonth()+theMonth && this.theDate.getFullYear()==chDate[0]){
				theMonth = chDate[1]-1;
				this.theDate.setMonth(0);
			}
		}
		if(this.options.maxDate!=false){
			var chDate = this.options.maxDate.split("-");
			if(chDate[1]-1 < this.theDate.getMonth()+theMonth && this.theDate.getFullYear()==chDate[0]){
				theMonth = chDate[1]-1;
				this.theDate.setMonth(0);
			}
		}
		this.theDate.setMonth(this.theDate.getMonth()+theMonth);
		this.buildCalender();
		this.setupFunctions();
	}
});