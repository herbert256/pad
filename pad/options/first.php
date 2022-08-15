<?php

  $padFilter_first = $padPrmsTag [$pad] ['first'];
  if ( ! $padFilter_first )
    $padFilter_first = 1;

	$padFilter_start = 1;
	$padFilter_end   = ($padFilter_start + $padFilter_first) - 1;
     
  padDone ( 'first', TRUE);      
	padDataFilterGo ($padData [$pad], $padFilter_start, $padFilter_end);    

?>