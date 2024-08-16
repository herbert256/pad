<?php
 
  $padOpenClose = padOpenCloseList ( $padBase [$pad] ) ;
  
  if ( $padGiven [$pad] )
    $padOpenClose [ $padType [$pad] . ':' . $padTag [$pad] ] = TRUE;
  else
    $padOpenClose [ $padTag [$pad] ] = TRUE;

  $padPos = strpos ( $padBase [$pad], '@else@');

  while ( $padPos !== FALSE) {
    
    if  ( padOpenCloseCount ( substr ( $padBase [$pad], 0, $padPos ), $padOpenClose) ) {
      $padFalse = substr ( $padBase [$pad], $padPos+6  );
      $padBase  [$pad] = substr ( $padBase [$pad], 0, $padPos );
      if ( $GLOBALS ['padInfo'] ) 
        include '/pad/info/events/else.php';
      return;
    }

    $padPos = strpos ( $padBase [$pad], '@else@', $padPos+1);

  }

?>