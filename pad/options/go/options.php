<?php

  pTiming_start ('opt');

  $pOptions_walk = $GLOBALS["pOptions_$pOptions"];
   
  if     ( $pOptions == 'start' ) $pContent = $pBase   [$pad];
  elseif ( $pOptions == 'end'   ) $pContent = $pResult [$pad];

  foreach ( $pPrms_tag as $pOption_name => $pad_v )

    if ( in_array ( $pOption_name, $pOptions_walk ) and ! isset ( $pDone [$pad] [$pOption_name] ) ) {

      pDone ( $pOption_name, TRUE );  

      include PAD . "options/$pOption_name.php" ;

      if ($pTrace_options)
        include 'trace';

    }

  if     ($pOptions == 'start' ) $pBase   [$pad] = $pContent;
  elseif ($pOptions == 'end'   ) $pResult [$pad] = $pContent;

  pTiming_end ('opt');

?>