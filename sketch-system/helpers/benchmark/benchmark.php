<?php
/*
 * Benchmark
 * Created By Kevin Dibble, (c) 2010
 * Part of the sketch framework
 * 
 * Purpose - to output a benchmark time in seconds for the sketch framework
 */
if(!function_exists("showBenchmark")){
	function showBenchmark(){
		$dat['microtime']	= sketch("startTime");?>
      	<script type="text/javascript">
		try{
			console.info("PHP Processing time:  <?php echo number_format((microtime(true)-$dat['microtime']),3); ?> seconds");
			<?php if(function_exists("memory_get_peak_usage")){ ?>
				console.info("Peak PHP Memory used: <?php echo number_format(memory_get_peak_usage()/1048576,3); ?> MB"); 
			<?php } ?>
			console.info("Benchmarks Called: <?php echo count(sketch("queryAmounts"));?>");
			<?php
				foreach(sketch("queryAmounts") as $key => $value){?>
					console.log("Item <?php echo $key; ?> took:<?php echo number_format($value[2]-$value[1],3); ?> seconds. | <?php if(sketch("isAdminLoggedIn") || strpos($_SERVER['HTTP_HOST'],"localhost")!==false){?><?php echo str_replace(array("\r\n","\r","\n","  ",'"')," ",trim($value[0])); ?><?php } ?>");
		<?php 	} ?>
		}catch(e){}
		</script>
	<?php
	}
}