<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.html') )
    $padIncPage = substr ( $padIncPage, 0, -5 );

  $padCall    = padApp . "$padIncPage.php";
  $padIncRet  = include pad . 'build/go.php';
  
  $padIncRet .= padFileGetContents ( padApp . "$padIncPage.html" );
      
  return $padIncRet;

?>