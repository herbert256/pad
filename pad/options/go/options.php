<?php

  if     ( $padOptions == 'app' ) $padOptionsWalk = $padOptionsAppStart [$pad];
  else                            $padOptionsWalk = constant ( 'padOptions' . ucfirst($padOptions) );

  if     ( $padOptions == 'start'    ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'      ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'app'      ) $padContent = $padBase   [$pad];

  foreach ( $padPrm [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! padIsDone ( $padOptionName ) ) {

      $padGetName = padTagParm ( $padOptionName, '???' );

      padDone ( $padOptionName );

      if ( $padOptions == 'app' )
        $padCall = APP . "_options/$padOptionName.php" ;
      else
        $padCall = PAD . "options/$padOptionName.php" ;

      include PAD . 'call/any.php';

    }

  if     ( $padOptions == 'start'    ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'      ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'app'      ) $padBase   [$pad] = $padContent;

?>