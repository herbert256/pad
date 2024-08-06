<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.pad') )
    $padIncPage = substr ( $padIncPage, 0, -4 );

  $padIncPHP = padApp . "$padIncPage.php";
  $padIncPAD = padApp . "$padIncPage.pad";

  if ( ! file_exists ($padIncPHP) )
    return padFileGetContents ( $padIncPAD );
  else
    return "{call '$padIncPHP'}" . padFileGetContents ( $padIncPAD ) . '{/call}';

?>