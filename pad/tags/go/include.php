<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.pad') )
    $padIncPage = substr ( $padIncPage, 0, -4 );

  $padIncPHP  = padApp . "$padIncPage.php";
  $padIncPAD = padApp . "$padIncPage.pad";

  $padIncReturn = padFileGetContents ( $padIncPAD ) ;
  
  if ( file_exists ($padIncPHP) )
    return "{call '$padIncPHP'}$padIncReturn{/call}";
  else
    return $padIncReturn;

?>