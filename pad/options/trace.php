<?php
      
  if ( $pad_options = 'level_start' ) {
    $pad_walk_save = $pad_walk;
    $pad_walk = 'start';
    include PAD . 'tags/trace.php';
    $pad_walk = $pad_walk_save;
  }
 
  if ( $pad_options = 'level_end' ) {
    $pad_walk_save = $pad_walk;
    $pad_walk = 'end';
    include PAD . 'tags/trace.php';
    $pad_walk = $pad_walk_save;
  }

?>