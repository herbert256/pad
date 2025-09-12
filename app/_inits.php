<?php

  $title = $padPage;

  if ( isset ($skipTitle) ) 

    $showTitle = FALSE;

  else { 

    $showTitle = TRUE;

    $title = str_replace ( '/index', '', $title );
    $title = padExplode ( $title, '/' );
    $title = end ( $title ) ;
  
  }

?>