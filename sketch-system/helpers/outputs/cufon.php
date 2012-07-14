<script type="text/javascript">
    function SetUpCufon(){
<?php
for ($i = 1; $i < (intval($this->e("amount")) + 1); $i++) {
    $allClasses = explode(",", $this->e('class' . $i));
    foreach ($allClasses as $key => $value) {
	$value = trim($value);
	if ($value != "") {
?>
	    	$$('<?php echo $value; ?>').each(function(item){
	    	    var options = {hover : false,fontFamily: "<?php echo $this->e("family" . $i); ?>"<?php if($this->e("extras".$i) != ""){ echo ",".$this->e("extras".$i); } ?>};
	    	    if($(item).get("tag") == "a" || '<?php echo $value; ?>'.contains("menu")){
					options.hover = true;
				}
	    	    try{Cufon.replace(item, options);}catch(e){}
	    	});
<?php
	}
    }
}
?>
	try{Cufon.now();}catch(e){}
    }
    window.addEvent("domready",SetUpCufon);
</script>