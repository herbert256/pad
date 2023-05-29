<?php

  $padBuildTmp  = '';
  $padOpenClose = padOpenCloseList ( $padBuildTrue ) ;
  $padPos       = strpos ( $padBuildTrue , '{else}');

  while ( $padPos !== FALSE) {
    
    if  ( padOpenCloseCount ( substr ( $padBuildTrue , 0, $padPos ), $padOpenClose) ) {
      $padBuildTmp  = substr ( $padBuildTrue, $padPos+6  );
      $padBuildTrue = substr ( $padBuildTrue, 0, $padPos );
      return $padBuildTmp;
    }

    $padPos = strpos ( $padBuildTrue, '{else}', $padPos+1);

  }

  return $padBuildTmp;

?>