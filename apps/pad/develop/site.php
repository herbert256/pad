<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $pagesList ['hom'] ['item'] = 'index';
  $pagesList ['ref'] ['item'] = 'reference/index';
  $pagesList ['man'] ['item'] = 'manual/index';

  $work = padList ();

  foreach ( $work as $key => $one ) {

    if ( ! str_contains ( $one ['path'], 'manual' ) ) continue;
    if (   str_contains ( $one ['path'], 'index'  ) ) continue;

    $pagesList [$key] ['item'] = $one ['item'];

  }

  $pagesList ['dev']  ['item'] = 'develop/index';
  $pagesList ['xref'] ['item'] = 'develop/xref';

  set_time_limit ( 300 );

  $showTitle = FALSE;

?>