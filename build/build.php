<?php

  include pad . 'build/dirs.php';

  $padBuildLib = include pad . 'build/lib.php';  

  if ( isset ($padInclude) or isset ( $_REQUEST ['padInclude'] ) ) 
    $padBuildBase = '@pad@';
  else 
    $padBuildBase = include pad . 'build/base.php';

  $padBuildPath = padApp . $padPage;
  $padBuildHtml = padFileGetContents ( "$padBuildPath.html" );
  $padBuildCall = include pad . 'build/call.php';

  $padBuildTrue  = $padBuildHtml;
  $padBuildFalse = include pad . 'build/false.php';

  if     ( $padBuildCall === TRUE  ) $padBuild = $padBuildOB . $padBuildTrue;
  elseif ( $padBuildCall === FALSE ) $padBuild = $padBuildOB . $padBuildFalse;
  elseif ( $padBuildCall === NULL  ) $padBuild = '';
  else                               $padBuild = $padBuildOB . $padBuildCall . $padBuildTrue;

  if ( count ( $padBuildArray ) ) 
    include pad . 'build/array.php';
 
  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuild, $padBuildBase );;

  include pad . 'occurrence/start.php';
   
?>