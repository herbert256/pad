<?php
    
  $pNs = strpos($pTag [$pN], ':');

  if ( $pNs ) {

    $pType [$pN] = substr ($pTag [$pN], 0, $pNs);
    $pTag  [$pN] = substr ($pTag [$pN], $pNs+1);

    if ( ! file_exists ( PAD . "types/$pType[$pN].php" ) ) 
      $pType [$pN] = FALSE;
    
  } else

    $pType [$pN] = pGet_type_lvl ( $pTag [$pN] );

  return $pType [$pN];  

?>