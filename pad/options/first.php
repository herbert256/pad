<?php

  $padFilterFirst = $padPrmsTag [$pad] ['first'];
  if ( ! $padFilterFirst )
    $padFilterFirst = 1;

	$padFilterStart = 1;
	$padFilterEnd   = ($padFilterStart + $padFilterFirst) - 1;
     
  padDone ( 'first', TRUE);      
	padDataFilterGo ($padData [$pad], $padFilterStart, $padFilterEnd);    

?>