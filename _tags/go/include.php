<?php
  
  if ( str_ends_with ( $padIncPage, '.php') )
    $padIncPage = substr ( $padIncPage, 0, -4 );
  
  if ( str_ends_with ( $padIncPage, '.html') )
    $padIncPage = substr ( $padIncPage, 0, -5 );

  return "{call '" . padApp . "$padIncPage.php" . "'}" . padFileGetContents ( padApp . "$padIncPage.html" );

?>