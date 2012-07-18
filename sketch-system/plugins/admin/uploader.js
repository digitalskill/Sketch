// Author Kevin Dibble
// DATE MODIFIED 4/15/2009
// BASED ON SWFUPLOAD 
// REQUIRES : MOOTOOLS 1.2, SWFUPLOAD, 
// EXAMPLE /////////////////////////////////////////////////////////////////////////////////////////////
// The REL tag is where you enter the object options like a javascript object
// e.g : var myOptions = {PHPSESSID};
// EVAL is used to convert this into javascript options

// NOTE : This would require the use of "bridge" in ADOBE AIR applications if this libary is not internal 
/*
<input name="afile" type="file" 
	class="required input" 
	rel="{params: {'PHPSESSID' : '<?php echo session_id(); ?>', 'folder' : 'cms_images'}}"/>
	
	Replaces the file input with a hidden field with the same name
	Hidden field receives the (name) of the file uploaded.
	
	If the file uploaded will have the name changed on the upload script - then check the session with the file name	
	E.g $_SESSION['afile'] would give the file name as modifed by the upload script
*/
var uploader = new Class({
	ani 		: null,					// Used to keep track of the progress animations
	Implements 	: [Options],
	options 	: {
		flash_url 	: 'auto',
		upload_url	: 'auto',
		button_url	: 'auto',
		show_thumb      : 'auto',
		auto_load	: true,
		types		: "*.jpg;*.gif;*.png;*.pdf;*.doc;*.ppt;*.flv;*.swf;*.mp3;*.wav;*.dot;*.xls",
		size_limit 	: "100 MB",
		description	: 'ALL Files',
		queue_limit	: 0,
		upload_limit: 0,
		button_width: 0,
		button_height: 0,
		show_image	: true,
		show_place	: 'auto',			// Must be an ID of a div object to show the image uploaded
		button_fade	: 0.5,
		imageBrowser: false,
		updateval	: false,
		params		: {PHPSESSID 	: '',
					   folder 		: '',
					   theFile		: ''}
	},
	showResult: function(){
		$(this.result).fade("in");
		if(this.Fx != null){
			this.Fx.cancel();
			var child = $(this.result).getChildren()[0];		// Get the first object in the area
			if(child && $(child).getSize().y > $(this.result).getSize().y){
				if($(child).getSize().y > 200){
					$(child).setStyles({"height":200,"width":"auto"});
				}
				this.Fx.start('height',$(child).getSize().y);	// Animate to that objects size
			}else{
				$(this.result).style.height = "auto";			// clear in case the image did not fully load (ie -no height)
			}
		}
	},
	initialize: function(obj,options){
		this.object 	= obj;
		if($(this.object).hasClass("nojs")){
			return false	
		}
		this.file 		= null;
		this.setOptions(options);
		if($(this.object).get("rel") && $(this.object).get("rel").contains('{') && $(this.object).get("rel").contains('}')){
			try{
				eval("this.setOptions("+($(this.object).get("rel").substring($(this.object).get("rel").indexOf('{'),$(this.object).get("rel").lastIndexOf('}')+1))+")");
			}catch(e){};
		}
		if(Browser.Plugins.Flash.version < 10){
			return false;
		}
		this.swfURL = window.location.toString().split("/index.php");
                this.swfURL = this.swfURL[0].split("?");
                this.swfURL = this.swfURL[0] +  "/sketch-upload/";
		this.imagePath= this.swfURL.substring(0,this.swfURL.lastIndexOf('/'));
		if(this.options.flash_url == 'auto'){
			this.options.flash_url = this.swfURL + "swfupload.swf";	
		}
		if(this.options.upload_url == 'auto'){
			this.options.upload_url = this.swfURL + "upload.php";
		}
		if(this.options.button_url == 'auto'){
			this.options.button_url = this.swfURL + "folder.png";
		}
		if(this.options.params.theFile ==''){
			this.options.params.theFile = encodeURIComponent($(this.object).get("name"));	
		}
		this.wrapper = new Element('div',{
				'class' : 'uploaderWrapper'
		});
		this.FileBtn = new Element('div',{
				'id'	 : obj.name +'select',
				'class'	 : 'flashBtn'
			});
		this.myTmp  = new Element('div', {
				'class'	: "FileUploadSection"
		});
		if(this.options.show_place == "auto"){
			this.result 			= new Element("div", {'class':'result'});
			$(this.result).inject($(this.wrapper),'top');
			this.Fx = new Fx.Tween($(this.result));
		}else{
			if($(this.options.show_place)){
				this.result = $(this.options.show_place);
			}
		}
		if(this.options.imageBrowser){
			$(this.result).addEvent("click",function(){
				FileBrowserDialogue.mySubmit($(this.result).getElement('.link').get("rel"));										 
			});	
		}
		this.FileUploadName 	= new Element("div", {'class' : 'FileUploadName', 'html' : 'Select File'});
		this.FileUploadProgress = new Element("div", {'class' : 'FileUploadProgress'});
		$(this.wrapper).inject($(obj),"before");
		$(this.myTmp).inject($(this.wrapper),"top");
		$(this.FileUploadProgress).inject($(this.myTmp),'top');
		$(this.FileUploadName).inject($(this.myTmp),'top');
		$(this.FileBtn).inject($(this.myTmp),'top');
		var doResult 			= this.showResult.bind(this);
		if(this.options.show_thumb=='auto'){
			this.options.show_thumb = window.location.toString().split("/index.php");
                        this.options.show_thumb = this.options.show_thumb[0].split("?");
                        this.options.show_thumb = this.options.show_thumb[0]  + "/index.php/showthumb";
		}
		$(this.result).set("load",{'url' : this.options.show_thumb, 'method' : 'post', 'data' : {'filename' : this.options.params.theFile},onSuccess: function(){doResult.delay(500);}}); 
		var fileQueued 			= this.fileQueued.bind(this);
		var queueError			= this.queueError.bind(this);
		var fileDialogStart		= this.fileDialogStart.bind(this);
		var fileDialogComplete          = this.fileDialogComplete.bind(this);
		var uploadStart			= this.uploadStart.bind(this);
		var uploadProgress		= this.uploadProgress.bind(this);
		var uploadError			= this.uploadError.bind(this);
		var success			= this.success.bind(this);
		var uploadFinished		= this.uploadFinished.bind(this);
		var queueCompleted 		= this.queueCompleted.bind(this);
		this.settings 			= {
			transparent		: true,
			flash_url 		: this.options.flash_url ,
			upload_url		: this.options.upload_url,			// Relative to the SWF file
			post_params		: this.options.params,
			file_post_name		: this.options.params.theFile,
			file_size_limit		: this.options.size_limit,			// Limit files to this size
			file_types 		: this.options.types,				// Restrict to these files only
			file_types_description	: "All Files",					// The appears in the file dialogin
			file_upload_limit	: this.options.upload_limit,			// Allow lots of file attempts (set to 100)
			file_queue_limit	: this.options.queue_limit,			// Only allow xx files in the que (set to one)
			debug: false,
			// Button settings
			button_image_url	: this.options.button_url,	
			button_width		: this.options.button_width,
			button_height		: this.options.button_height,
			button_placeholder_id	: obj.name + 'select',			// IMPORTANT - this section is replaced by the SWF object
				
			// The event Functions
			file_queued_handler 		: fileQueued,			// Points to this object handler
			file_queue_error_handler	: queueError,
			file_dialog_start_handler	: fileDialogStart,
			file_dialog_complete_handler 	: fileDialogComplete,
			upload_start_handler 		: uploadStart,
			upload_progress_handler 	: uploadProgress,
			upload_error_handler 		: uploadError,
			upload_success_handler  	: success,
			upload_complete_handler 	: uploadFinished,
			queue_complete_handler 		: queueCompleted					// Queue plugin event
		}
		this.swfu = new SWFUpload(this.settings);						// Create SWF Uploader object
		this.input = new Element("input",{'type' : 'text',
								 		  'name' : this.options.params.theFile});
		$(this.input).value 		= "";
		$(this.input).style.display = "none";
		$(this.input).className = $(this.object).className;									// Copy the classes Over
		$(this.input).replaces($(this.object));												// Remove the file feild from the form
	},
	queueError: function(){
		$(this.FileUploadName).set("html","Error: Could not Queue File");					// Remove the last file text 
		$(this.FileUploadName).removeClass('UploadFinished');
		$(this.FileUploadName).removeClass('UploadSuccess');
		$(this.FileUploadName).removeClass('UploadFailed');
		$(this.FileUploadName).addClass('Uploading');
		this.swfu.cancelUpload();
	},
	fileDialogStart: function(){
		$(this.FileUploadName).removeClass('UploadFinished');
		$(this.FileUploadName).removeClass('UploadSuccess');
		$(this.FileUploadName).removeClass('UploadFailed');
		$(this.FileUploadName).addClass('Uploading');
		this.swfu.cancelUpload();
	},
	queueCompleted: function(){
		$(this.FileUploadProgress).style.width = "0px";	
		$(this.FileUploadName).set("html","Upload Finished");
	},
	fileDialogComplete: function(){
		if(this.file){
			this.hideResult();
			this.startUpload();
		}else{
			$(this.FileUploadName).set("html","Select File");
		}
	},
	hideResult : function(){
		if(this.Fx != null){
			this.Fx.cancel();
			this.Fx.start('height',0);
		}
	},
	success: function(file,response){
		var location = this.FileUploadName;
		$(this.FileUploadName).removeClass('Uploading');
		$(this.FileUploadName).addClass('UploadFailed');
		$(this.FileBtn).setOpacity(0);
		$(this.FileBtn).style.display = 'none';
		$(this.FileUploadProgress).style.width = "0px";	
		$$('input[type=submit]').each(function(item,index){
				$(item).disabled=false;
			});
		switch (response) {
		case "No File":
			this.hideResult();
			$(this.FileUploadName).set("html","Error: No File Uploaded");
		 	break;
		 case "No Path":
			this.hideResult();
			$(this.FileUploadName).set("html","Error: No Folder for file Specified");
		 	break;
		 case "Too Small":
		 	this.hideResult();
		 	$(this.FileUploadName).set("html","Error: File size is less than 0");
		 	break;
		case "Too Big":
			this.hideResult();
			$(this.FileUploadName).set("html","Error: File size is Too Large");
			break;
		case "Too Long":
			$(this.FileUploadName).set("html","Error: File Name is Too Long");
			break;
		case "Not Saved":
			this.hideResult();
			$(this.FileUploadName).set("html","Error: Could not Save file");
			break;
		case "Invalid":
			this.hideResult();
			$(this.FileUploadName).set("html","Error: Invalid File Type");
			break;
		case "Error":
		default:
			this.hideResult();
			$(this.FileUploadName).set("html","Error: Upload Failed");
			if(response != "Error"){
				alert(response);
			}
			break;
		case "Success":
			var tempAmount = "";
			var amount= "" + (file.size / 1024 / 1024);					// Get the file size in MB
			if(amount.indexOf('.')!=-1){
				amount=amount.substring(0,amount.indexOf('.')+1+parseInt(3));
			}
			for(var i=0;i<amount.length;i++){
				if(!isNaN(amount.charAt(i))||amount.charAt(i)=="."){
					tempAmount+=""+amount.charAt(i);
				}
			}
			$(this.FileUploadName).removeClass('UploadFailed');
			$(this.FileUploadName).addClass('UploadSuccess');
			$(this.FileUploadName).set("html",file.name + "<br />" + tempAmount + "MB : 100%");
			$(this.FileUploadProgress).style.width = "100%";
			$(this.input).value = this.file;
			// Create folder path
			if(this.options.show_image){
				$(this.result).set("opacity",0);
				$(this.result).load();
			}
			if($(this.options.updateval)){
				$(this.options.updateval).value=this.options.params.folder+"/"+file.name;	
			}
			if($(this.result).getParent("li")){
				var par = $(this.result).getParent("li");
				var iname = file.name.replace(/ /g,'');
				var num = $$('.admin_image').length + 200;
				var spani	= new Element("span",{'id':'admin_image_new' + num});
				var img = new Element("div",{'class':'admin_image','rel':iname});
				var root = window.location.toString().split("/index.php");
				var absPath = this.options.params.folder;
				absPath = absPath.replace(/\/\//,'/');
				root    = root[0].split("?");
				absPath = root[0] + "/" + absPath + "/";		
				$(img).set("html","<img src='"+this.options.params.folder+iname+"' alt='"+iname+"' style='height:auto;width:100%;'/><div class='image_name'>"+file.name+"</div>");
				$(img).inject(spani,'top');
				$(spani).inject(par,'top');
				setupOvers(spani);
				var i_src =  "<img src='"+this.options.params.folder+iname+"' alt='"+iname+"'>";
				$$(".image_placer").each(function(item,index){
					if($(item).get("rel").test(this.options.params.folder,"i") || $(item).get("rel").test("/"+this.options.params.folder,"i") || $(item).get("rel").test(this.options.params.folder+"/","i")){
						$(item).set("html",$(item).get("html")+i_src);
					}
				},this);
			}
			
			break;
		}
		this.file = null;												// Set the file name to null
	},
	fileQueued: function(file){											// Queue and Show the file
		this.file = file.name;											// Set the file name 
		$(this.FileUploadProgress).style.width = "0px";					// Set the animation to 1px
		$(this.FileUploadName).removeClass('UploadSuccess');
		$(this.FileUploadName).removeClass('UploadFailed');
		$(this.FileUploadName).removeClass('UploadFinished');
		var tempAmount = "";
		var amount = "" + (file.size / 1024 / 1024);					// Get the file size in MB
		if(amount.indexOf('.') !=- 1){
			amount = amount.substring(0,amount.indexOf('.')+1+parseInt(3));
		}
		for(var i=0;i<amount.length;i++){
			if(!isNaN(amount.charAt(i))||amount.charAt(i)=="."){
				tempAmount +=""+amount.charAt(i);
			}
		}
		$(this.FileUploadName).set("html",file.name + "<br />" + tempAmount + "MB");
	},
	startUpload: function(){
		if(this.file){
			this.swfu.startUpload();
			$(this.result).empty();
			if(this.Fx != null){
				this.Fx.cancel();
				this.Fx.start('height',40);
			}
		}else{
			$(this.FileUploadName).set("html","Please select a file");	
		}
	},
	uploadProgress: function(file,loaded,total){
		var percentage = (loaded / total) * 100;
		var tempAmount = "";
		var amount = "" + (file.size / 1024 / 1024); // Get the file size in MB
		if (amount.indexOf('.') != -1) {
			amount = amount.substring(0, amount.indexOf('.') + 1 + parseInt(3));
		}
		for (var i = 0; i < amount.length; i++) {
			if (!isNaN(amount.charAt(i)) || amount.charAt(i) == ".") {
				tempAmount += "" + amount.charAt(i);
			}
		}
		$(this.FileUploadName).set("html", file.name + "<br />" + tempAmount + "MB : " + parseInt(((loaded / total) * 100)) + "%");
		$(this.FileUploadProgress).style.width = (percentage) + "%";
		if(this.timer){
			window.clearTimeout(this.timer);
		}
	},
	uploadStart: function(){
		$(this.FileUploadName).removeClass('UploadFinished');
		$(this.FileUploadName).removeClass('UploadSuccess');
		$(this.FileUploadName).removeClass('UploadFailed');
		$(this.FileUploadName).addClass('Uploading');
		$$('input[type=submit]').each(function(item,index){
			$(item).disabled=true;
		});
		if(this.timer){
			window.clearTimeout(this.timer);
		}
		var tch = this.checkIfFileStarted.bind(this);
		this.timer = tch.delay(5000);
		return true;
	},
	checkIfFileStarted: function(){
		window.clearTimeout(this.timer);
		alert("Flash cannot work on your network. Please use the standard upload");
	},
	uploadFinished: function(file){
		$$('input[type=submit]').each(function(item,index){
			$(item).disabled=false;
		});
		window.clearTimeout(this.timer);
	},
	uploadError : function(file, errorCode, message) {
		window.clearTimeout(this.timer);
		switch (errorCode) {
			case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
				alert("HTTP Error, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
				alert("Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.IO_ERROR:
				alert("IO Error, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
				alert("Security Error, File name: " + file.name + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				alert("Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
				alert("File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
			case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
				break;
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				break;
			default:
				alert("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
				break;
		}
	}
});
var uploaders = Array();								// Global uploaders object array
window.addEvent('domready',function(){
	$$('input[type=file]').each(function(item,index){ 	// Get all file feilds on the form
		uploaders[index] = new uploader(item);
	});
	$$('.uploadFile').each(function(item){
		uploaders.push(new uploader(item));							
	});
});