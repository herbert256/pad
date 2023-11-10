<?php
 
  $padPadStart [$pad] = $padTrue [$pad];

  $padOpenClose = padOpenCloseList ( $padTrue [$pad] ) ;
  
  if ( $padGiven [$pad] )
    $padOpenClose [ $padType [$pad] . ':' . $padTag [$pad] ] = TRUE;
  else
    $padOpenClose [ $padTag [$pad] ] = TRUE;

  $padPos = strpos ( $padTrue [$pad], '{else}');

  while ( $padPos !== FALSE) {
    
    if  ( padOpenCloseCount ( substr ( $padTrue [$pad], 0, $padPos ), $padOpenClose) ) {
      $padFalse [$pad] = substr ( $padTrue [$pad], $padPos+6  );
      $padTrue  [$pad] = substr ( $padTrue [$pad], 0, $padPos );
      return;
    }

    $padPos = strpos ( $padTrue [$pad], '{else}', $padPos+1);

  }

?>