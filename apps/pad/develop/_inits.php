<?php

  $one       = padCorrectPath ( $one       ?? 'hello/index'     );
  $item      = padCorrectPath ( $item      ?? 'hello/index'     );
  $reference = padCorrectPath ( $reference ?? 'tags/properties' );

  if     ( $padPage == 'xapp/index' ) $title = $reference;
  elseif ( $padPage == 'xapp/show'  ) $title = $item;
  else                                     $title = $padPage;

  if ( strpos($title, '/docs/')  ) 

    $showTitle = FALSE;

  elseif ( isset ($skipTitle) ) 

    $showTitle = FALSE;

  else { 

    $title = str_replace ( '/index', '', $title );
    $title = padExplode ( $title, '/' );
    $title = end ( $title ) ;
    $title = str_replace ( '_', ' ', $title );

    $showTitle = TRUE;
  
  }

  if ( ! isset ( $manual ) ) 
    $manual = '';

?>