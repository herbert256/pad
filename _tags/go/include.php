<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.html') )
    $padIncPage = substr ( $padIncPage, 0, -5 );

  $padIncReturn  = "{call '" . padApp . "$padIncPage.php" . "'}";
  $padIncReturn .= padFileGetContents ( padApp . "$padIncPage.html" );

  $padIncReturn = str_replace ('@contect@', $padContent, $padIncReturn);
  
  return $padIncReturn;

?>