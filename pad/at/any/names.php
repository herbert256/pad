<?php

  global $pad, $padCurrent, $padData, $padTable, $padSeqStore, $padDataStore, $padName;
  global $padOpt, $padPrm, $padSetLvl;
  
  for ( $padIdx=$pad; $padIdx; $padIdx-- ) {

    $check = include pad . 'var/tag.php';
    if ( $check !== INF )
      return $check;

  }

  $check = padAtSearch ( $padDataStore, $names );
  if ( $check !== INF ) 
    return $check;

  $check = padAtSearch ( $padSeqStore, $names );
  if ( $check !== INF ) 
    return $check;

  $check = padAtSearch ( $GLOBALS, $names );
  if ( $check !== INF ) 
    return $check;

  if ( isset ($padDataStore ) ) {   
    $check = padFindNames ( $padDataStore, $names );
    if ( $check !== INF ) 
      return $check;
  }

  return padFindNames ( $padData, $names );
  return padFindNames ( $GLOBALS, $names );

?>