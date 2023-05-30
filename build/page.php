<?php

  $padBuildHtml  = ''
  $padBuildArray = [];
  $padBuildPath  = padApp . $padPage;
  $padBuildCall  = include pad . 'build/call.php';

  $padBuildHtml  = $padBuildOB . $padBuildHtml . padFileGetContents ( "$padBuildPath.html" );

  padTrueFalse ( $padBuildHtml , $padBuildTrue, $padBuildFalse );

  if     ( $padBuildCall === TRUE  ) $padBuildPage = $padBuildTrue;
  elseif ( $padBuildCall === FALSE ) $padBuildPage = $padBuildFalse;
  elseif ( $padBuildCall === NULL  ) $padBuildPage =  '';
  else                               $padBuildPage = $padBuildCall . $padBuildTrue;

  if ( count ( $padBuildArray ) ) 
    include pad . 'build/array.php';

  return $padBuildPage;

?>