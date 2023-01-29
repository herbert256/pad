<?php

  padTimingStart ('opt');

  $padOptionsWalk = $GLOBALS [ 'padOptions' . ucfirst($padOptions) ];
   
  if     ( $padOptions == 'start' ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'   ) $padContent = $padResult [$pad];

  foreach ( $padPrm [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! isset ( $padDone [$pad] [$padOptionName] ) ) {

      padDone ( $padOptionName, TRUE );  

      include PAD . "pad/options/$padOptionName.php" ;

      if ( $padOptionName <> 'trace' and $padTrace )
        include 'trace.php';

    }

  if     ( $padOptions == 'start' ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'   ) $padResult [$pad] = $padContent;

  padTimingEnd ('opt');

?>