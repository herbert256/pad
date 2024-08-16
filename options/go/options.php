<?php

  if     ( $padOptions == 'app' ) $padOptionsWalk = $padOptionsAppStart [$pad];
  else                            $padOptionsWalk = constant ( 'padOptions' . ucfirst($padOptions) );
   
  if     ( $padOptions == 'start'    ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'      ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'app'      ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'callback' ) $padContent = $padResult [$pad];
 
  foreach ( $padPrm [$pad] as $padOptionName => $padV ) {

    if ( in_array ( $padOptionName, $padOptionsWalk ) )

      if ( $padOptions == 'callback' or ! isset ( $padDone [$pad] [$padOptionName] ) ) {

        padDone ( $padOptionName, TRUE );  
   
        if ( $padOptions == 'app' )
          $padCall = "/app/_options/$padOptionName.php" ;
        else
          $padCall = "/pad/options/$padOptionName.php" ;

        include '/pad/call/call.php';

        if ( $GLOBALS['padInfo'] )
          include '/pad/info/events/options.php';           

      }

  }

  if     ( $padOptions == 'start'    ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'      ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'app'      ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'callback' ) $padResult [$pad] = $padContent;

?>