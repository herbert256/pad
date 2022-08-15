<?php

  pTiming_start ('opt');

  $padOptions_walk = $GLOBALS["padOptions_$padOptions"];
   
  if     ( $padOptions == 'start' ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'   ) $padContent = $padResult [$pad];

  foreach ( $padPrmsTag [$pad] as $padOption_name => $padV )

    if ( in_array ( $padOption_name, $padOptions_walk ) and ! isset ( $padDone [$pad] [$padOption_name] ) ) {

      pDone ( $padOption_name, TRUE );  

      include PAD . "options/$padOption_name.php" ;

      if ($padTrace)
        include 'trace.php';

    }

  if     ($padOptions == 'start' ) $padBase   [$pad] = $padContent;
  elseif ($padOptions == 'end'   ) $padResult [$pad] = $padContent;

  pTiming_end ('opt');

?>