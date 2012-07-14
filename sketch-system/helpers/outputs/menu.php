<?php
    $html = '<ul>';
    foreach ($this->menuData['parents'][intval($parentid)] as $itemId) {
		$c = (in_array($itemId, $this->currents)) ? 'selected' : '';
		if ($this->datalookup[$itemId]['menu_show'] == 1) {
			$html .= '<li class="' . $c . ' ' . $this->datalookup[$itemId]['menu_class'] . '">' . $this->menuData['items'][$itemId];
			$html .= $this->buildMenu($itemId);
			$html .= '</li>';
		}
    }
    $html .= '</ul>';
	if($html=="<ul></ul>"){
		$html = "";	
	}

// FOR DREAMWEAVER PREVIEW
if(false){
	?>
<ul>
  <li class="selected home"><a class="" href=" ">Home</a></li>
  <li class=" "><a class="" href=" ">Products</a></li>
  <li class=" "><a class="" href=" ">Gallery</a>
    <ul style=" ">
      <li class=" "><a class="" href=" ">All</a></li>
      <li class=" "><a class="" href=" ">Web Design</a></li>
      <li class=" "><a class="" href=" ">Video</a></li>
      <li class=" "><a class="" href=" ">Illustration</a></li>
      <li class=" "><a class="" href=" ">Photography</a></li>
    </ul>
  </li>
  <li class=" "><a class="" href=" ">Contact us</a>
    <ul style=" ">
      <li class=" "><a class="" href=" ">Our Address</a></li>
    </ul>
  </li>
</ul>
<?php
}
?>
