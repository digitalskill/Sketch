<?php
function plugincmp( $a, $b ) {
    global $sketch;
    return ( $sketch->plugins[ $a ]->getOrder() > $sketch->plugins[ $b ]->getOrder() ) ? 1 : 0;
}
class ADMIN extends PLUGIN {
    function ADMIN( $args ) {
        $settings = array(
             "location" => "end",
            "global" => 0,
            "php" => 1,
            "pluginsection" => "pageedit",
            "noform" => 0
        );
        $this->start( $settings, $args );
    }
    function update( $old, $new ) {
        global $_POST, $sketch;
        if ( isset( $_POST[ 'order' ] ) ) {
            $updates = explode( ";", $_POST[ 'order' ] );
            foreach ( $updates as $key => $value ) {
                @list( $id, $ord ) = @explode( ":", $value );
                if ( is_numeric( $id ) && is_numeric( $ord ) ) {
                    $SQL = "UPDATE " . $sketch->settings[ 'prefix' ] . "plugin " . "SET admin_order = '" . intval( $ord ) . "' " . "WHERE plugin_id=" . intval( $id );
                    $r   = ACTIVERECORD::keeprecord( $SQL );
                }
            }
            exit( );
        }
        return $new;
    }
    function display( $args = '' ) {
        global $sketch;
        foreach ( $sketch->pluginArgs as $key => $value ) {
            $sketch->registerPlugin( $value );
        }
        $this->settings[ 'updates' ] = $sketch->plugins[ 'pageedit' ]->get_plugin_id();
        $allp                        = $sketch->plugins;
        uksort( $allp, 'plugincmp' );
?>
       <div id="admin_panel" class="hide">
              <div class="admin-controls">
                <div id="admin-tabMenu"><?php
        $count = 0;
        foreach ( $allp as $key => $value ) {
            if ( $sketch->plugins[ $key ]->candoform() == true && $sketch->plugins[ $key ]->topNav() == true && $sketch->plugins[ $key ]->superUser() == false ) {
                $sketch->thisPagePlugins( strtolower( $key ) );
?>
                           <a rel="<?php
                echo $sketch->plugins[ $key ]->settings[ 'plugin_id' ];
?>" href="<?php
                echo urlPath( "admin" );
?>/ajax_plugin_<?php
                echo $key;
?>?page_id=<?php
                echo $sketch->page_id;
?>&amp;preview=" class="expose:'<?php
                echo trim( stripslashes( $sketch->plugins[ $key ]->getSection() ) );
?>'" ><span><?php
                echo $sketch->plugins[ $key ]->menuName( $key );
?></span></a>
          <?php
                $count++;
            }
        }
?>
       
        </div>
        <div id="admin-tabMenuRight">
       		<form class="required" style="float:left;width:100px;margin-right:10px;overflow:visible;position:relative;margin-top:-1px;z-index:999;height:23px">
                <select name="menu_under" class="bgClass:'select_bg'" style="-webkit-appearance:textarea;" id="pagejumper" onchange="if(this.value != ''){ window.location ='<?php echo urlPath(); ?>?page_id=' + this.value;}">
                <option value="">Jump to page</option>
                <?php adminFilter("menu",array("select"=>true)); ?>
                </select>
            </form>
            <a href="<?php echo urlPath( "?logout=true" ); ?>" class="logout:true"><span>Logout</span></a></div>
  <div id="sub-nav" class="hide">
      <div class="subnavmid">
        <div class="assetbutn">
        <a href="<?php echo urlPath( "admin" );?>/ajax_plugin_images?page_id=<?php echo $sketch->page_id;?>&amp;preview=preview" class="image:true" id="assetlink" onclick="$('helplink').toggleClass('hide'); if($('helplink').hasClass('hide')){ $('helplink').fade('out'); }else{$('helplink').fade('in');}"><span>Assets</span></a>
    	</div>
    	<div class="helpbutn">
        	<a href="<?php echo urlPath();?>/userhelp?page_id=<?php echo $sketch->page_id;?>" class="image:true" id="helplink" onclick="$('assetlink').toggleClass('hide'); if($('assetlink').hasClass('hide')){ $('assetlink').fade('out'); }else{$('assetlink').fade('in');}"><span>Help</span></a>
    	</div>
      <?php
        $section     = "";
        $allSections = array( );
        foreach ( $allp as $key => $value ) {
            $section                    = $sketch->plugins[ $key ]->getSection();
            $allSections[ $section ][ ] = $key;
        }
        foreach ( $allSections as $key => $value ) {
?>
       <div id="section_<?php
            echo $key;
?>" class="hide" style="float:left;"><?php
            $menuSectionHTML = "<span class='asortable'>";
            foreach ( $value as $k => $v ) {
                if ( $sketch->plugins[ $v ]->candoform() == true && $sketch->plugins[ $v ]->superUser() == false && ( $sketch->checkIfLoaded( strtolower( $v ) ) == true || $sketch->plugins[ $v ]->showIfAdmin() == true ) ) {
                    if ( $sketch->plugins[ $v ]->topNav() != true ) {
                        $menuSectionHTML .= '<a rel="' . $sketch->plugins[ $v ]->settings[ 'plugin_id' ] . '" href="' . urlPath( "admin" ) . '/ajax_plugin_' . $v . '?page_id=' . $sketch->page_id . '&amp;preview=" class="' . $sketch->plugins[ $v ]->getAdminclass() . ' ' . $v . 'edit">' . $sketch->plugins[ $v ]->menuName( $v ) . '</a>';
                    } else {
?>
                   <a rel="" href="<?php
                        echo urlPath( "admin" );
?>/ajax_plugin_<?php
                        echo $v;
?>?page_id=<?php
                        echo $sketch->page_id;
?>&preview=" class="<?php
                        echo $sketch->plugins[ $v ]->getAdminclass();
?>"><?php
                        echo $sketch->plugins[ $v ]->menuName( $v );
?></a>
          <?php
                    }
                }
            }
            echo $menuSectionHTML . "&nbsp;</span>";
?>
       </div>
        <?php
        }
?>
   </div>
 </div>
</div>
</div>
<script type="text/javascript">
    function sortAdminMenu(){
        new Sorter($("admin-tabMenu"),{"url":"<?php
        echo urlPath( "admin" );
?>/plugin_<?php
        echo $this->settings[ 'name' ];
?>"});
        $$(".asortable").each(function(item,index){
            new Sorter($(item),{"url":"<?php
        echo urlPath( "admin" );
?>/plugin_<?php
        echo $this->settings[ 'name' ];
?>?menuorder","clone":false});
        });
    };
    sortAdminMenu.delay(1000);
    <?php
        if ( isset( $_REQUEST[ 'e' ] ) ) {
?>
       window.addEvent("load",function(){
            $("admin-tabMenu").getElements("a").each(function(item,index){
                if($(item).get("class").contains("pageedit")){
                    $(item).fireEvent("click");
                }
            });
        });
<?php
        }
?>
</script>
<?php
    }
    function preview( ) {
        $this->display();
    }
    function form( ) {
    }
}