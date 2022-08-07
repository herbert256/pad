<?php
    
  $pNs = strpos($pTag[$pT], ':');

  if ( $pNs ) {

    $pType[$pT] = substr ($pTag[$pT], 0, $pNs_pos);
    $pTag[$pT]  = substr ($pTag[$pT], $pNs_pos+1);

    if ( ! file_exists ( PAD . "types/$pType.php" ) ) 
      $pType[$pT] = FALSE;
    
  } else

    $pType[$pT] = pGet_type_lvl ( $pTag[$pT] );

?>