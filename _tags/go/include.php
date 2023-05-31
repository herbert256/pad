<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.html') )
    $padIncPage = substr ( $padIncPage, 0, -5 );

  $padIncPHP  = padApp . "$padIncPage.php";
  $padIncHTML = padApp . "$padIncPage.html";

  $padIncReturn = padFileGetContents ( $padIncHTML ) ;
  
  if ( padExists ($padIncPHP) )
    return "{call '$padIncPHP'}$padIncReturn{/call}";
  else
    return $padIncReturn;

?>