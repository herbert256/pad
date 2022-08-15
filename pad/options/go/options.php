<?php

  padTimingStart ('opt');

  $padOptionsWalk = $GLOBALS [ 'padOptions' . ucfirst($padOptions) ];
   
  if     ( $padOptions == 'start' ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'   ) $padContent = $padResult [$pad];

  foreach ( $padPrmsTag [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! isset ( $padDone [$pad] [$padOptionName] ) ) {

      padDone ( $padOptionName, TRUE );  

      include PAD . "options/$padOptionName.php" ;

      if ($padTrace)
        include 'trace.php';

    }

  if     ( $padOptions == 'start' ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'   ) $padResult [$pad] = $padContent;

  padTimingEnd ('opt');

?>