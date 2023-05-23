<?php

  $item  = $item ?? 'examples/hello';  
  $title = $item;

  $old  = padFileGetContents ( padApp . "_regression/$item.html" );
  $new  = getPageData ($item);
  $html = padFileGetContents ( padApp . "$item.html" );

  $ok = ( $old == $new);

  if ( strpos ( $html, '<!-- PAD: NO REGRESSION -->') !== FALSE ) 
    $ok = TRUE;


?>