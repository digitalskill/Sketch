<?php
session_start();
session_destroy();

if(!isset($_REQUEST['ajax'])){
	header("location: ../index.php");
}else{ ?>
<script type="text/javascript">
	window.location=window.location;
</script>
<?php
}
die();