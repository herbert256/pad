<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $pagesAll = padPages ();

  foreach ( $pagesAll as $key => $one )
    if ( str_contains ( file_get_contents ( $one ['path'] ), '<!-- PAD: NO ALL -->') ) 
      unset ( $pagesAll [$key] );

  set_time_limit ( 30 );

  $title = "All PAD pages with the example tag";

?>