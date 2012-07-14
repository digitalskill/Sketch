/* Created by Kevin Dibble
 * Uses Mootools
 * Add a class of "required" to input feilds / forms - thats all for basic options
 * use the rel="{}" to set advanced options
 * Type = integer | decimal | credit | email | password | username
 * <input name="date" type="text" class="required" size="12" />
 * Use the class tag in the form to setup the form options
 * errors: 'errorLocation', ajax : 'formcall.php', output: 'errorLocation'
 */
var InputOptions = new Class({
    Implements: [Options],
    options: {
        required: false,
        // Set to false to not require [optional]
        type: 'text',
        // This can be: integer,decimal,credit,text,email,password,username
        minValue: 'auto',
        // This is the minimum amount of text or number value
        maxValue: 'auto',
        // This is the Maximum amount of text or number value
        valid: false,
        // Until checked - the input is invalid
        message: 'Please fill in [name]',
        name: 'Feild',
        // The Default Name for the field
        messageLocation: 'alert',
        // The default location for the error message (if set to After, Before, or Element ID)
        doTiny: false,
        // The Default settings for TinyMCE being used (text areas only)
        group: false,
        // If the item belongs to a group (radio , checkboxes)
        showArea: false,
        // The area to show (this is an array)
        tinySettings: false,
        // The Default settings for the the TinyMCE configuration
        calOptions: {},
        // Sets up options for the calender
        messageLeft: 'auto',
        messageTop: 'auto',
        parent: 'auto',
        yesClass: false,
        noClass: false,
        bgClass: false,
        fileTypes: '',
        rich: false,
		lineup:false,
        // Allow Rich text \r\n
        label: false // The label used to go over the text
    },
    getObject: function () {
        return $(this.object);
    },
    getGroup: function () {
        return this.options.group;
    },
    isChecked: function () {
        return $(this.object).checked;
    },
    showLabel: function () {
        if (this.options.label != false && $(this.options.label)) {
            $(this.options.label).position({
                "relativeTo": $(this.object),
                'position': 'topLeft',
                'offset': {
                    x: 5,
                    y: 0
                }
            });
			$(this.options.label).fade("in");
            var currentIndex = ($(this.object).get("value").clean() != "") ? "none" : "block";
            $(this.options.label).setStyle("display", currentIndex);
        }
    },
    hideLabel: function () {
        if (this.options.label != false && $(this.options.label)) {
			$(this.options.label).fade(0.5);
			var currentIndex = ($(this.object).get("value").clean() != "") ? "none" : "block";
            $(this.options.label).setStyle("display", currentIndex);
            $(this.object).focus();
        }
    },
    changeImage: function () {
        $(document.body).fireEvent("buttonsave");
        if ($(this.replaceRadio)) {
            // find all other checked items
            $(this.object).getParent("form").getElements('input[type=radio]').each(function (item, index) {
                if ($(item).name == $(this.object).name && $(item).checked) {
                    $(item).fireEvent('noclass');
                }
            }, this);
            $(this.object).checked = !$(this.object).checked;
            if ($(this.object).checked) {
                $(this.replaceRadio).removeClass(this.options.noClass);
                $(this.replaceRadio).addClass(this.options.yesClass);
            } else {
                $(this.replaceRadio).addClass(this.options.noClass);
                $(this.replaceRadio).removeClass(this.options.yesClass);
            }
        }
    },
    noclass: function () {
        if ($(this.replaceRadio)) {
            $(this.replaceRadio).addClass(this.options.noClass);
            $(this.replaceRadio).removeClass(this.options.yesClass);
        }
    },
    createOptions: function () {
        if ($(this.object).hasClass("required")) {
            this.makeRequired();
        }
        this.options.name = $(this.object).name.capitalize().clean();
        this.options.message = this.options.message.replace(/\[name\]/g, this.options.name);
        if ($(this.object).hasClass("decimal")) {
            this.options.type = "decimal";
        }
        if ($(this.object).type.contains("file")) {
            this.options.type = 'file';
        }
        if ($(this.object).hasClass("integer")) {
            this.options.type = "integer";
        }
        if ($(this.object).hasClass("calender") || $(this.object).hasClass("calendar")) {
            this.options.type = "calender";
        }
        if ($(this.object).hasClass("credit")) {
            this.options.type = "credit";
        }
        if ($(this.object).hasClass("username")) {
            this.options.type = "username";
        }
        if ($(this.object).hasClass("password") || $(this.object).type == "password") {
            this.options.type = "password";
        }
        if ($(this.object).type == "checkbox") {
            this.options.type = "checkbox";
            if (this.options.noClass && this.options.yesClass) {
                this.replaceRadio = new Element("div", {
                    styles: {
                        'position': 'relative'
                    }
                });
                $(this.object).setStyles({
                    "visibility": "hidden",
                    "position": "absolute"
                });
                $(this.replaceRadio).inject($(this.object), "after");
                $(this.replaceRadio).addEvent("click", this.changeImage.bind(this));
                if ($(this.object).checked) {
                    $(this.replaceRadio).addClass(this.options.yesClass);
                } else {
                    $(this.replaceRadio).addClass(this.options.noClass);
                }
            }
        }

        if ($(this.object).type.contains("select") && this.options.bgClass) {
            var tmp = $(this.object).clone();
            this.bgdiv = new Element("a");
            this.innerDiv = new Element("div", {
                "html": "<span class='span'>" + $(this.object).value + "</span><span class='icons downarrow'></span>"
            });
            $(this.bgdiv).addClass(this.options.bgClass + " button");
            $(this.bgdiv).inject($(this.object), "after");
            $(this.innerDiv).inject($(this.bgdiv));
            $(tmp).inject($(this.innerDiv), "top");
            $(tmp).set("id", $(this.object).get("id"));
            $(this.object).destroy();
            this.object = $(tmp);
            $(this.object).setOpacity(0.01);
            $(this.object).addEvent("change", this.updatevalue.bind(this));
            this.updatevalue();
        }

        if ($(this.object).type.contains("file") && this.options.bgClass) {
            this.options.type = 'file';
            $(this.object).addEvent("change", this.updatename.bind(this));
            $(this.object).addEvent("update", this.updatename.bind(this));
            this.bgdiv = new Element("a", {
                "class": "button",
                "html": "<span class='icons file'></span>"
            });
            this.nameArea = new Element("span", {
                "html": "Select File"
            });
            $(this.bgdiv).wraps(this.object);
            $(this.nameArea).inject($(this.bgdiv), "inside");
            $(this.bgdiv).addClass(this.options.bgClass);
            $(this.bgdiv).setStyles({
                "position": "relative",
                "overflow": "hidden"
            });
            $(this.object).setStyles({
                "position": "absolute",
                "left": 0,
                "top": 0,
                "z-index": 2,
                "opacity": 0.01
            });
            $(this.bgdiv).addEvent("mousemove", function (event) {
                var ev = new Event(event);
                var myLocation = $(this).getPosition();
                var left = Math.abs(ev.page.x - myLocation.x);
                var top = Math.abs(ev.page.y - myLocation.y);
                top = top - $(this).getElement("input").getSize().y / 2
                left = left - $(this).getElement("input").getSize().x + 30;
                $(this).getElement("input").setStyles({
                    "margin-top": top,
                    "margin-left": left
                });
            });
        }

        if ($(this.object).type == "radio") {
            this.options.type = "radio";
            if (this.options.noClass && this.options.yesClass) {
                this.replaceRadio = new Element("div", {
                    styles: {
                        'position': 'relative'
                    }
                });
                $(this.object).setStyles({
                    "visibility": "hidden",
                    "position": "absolute"
                });
                $(this.replaceRadio).inject($(this.object), "after");
                $(this.replaceRadio).addEvent("click", this.changeImage.bind(this));
                $(this.object).addEvent("noclass", this.noclass.bind(this));
                if ($(this.object).checked) {
                    $(this.replaceRadio).addClass(this.options.yesClass);
                } else {
                    $(this.replaceRadio).addClass(this.options.noClass);
                }
            }
            this.options.group = this.options.group; //$(this.object).name; // Radio Groups become instantly required
        }
        if ($(this.object).hasClass("email")) {
            this.options.type = "email";
        }
        if (this.options.label != false && $(this.options.label)) {
            $(this.object).addEvent("blur", this.showLabel.bind(this));
            $(this.object).addEvent("focus", this.hideLabel.bind(this));
            $(this.options.label).addEvent('click', this.hideLabel.bind(this));
			$(this.object).addEvent("keyup", this.hideLabel.bind(this));
            currentIndex = ($(this.object).get("value").clean() != "") ? "none" : "block";
            $(this.options.label).position({
                "relativeTo": $(this.object),
                'position': 'topLeft',
                'offset': {
                    x: 5,
                    y: 0
                }
            });
			var theindex = $(this.object).getStyle("z-index").toInt() + 1
            $(this.options.label).setStyles({
                "z-index": theindex,
                "display": currentIndex
            });
            window.addEvent("resize", this.showLabel.bind(this));
        }

        if (this.options.showArea != false) {
            var show = this.showArea.bind(this);
            $(this.object).addEvent('click', show);
            this.options.showArea.each(function (item) {
                $(item).addClass("hide");
            });
        }
    },
    updatename: function () {
        if ($(this.object).get("class").contains("bgClass") && this.options.fileTypes != '') {
            if (this.options.fileTypes.contains($(this.object).get("value").split('.').getLast())) {
                $(this.nameArea).set("html", $(this.object).get("value"));
            } else {
                $(this.nameArea).set("html", "INVALID FILE");
            }
        }
    },
    updatevalue: function () {
        $(this.innerDiv).getElement("span").set("html", $(this.object).getSelected().get("html"));
    },
    showArea: function () {
        var i = null;
        this.options.showArea.each(function (item) {
            if ($(this.object).value == item || $(this.object).checked) {
                $(item).removeClass("hide");
            } else {
                $(item).addClass("hide");
            }
        }, this);
    },
    initialize: function (obj, options) {
        this.object = obj;
        this.thePlace = null;
        this.replaceRadio = false;
        this.setOptions(options);
        this.message = false; // This becomes the DOM element to show error messages
        this.setupOps = '';
		
		if ($(this.object).get("class")) {
            Array.from($(this.object).get("class").split(" ")).each(function (item, index) {
                if (item.contains(":")) {
                    this.setupOps += ((this.setupOps == '') ? '' : ',') + item;
                }
            }, this);
            if (this.setupOps != '') {
                this.setOptions(JSON.decode("{" + this.setupOps + "}"));
            }
        }
		
		
        
        if ($(this.object).hasClass("validate-tip")) {
            this.tip = new toolTips($(this.object), {
                hover: false,
                followMouse: false
            });
        }
        $(this.object).addEvent("keypress", function () {
            $(document.body).fireEvent("buttonsave");
        });
        $(this.object).addEvent("change", function () {
            $(document.body).fireEvent("buttonsave");
        });
        this.options.message = ($(this.object).get("title")) ? $(this.object).get("title") : this.options.message;
        this.createOptions();
        var isValid = this.validate.bind(this);
        if (this.options.type == "integer" || this.options.type == "credit") {
            $(this.object).addEvent("blur", isValid);
            $(this.object).addEvent("keydown", function (event) {
                var ev = new Event(event);
                if ((ev.code < 48 || ev.code > 57) && (ev.code < 96 || ev.code > 105) && (ev.code < 37 || ev.code > 40) && ev.code != 46 && ev.code != 8 && ev.code != 9 && ev.code != 90 && ev.code != 13) {
                    ev.stop();
                }
            });
        }
        if (this.options.type == "decimal") {
            $(this.object).addEvent("blur", isValid);
            $(this.object).addEvent("keydown", function (event) {
                var ev = new Event(event);
                if ((ev.code < 48 || ev.code > 57) && (ev.code < 96 || ev.code > 105) && (ev.code < 37 || ev.code > 40) && ev.code != 46 && ev.code != 8 && ev.code != 9 && ev.code != 90 && ev.code != 190 && ev.code != 13 && ev.code != 110 || ($(this).value.contains('.') && ev.code == 190 && ev.code == 110)) {
                    ev.stop();
                }
            });
        }
        if (this.options.type == "username" || this.options.type == "password") {
            $(this.object).addEvent("blur", isValid);
        }

        if (this.options.type == "calender") {
            this.calender = new Calender($(this.object), this.options.calOptions); // Setup Calender
            $(this.object).removeEvent("blur", this.showLabel.bind(this));
            $(this.object).addEvent("focus", this.hideLabel.bind(this));
			$(this.object).addEvent("blur", isValid);
			$(this.object).addEvent("focus", isValid);
        }

        if (this.options.type == "text") {
            if (!isNaN(this.options.maxValue)) {
                $(this.object).set("maxlength", this.options.maxValue);
            }
            var maxValue = this.options.maxValue;
            $(this.object).addEvent("blur", isValid);
            $(this.object).addEvent("keydown", this.checkLength.bind(this));
        }
        if (this.options.type == "email") {
            $(this.object).addEvent("blur", isValid);
            $(this.object).addEvent("keydown", function (event) {
                var ev = new Event(event);
                if (ev.code == 32) {
                    ev.stop();
                }
            });
        }
			if (this.options.doTiny == true || this.options.doTiny == 'true') { // Setup Tiny MCE for this input
				if(Browser.Platform.ios){
				var text = $(this.object).get('text');
				
				this.elm = new Element("div",{styles:{overflow:"scroll","height":$(this.object).getSize().y,"width":$(this.object).getSize().x}});
				$(this.elm).inject($(this.object),"before");
				
				$(this.object).setStyle("display","none");
				$(this.elm).set("html",text);
				$(this.elm).set("contenteditable","true");
				$(this.elm).addEvent("blur",this.updateEditable.bind(this));
			}else{
				var removeMCE = this.removeMCE.bind(this);
				if ($(this.object).getParent("form")) {
					$(this.object).getParent("form").addEvent("removeMCE", removeMCE);
				}
				try {
					if (this.options.tinySettings !== false) {
						tinyMCE.settings = configArray[this.options.tinySettings.toInt()];
					}
					if ($(this.object).get("id") != "") {
						tinyMCE.execCommand('mceAddControl', false, $(this.object).get("id"));
					} else {
						tinyMCE.execCommand('mceAddControl', false, $(this.object));
					}
				} catch (e) {}
			}
		}
    },
	updateEditable: function(){
		$(this.object).set("text",$(this.elm).get("html"));
	},
    checkLength: function () {
        if (!isNaN(this.options.maxValue) && $(this.object).value.length >= this.options.maxValue) {
            $(this.object).value = $(this.object).value.substring(0, this.options.maxValue);
            $(this.object).highlight();
        }
    },
    removeMCE: function () {
        if ($(this.object)) {
            if ($(this.object).get("id") != "") {
                tinyMCE.execCommand('mceRemoveControl', false, $(this.object).get("id"));
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, $(this.object));
            }
        }
    },
    validate: function () {
        this.options.valid = true;
        if (Modernizr.localstorage && $(this.object).getParent("form").hasClass("memory")) {
            localStorage.setItem(window.location.toString() + ":" + $(this.object).get("name"), $(this.object).get("value"));
        }
        if (this.options.doTiny) {
            try {
                tinyMCE.triggerSave();
            } catch (e) {}
        }
        if (this.options.required && !$(this.object).getParent('.hide')) {
            switch (this.options.type) {
            case 'radio':
            case 'checkbox':
                if (!$(this.object).checked) {
                    this.options.valid = false;
                }
                break;
            case 'decimal':
                $(this.object).value = $(this.object).value.toFloat().round(2);
                if (isNaN($(this.object).value) || $(this.object).value.clean() == "") {
                    $(this.object).value = '';
                    this.options.valid = false;
                } else {
                    if (!isNaN(this.options.minValue) && $(this.object).value.toFloat() < this.options.minValue) {
                        this.options.valid = false;
                        this.options.message = "Please provide a value";
                    } else if (!isNaN(this.options.maxValue) && $(this.object).value.toFloat() > this.options.maxValue) {
                        this.options.valid = false;
                        this.options.message = "Value is too high";
                    }
                }
                break;
            case 'email':
                $(this.object).value = $(this.object).value.clean();
                if ($(this.object).value.indexOf('@') < 2 || $(this.object).value.lastIndexOf('.') < $(this.object).value.indexOf('@')) {
                    this.options.valid = false;
                }
                break;
            case 'integer':
                if (isNaN($(this.object).value) || $(this.object).value.clean() == "") {
                    $(this.object).value = '';
                    this.options.valid = false;
                } else {
                    if (!isNaN(this.options.minValue) && $(this.object).value.toInt() < this.options.minValue) {
                        this.options.valid = false;
                        this.options.message = "Please Provide a Value";
                    } else if (!isNaN(this.options.maxValue) && $(this.object).value.toInt() > this.options.maxValue) {
                        this.options.valid = false;
                        this.options.message = "Value is too high";
                    }
                }
                break;
            case 'credit':
                var checksum = 0;
                var cardNo = $(this.object).value;
                if (isNaN($(this.object).value) || $(this.object).value.clean() == "") {
                    $(this.object).value = '';
                    this.options.valid = false;
                } else {
                    var validVisa = new RegExp(/^(4\d{3}\d{4}\d{4}\d{4})|(4\d{3}\d{4}\d{4}\d{1})$/);
                    var validDinersMaster = new RegExp(/^(5[1-5]\d{2}\d{4}\d{4}\d{4})|(3[0,6,8]\d{2}\d{4}\d{4}\d{2})$/);
                    var validAmexDiscover = new RegExp(/^(3[4,7]\d{2}\d{4}\d{4}\d{1})|(6011\d{4}\d{4}\d{4})$/);
                    if (validVisa.test(cardNo) == false) {
                        if (validDinersMaster.test(cardNo) == false) {
                            if (validAmexDiscover.test(cardNo) == false) {
                                this.options.valid = false;
                            }
                        }
                    }
                    for (var i = (2 - (cardNo.length % 2)); i <= cardNo.length; i += 2) {
                        checksum += parseInt(cardNo.charAt(i - 1));
                    }
                    for (var i = (cardNo.length % 2) + 1; i < cardNo.length; i += 2) {
                        var digit = parseInt(cardNo.charAt(i - 1)) * 2;
                        if (digit < 10) {
                            checksum += digit;
                        } else {
                            checksum += (digit - 9);
                        }
                    }
                    if ((checksum % 10) != 0) {
                        this.options.valid = false;
                    }
                }
                break;
            case 'file':
                if ($(this.object).get("class").contains("bgClass") && this.options.fileTypes != '') {
                    if (!this.options.fileTypes.contains($(this.object).get("value").split('.').getLast())) {
                        this.options.valid = false;
                    }
                }
                if ($(this.object).value == "") {
                    this.options.valid = false;
                }
                break;
            case 'calender':
                if ($(this.object).value.clean() < 3) {
                    this.options.valid = false;
                }
                break;
            default:
                if (this.options.rich == false) {
                    var text = $(this.object).value.clean();
                } else {
                    var text = $(this.object).value
                }
                $(this.object).value = text;
                if (!isNaN(this.options.minValue) && text.length < this.options.minValue) {
                    this.options.valid = false;
                } else if (!isNaN(this.options.maxValue) && text.length > this.options.maxValue) {
                    this.options.valid = false;
                } else {
                    if (text.length < 1 && isNaN(this.options.minValue)) {
                        this.options.valid = false;
                    }
                }
                break;
            }
            if (this.options.valid) {
                $(this.object).removeClass("validate-error");
                $(this.object).addClass("required");
                $(this.object).addClass("checked");
                if ($(this.object).type.contains("select") && this.options.bgClass) {
                    $(this.object).getParent("." + this.options.bgClass).removeClass("validate-error");
                }
                if ($(this.replaceRadio)) {
                    $(this.replaceRadio).removeClass("validate-error");
                }
            } else {
                $(this.object).removeClass("required");
                $(this.object).removeClass("checked");
                $(this.object).addClass("validate-error");
                if ($(this.object).type.contains("select") && this.options.bgClass) {
                    $(this.object).getParent("." + this.options.bgClass).addClass("validate-error");
                }
                if ($(this.replaceRadio)) {
                    $(this.replaceRadio).addClass("validate-error");
                }
            }
        }
        this.getMessage(); // display inline error messages
        return this.options.valid;
    },
    hasFocus: function () {
        $(this.object).removeClass("validate-error");
        if ($(this.object).type.contains("select") && this.options.bgClass) {
            $(this.object).getParent("." + this.options.bgClass).removeClass("validate-error");
        }
        if ($(this.replaceRadio)) {
            $(this.replaceRadio).removeClass("validate-error");
        }
        if (this.options.required) {
            $(this.object).addClass("required");
        }
    },
    isRequired: function () {
        return this.options.required;
    },
    makeRequired: function () {
        this.options.required = true;
        $(this.object).addClass("required");
    },
    makeOptional: function () {
        this.options.required = false;
        $(this.object).removeClass("required");
        $(this.object).removeClass("validate-error");
    },
	clearMessageLocation: function(){
		if($(this.options.messageLocation)){
			$(this.options.messageLocation).set("html","");
		}
	},
    getMessage: function () {
        if (this.tip) {
            this.tip.hide();
        }
		if($(this.options.messageLocation) && this.options.valid){
			$(this.options.messageLocation).set("morph",{onComplete:this.clearMessageLocation.bind(this),"duration":500});
			$(this.options.messageLocation).setStyle("overflow","hidden");
			$(this.options.messageLocation).morph({"opacity":0});
		}
        if (this.options.valid == false) {
            if (this.tip) {
                this.tip.show();
                try {
                    SetUpCufon();
                } catch (e) {}
                return '';
            } else {
				if($(this.options.messageLocation)){
					if($(this.options.messageLocation).get("html").clean()==""){
						$(this.options.messageLocation).setOpacity(0);
						if(this.options.lineup){
							$(this.options.messageLocation).position({relativeTo:$(this.object),"position":this.options.lineup})	
						}
						
						$(this.options.messageLocation).set("html","<span>"+this.options.message+"</span>");
						$(this.options.messageLocation).fade("in");
					}
					return '';
				}else{
                	return this.options.message;
				}
            }
        }
    }
});

var Validate = new Class({
    Implements: [Options],
    group: [],
    groupValid: [],
    options: {
		setupImagePop:true,
        required: false,
        output: false,
        // Must be an element ID for the jax result to be visible
        ajax: false,
        // Set to the URL for the ajax request
        valid: false,
        // The form validation check
        errors: 'alert',
        // The message area for input items - error messages only
        dontSend: false,
        // Dont send the form
        resetform: false,
        hide_form_match: 'auto',
        duel: false,
        duelURL: false,
        loading: false
    },
    initialize: function (form, options) {
        this.setOptions(options);
        this.form = form;
        this.Items = [];
        this.groupItem = [];
        this.message = '';
        this.thePlace = null;
        this.setupOps = '';
        this.newmsg = "";
        if (!$(this.form).hasClass("required") || $(this.form).hasClass("valProcessed")) {
            return false;
        }
        if ($(this.form).getParent("load-box")) {
            $(this.form).spin();
        }
        $(this.form).addClass("valProcessed");
        $(this.form).addEvent("change", function () {
            $(document.body).fireEvent("buttonsave");
        });
        if ($(this.form).get("class")) {
            Array.from($(this.form).get("class").split(" ")).each(function (item, index) {
                if (item.contains(":")) {
                    this.setupOps += ((this.setupOps == '') ? '' : ',') + item;
                }
            }, this);
            this.setOptions(JSON.decode("{" + this.setupOps + "}"));
        }

		if(this.options.setupImagePop){
			$(this.form).getElements(".imageload").each(function(item,index){
					var id = $(item).get("id");
					id = id.split("i");
					id = id[1];
					var img = new Element("img",{"styles":{"width":"auto","height":50,"float":"right"},"id":"im"+id,"src":$(item).get("value")});
					var imgSrc = $(item).get("value").split("sketch-images");
					var cross = "Clear Image";
					var add	   = "Update Image";
					if(!imgSrc[1]){
						add = "Add image";
						cross = "Reset";
					}
					var del = new Element("a",{"class":"button negative","rel":id,"html":'<span class="icons cross"></span>' + cross});
				
					var popu = new Element("a",{"rel":id,"class":"button popupi width:900 height:500 zindex:20002","html":'<span class="icons cog"></span>' + add,"href":"admin/ajax_plugin_images?page_id=1&preview=&outside=&area="+id});
					$(del).inject(item,"before");
					$(popu).inject(item,"before");
					$(img).inject(item,"after");
					if($(item).get("value")==""){
						$(img).fade("out");	
					}
					
					$(del).addEvent("click",function(event){
						new Event(event).stop();
						var id = $(this).get("rel");
						$("i"+id).set("value","");	
						$("im"+id).set("src","");
						$("im"+id).fade("out");
						
					});
					new Popup(popu,{'id':index,zindex:'200002 !important','className':'nodel'});
					$(popu).addEvent("click",function(){
						var id = $(this).get("rel");
						$("im"+id).fade("in");
					});
			});
		}
		
        if ($(this.form).hasClass("required") || this.options.required == true) {
            this.options.required = true;
            $(this.form).addEvent('submit', this.submitCheck.bind(this));
        }
        var formItems = $(this.form).getElements('input'); // Get all inputs
        formItems.combine($(this.form).getElements('textarea')); // Get all Text Areas
        formItems.combine($(this.form).getElements('select')); // Get all Select Boxes
        var count = 0;
        var groupNames = [];
        formItems.each(function (item, index) { // Sets up all Inputs for this form
            if (item.type != "button" && item.type != "submit" && item.type != "hidden") {
                if (Modernizr.localstorage && item.type != "file" && $(this.form).hasClass("memory")) {
                    if (localStorage.getItem(window.location.toString() + ":" + $(item).get("name"))) {
                        $(item).set("value", localStorage.getItem(window.location.toString() + ":" + $(item).get("name")));
                    }
                }
                this.Items.push(new InputOptions(item));
            }
        }, this);
        if (this.options.ajax != false) {
            var fail = this.ajaxFail.bind(this);
            var success = this.ajaxSuccess.bind(this);
            if (this.options.ajax == true || this.options.ajax == 'true') {
                this.options.ajax = $(this.form).get("action");
            }
            $(this.form).set("send", {
                onFailure: fail,
                onSuccess: success,
                evalScripts: true
            });
        }
        this.alertBox = new sketchAlert($(this.form));
        if (this.options.loading == true) {
            this.processingBox = new sketchAlert($(this.form), {
                "isLoading": true
            });
        }
        this.bringback.bind(this).delay(500);
    },
    bringback: function () {
        $(this.form).removeClass("required");
        $(this.form).unspin();
    },
    ajaxFail: function (html, xml) {
        this.showForm();
        alert("Im sorry - The Request Failed\r\nPlease try again Later");
    },
    ajaxSuccess: function (html, xml) {
        if ($(this.options.output)) {
            if ($(this.options.errors)) {
                $(this.options.errors).removeClass("validate-error");
            }
            $(this.options.output).getElements("form").each(function (item, index) {
                $(item).fireEvent("removeMCE");
                $(item).unspin();
            });
            $(this.options.output).set("html", html);
            $(this.options.output).getElements("form").each(function (item, index) {
                if ($(item.hasClass("required"))) {
                    new Validate($(item));
                }
            });
        }
        this.showForm();
        if (this.options.hideform) {
            $(this.form).addClass('hide');
        }
        if (this.options.resetform) {
            $(this.form).reset();
        }
    },
    submitCheck: function (event) {
		$(this.form).getElements('[placeholder]').each(function(item,index) {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
				input.val('');
			}
		});
        var allGroups = [];
        var tmp = [];
        var groupNames = [];
        var found = false;
        var group = '';
        var firstGroupItem = []; //Get the first item of groups for error message and setup values
        if (this.options.required) { // Check if the form is required
            this.options.valid = true; // Its valid until something is not filled in correctly
            this.message = ''; // Set the Message box to empty
            this.Items.each(function (item, index) { // Go through each item in the form
                if (item.validate() == false && !item.getGroup()) { // Is that input item valid?
                    this.options.valid = false; // Dont allow the form to submit
                    this.message += (item.getMessage().clean() != "") ? item.getMessage() + "<br />" : '';
                }
                if (item.getGroup()) { // Check if the item is part of a checkbox group or radio group
                    found = false;
                    group = item.getGroup();
                    allGroups.push(item); // Make an array up of the group
                    groupNames.each(function (name) {
                        if (name == group) {
                            found = true; // check if the name is already in the array
                        }
                    }, this);
                    if (!found) {
                        groupNames.push(item.getGroup()); 	// Make an array of the group names
                        firstGroupItem.push(item); 			// Store the first group item for messages
                    }
                }
            }, this);
            groupNames.each(function (item, index) { 		// Loop through all group names
                found = false; 								// Set the found option for the group
                allGroups.each(function (singleGroup) { 	// Loop through all the group items
                    if (singleGroup.isChecked() && singleGroup.getGroup() == item) { // Check if the item is checked
                        found = true; 
                    }
                });
                if (found == false) {
                    this.message += (firstGroupItem[index].getMessage().clean() != "") ? firstGroupItem[index].getMessage() + "<br />" : '';
                    this.options.valid = false;
                }
            }, this);
            if (this.options.valid == false) {
                if ($(this.options.errors)) { // Check if the message area ID exists
                    $(this.options.errors).set("html", this.message);
                } else {
                    var msg = this.message;
                    if (msg.clean() != "") {
                        try {
                            this.newmsg = "<div style='font-weight:bold;'>The following feilds are required:</div><div>" + msg + "<br /><br /></div>";
                            this.alertBox.setAlertTitle("Sorry, some items were missed");
                            this.alertBox.setAlertText(this.newmsg);
                            $(this.form).fireEvent("doAlert");
                        } catch (e) {
                            var msg = this.message.replace(/<br \/>/g, '\r\n');
                            alert(msg);
                        }
                    }
                }
            }
            if (this.options.valid) {
                if (Modernizr.localstorage) {
                    localStorage.clear();
                }
            }
			
			if(this.options.valid){
				try{
					this.str = "";
					$(this.form).getElements("input").each(function(item,index){
						this.str = " | " + this.str + " " + $(item).name + "="+ $(item.value);
					},this);
					_gaq.push(['_trackEvent', 'Form Submit on ' + window.location.toString(), 'Fields and Data', this.str]);
				}catch(e){}
			}
            if (this.options.ajax != false && this.options.valid) {
                this.hideForm();
                var url = (this.options.ajax.contains("?")) ? this.options.ajax + "&ajax=ajax" : this.options.ajax + "?ajax=ajax";
                $(this.form).send(url);
                return false; // Dont let the form submit
            } else {
                if (this.options.valid) {
                    $(this.form).fireEvent("processing");
                }
                return this.options.valid;
            }
        } else if (this.options.dontSend) {
            return false; // Testing - dont send the form
        }
    },
    hideForm: function () {
        $(this.form).spin();
    },
    showForm: function () {
        if ($(this.form)) {
            $(this.form).unspin();
        }
    }
});
window.addEvent('domready', function () {
    $$('form').each(function (item, index) {
        new Validate(item, index);
    });
	
	// check if the browser supports it natively first
	if ( ! Modernizr.input.placeholder) {
		$$('[placeholder]').each(function(item,index){
			item.addEvent("focus",function() {
				var input = $(this);
				if (input.val() == input.attr('placeholder')) {
					input.val('');
					input.removeClass('placeholder');
				}
			});
			$(item).addEvent("blur",function(item,index) {
				var val = $(this).get("value");
				var attr = $(this).get("placeholder");
				if (val === '' || val == attr) {
					$(this).addClass('placeholder');
					$(this).set("value",attr);
				}
			});
		});
	}
});
window.addEvent("load", function () {
    window.fireEvent("resize");
});


