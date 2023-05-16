<?php
  
  $padIncPage = padPageGetName ();
  $padCall    = padApp . "$padIncPage.php";
  $padIncRet  = include pad . 'build/go.php';
  $padIncRet .= padFileGetContents ( padApp . "$padIncPage.html" );
      
  return $padIncRet;

?>