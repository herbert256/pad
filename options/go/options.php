<?php

  if ( $padParse )
    return;

  padTimingStart ('opt');

  if ( $padOptions == 'padApp' )
    $padOptionsWalk = $padOptionsApp [$pad];
  else
    $padOptionsWalk = $GLOBALS [ 'padOptions' . ucfirst($padOptions) ];
   
  if     ( $padOptions == 'start' ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'   ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'padApp'   ) $padContent = $padBase   [$pad];

  foreach ( $padPrm [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! isset ( $padDone [$pad] [$padOptionName] ) ) {

      padDone ( $padOptionName, TRUE );  

    if ( $padOptions == 'padApp' )
      include padApp . "options/$padOptionName.php" ;
    else
      include pad . "options/$padOptionName.php" ;

      if ( $padOptionName <> 'trace' and $padTrace )
        include 'trace.php';

    }

  if     ( $padOptions == 'start' ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'   ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'padApp'   ) $padBase   [$pad] = $padContent;

  padTimingEnd ('opt');

?>