<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $pagesAll = padList ();

  foreach ( $pagesAll as $key => $one ) {

    if ( padFileContains ( $one ['path'], 'NO ALL') ) unset ( $pagesAll [$key] );
    if ( strpos ( $one ['path'], 'restart')         ) unset ( $pagesAll [$key] );
    if ( strpos ( $one ['path'], 'redirect')        ) unset ( $pagesAll [$key] );
    if ( strpos ( $one ['path'], 'manual/')         ) unset ( $pagesAll [$key] );
    if ( strpos ( $one ['path'], 'reference/')      ) unset ( $pagesAll [$key] );
    if ( strpos ( $one ['path'], 'develop/')        ) unset ( $pagesAll [$key] );
    if ( strpos ( $one ['path'], '/deep/')          ) unset ( $pagesAll [$key] );

  }

  set_time_limit ( 30 );

  $title = "All PAD pages with the example tag";

?>