<?php

  if ( $padOptions == 'app' )
    $padOptionsWalk = $padOptionsApp [$pad];
  else
    $padOptionsWalk = $GLOBALS [ 'padOptions' . ucfirst($padOptions) ];
   
  if     ( $padOptions == 'start' ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'   ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'app'   ) $padContent = $padBase   [$pad];

  foreach ( $padPrm [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! isset ( $padDone [$pad] [$padOptionName] ) ) {

      padDone ( $padOptionName, TRUE );  

    if ( $padOptions == 'app' )
      include padApp . "options/$padOptionName.php" ;
    else
      include pad . "options/$padOptionName.php" ;

    }

  if     ( $padOptions == 'start' ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'   ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'app'   ) $padBase   [$pad] = $padContent;

?>