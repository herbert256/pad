<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.pad') )
    $padIncPage = substr ( $padIncPage, 0, -5 );

  $padIncPHP  = padApp . "$padIncPage.php";
  $padIncHTML = padApp . "$padIncPage.pad";

  $padIncReturn = padFileGetContents ( $padIncHTML ) ;
  
  if ( padExists ($padIncPHP) )
    return "{call '$padIncPHP'}$padIncReturn{/call}";
  else
    return $padIncReturn;

?>