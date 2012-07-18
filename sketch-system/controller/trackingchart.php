<?php
class TRACKINGCHART extends CONTROLLER{
  function TRACKINGCHART($page){
    parent::__construct("trackingchart");
	global $_POST;
	if(!adminCheck()){
		header("location: ".urlPath());
		die();
	}
	$search  = isset($_POST['page_type']) && $_POST['page_type'] != '' ? " AND page_type=".sketch("db")->quote($_POST['page_type']) : "";
	
	if(isset($_POST['smonth'])){
		$sdate   = date("Y-m-d",mktime(0,0,0,intval($_POST['smonth']),1,intval($_POST['syear'])));
		$edate	 = date("Y-m-d",mktime(0,0,0,intval($_POST['emonth']),31,intval($_POST['eyear'])));
		$search .= " AND dateviewed BETWEEN ".sketch("db")->quote($sdate)." AND ".sketch("db")->quote($edate); 
	}
	
	$range   = ", MIN( dateviewed ) AS dateviewed, MAX( dateviewed ) AS lastdateviewed";
	$SQL = "SELECT ".getSettings("prefix")."sketch_menu. * , SUM( viewcount ) AS viewcount ".$range ."  
			FROM ".getSettings("prefix")."sketch_views, ".getSettings("prefix")."sketch_menu,".getSettings("prefix")."sketch_page
			WHERE ".getSettings("prefix")."sketch_views.page_id = ".getSettings("prefix")."sketch_menu.page_id
			AND ".getSettings("prefix")."sketch_views.page_id = ".getSettings("prefix")."sketch_page.page_id
			". $search ."
			GROUP BY ".getSettings("prefix")."sketch_menu.page_id
			ORDER BY dateviewed DESC";
	$r = ACTIVERECORD::keeprecord($SQL);
	$qu = $r->query;
	
	?>
    <html>
  <head>
   <title>Vertical bar chart</title>
   <script language="javascript" src="http://www.google.com/jsapi"></script>
   <?php getStylePath(); ?>
   </head>
   <body style="margin:0 !important; padding:0 !important;height:auto;width:100%;background:#fff">
   <div id="chart" style="margin:auto;width:800px;margin-top:20px"></div>
   <script type="text/javascript">
      var queryString = '';
      var dataUrl = '';
	  var w = window.innerWidth;
	  var h = window.innerHeight - 120;
      function onLoadCallback() {
		 w = window.innerWidth;
		 h = window.innerHeight - 120;
        if (dataUrl.length > 0) {
          var query = new google.visualization.Query(dataUrl);
          query.setQuery(queryString);
          query.send(handleQueryResponse);
        } else {
          var dataTable = new google.visualization.DataTable();
          dataTable.addRows(<?php echo intval($r->rowCount()); ?>);
		  dataTable.addColumn('number');
		  <?php 
		  $counter = 0;
		  $bignum  = 0;
		  $chdl	   = array();
		  $chd     = "";
		  $dvmin   = "";
		  $dvEnd   = "";
		  while($r->advance()){ 
		  	$bignum = $bignum < $r->viewcount ? $r->viewcount : $bignum;
			$chdl[]	= htmlentities($r->menu_name);
			$chd	.= ",".intval($r->viewcount);
			$dvmin   = $dvmin > strtotime($r->dateviewed) 		|| $dvmin == ""? strtotime($r->dateviewed) : $dvmin;
			$dvEnd   = $dvEnd < strtotime($r->lastdateviewed) 	|| $dvEnd=="" ? strtotime($r->lastdateviewed) : $dvEnd;
		  ?>
          	dataTable.setValue(<?php echo $counter; ?>, 0,<?php echo intval($r->viewcount); ?>);
		  <?php 
		  	$counter++;
		  } 
		  
		  $chdl = implode("|", array_reverse($chdl));
		  ?>
          draw(dataTable);
        }
      }

      function draw(dataTable) {
        var vis = new google.visualization.ImageChart(document.getElementById('chart'));
        var options = {
          chxl: '1:|<?php echo $chdl; ?>|',
          chxp: '',
          chxr: '0,0,<?php echo $bignum; ?>',
          chxs: '',
          chxtc: '',
          chxt: 'x,y',
          chs: '800x400',
          cht: 'bhs',
          chco: '008000',
          chd: 's:QdoW',
          chdl: 'Page views',
          chtt: '<?php echo date("M j Y",$dvmin)." - ".date("M j Y",$dvEnd); ?>'
        };
        vis.draw(dataTable, options);
      }

      function handleQueryResponse(response) {
        if (response.isError()) {
          alert('Error in query: ' + response.getMessage() + ' ' + response.getDetailedMessage());
          return;
        }
        draw(response.getDataTable());
      }
      google.load("visualization", "1", {packages:["imagechart"]});
      google.setOnLoadCallback(onLoadCallback);

    </script>
    <form action="" method="post" target="_self" style="clear:both;width:800;margin:auto">
    	<ul class="forms">
        	<li style="float:left;width:48%">
            <label>Start Month and Year</label>
            <div style="width:30%; float:left">
            	<select name="smonth">
            		<option value="<?php echo date("m"); ?>"><?php echo date("M"); ?></option>
                    <?php for($i =1;$i < 12;$i++){ ?>
                    <option value="<?php echo date("m")-$i; ?>" <?php if(isset($_POST['smonth']) && $_POST['smonth']==date("m")-$i){?>selected="selected"<?php } ?>><?php echo date("M",mktime(0,0,0,date("m") - $i,date("d"),date("Y"))); ?></option>
                    <?php } ?>
            	</select>
            </div>
            <div style="width:30%; float:left">
            	<select name="syear">
            		<option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                    <?php for($i =1;$i < 12;$i++){ ?>
                    <option value="<?php echo date("Y")-$i; ?>" <?php if(isset($_POST['syear']) && $_POST['syear']==date("Y")-$i){?>selected="selected"<?php } ?>><?php echo date("Y") - $i; ?></option>
                    <?php } ?>
            	</select>
            </div>
            <div style="width:28%; float:left">
            	<select name="page_type">
                	<?php $r = getData("sketch_page","page_type","WHERE page_status='published' GROUP BY page_type"); ?>
            		<option value="">All Page Types</option>
                    <?php while($r->advance()){ ?>
                    	<option value="<?php echo $r->page_type; ?>" <?php if(isset($_POST['page_type']) && $_POST['page_type']==$r->page_type){?>selected="selected"<?php } ?>><?php echo $r->page_type; ?></option>	
                    <?php } ?>
            	</select>
            </div>
            </li>
            <li style="float:left;clear:both;width:48%">
            <label>End Month and Year</label>
            <div style="width:30%; float:left">
            	<select name="emonth">
            		<option value="<?php echo date("m"); ?>"><?php echo date("M"); ?></option>
                    <?php for($i =1;$i < 12;$i++){ ?>
                    <option value="<?php echo date("m")-$i; ?>" <?php if(isset($_POST['emonth']) && $_POST['emonth']==date("m")-$i){?>selected="selected"<?php } ?>><?php echo date("M",mktime(0,0,0,date("m") - $i,date("d"),date("Y"))); ?></option>
                    <?php } ?>
            	</select>
            </div>
            <div style="width:30%; float:left">
            	<select name="eyear">
            		<option value="<?php echo date("Y"); ?>"><?php echo date("Y"); ?></option>
                     <?php for($i =1;$i < 12;$i++){ ?>
                    <option value="<?php echo date("Y")-$i; ?>" <?php if(isset($_POST['eyear']) && $_POST['eyear']==date("Y")-$i){?>selected="selected"<?php } ?>><?php echo date("Y") - $i; ?></option>
                    <?php } ?>
            	</select>
            </div>
             <div style="width:28%; float:left">
             	<button type="submit" style="margin:0">Get Tracking Data</button>
             </div>
            </li>
            <li>
        </ul>
    </form>
    <div style='clear:both'></div>
  </body>
</html>
 <?php
  }
}