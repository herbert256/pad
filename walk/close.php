<?php

  foreach ($pad_close_tags as $pad_tag) {
  
  	$pad_tag_type = pad_get_type_lvl ( $pad_tag );

  	$pad_between = $pad_tag;
  	include PAD_HOME . 'level/parms1.php';
 	include PAD_HOME . 'level/parms2.php';

  	$pad_walk = 'close';
 
  	include PAD_HOME . 'level/type.php';

  }

?>