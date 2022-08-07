<?php

  pTiming_start ('opt');

  $pOptions_walk = $GLOBALS["pOptions_$pOptions"];
   
  if     ( $pOptions == 'start' ) $pContent = $pBase  [$p];
  elseif ( $pOptions == 'end'   ) $pContent = $pResult[$p];

  foreach ( $pPrmsTag[$p] as $pOption_name => $pV )

    if ( in_array ( $pOption_name, $pOptions_walk ) and ! isset ( $pDone[$p] [$pOption_name] ) ) {

      pDone ( $pOption_name, TRUE );  

      include PAD . "options/$pOption_name.php" ;

      if ($pTrace)
        include 'trace';

    }

  if     ($pOptions == 'start' ) $pBase  [$p] = $pContent;
  elseif ($pOptions == 'end'   ) $pResult[$p] = $pContent;

  pTiming_end ('opt');

?>