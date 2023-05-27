<?php
  
  $one       = $one ?? 'hello/index';
  $item      = $item ?? 'hello/index';
  $reference = $reference ?? 'tags/properties';

  if     ( $padPage == 'reference/reference'   ) $title = $reference;
  elseif ( $padPage == 'reference/show'        ) $title = $item;
  else                                           $title = $padPage;

  if ( strpos($title, '/docs/')  ) 

    $showTitle = FALSE;

  elseif ( isset ($skipTitle) ) 

    $showTitle = FALSE;

  else { 

    $title = str_replace ( '/index', '', $title );
    $title = padExplode ( $title, '/' );
    $title = end ( $title ) ;
    $title = str_replace ( '_', ' ', $title );
    $title = ucwords ( $title );

    $showTitle = TRUE;
  
  }

?>