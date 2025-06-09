<?php

  if     ( $padOptions == 'app' ) $padOptionsWalk = $padOptionsAppStart [$pad];
  else                            $padOptionsWalk = constant ( 'padOptions' . ucfirst($padOptions) );
   
  if     ( $padOptions == 'start'    ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'      ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'app'      ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'callback' ) $padContent = $padResult [$pad];
 
  foreach ( $padPrm [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! padIsDone ( $padOptionName ) ) {

      if ( $padOptions <> 'callback' )
        padDone ( $padOptionName );  
 
      if ( $padOptions == 'app' )
        $padCall = APP . "_options/$padOptionName.php" ;
      else
        $padCall = "options/$padOptionName.php" ;

      include 'call/any.php';

    }

  if     ( $padOptions == 'start'    ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'      ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'app'      ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'callback' ) $padResult [$pad] = $padContent;

?>