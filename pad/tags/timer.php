<?php

  if ( $pad_walk == 'start' ) {
  	$timer = microtime(true);
    $pad_walk = 'end';
  } else {
  	$timer = microtime(true) - $timer;  	
  }

  return TRUE;

?>