<?php

  foreach ($pad_close_tags as $pad_tag) {
  
  	$pad_tag_type = pad_get_type_lvl ( $pad_tag );

    $pad_between = $pad_tag;
    include PAD . 'level/parms1.php';
    include PAD . 'level/parms2.php';

  	$pad_walk = 'close';
  	include PAD . 'level/type.php';

  }

?>