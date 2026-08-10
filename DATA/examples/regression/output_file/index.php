<?php

  // The file writer's promise: the page lands on disk instead of travelling - the response
  // is the $padFileNextPage page delivered as normal web, with the payload's body nowhere
  // in it, and the written file holding that body under the configured name.

  $r = padCurl ( $padHost . 'regression/output_file/?payload&padInclude' );

  $landed = FALSE;

  foreach ( glob ( DATA . 'regression_output_file/payload_*.html' ) as $file )
    if ( str_contains ( padFileGet ( $file ), 'CARRIED ALL THE WAY' ) )
      $landed = TRUE;

  $verdict = ( $r ['result'] == '200'
               and ! isset ( $r ['headers'] ['Content-Disposition'] )
               and str_contains ( $r ['data'], 'wrote the page to disk' )
               and ! str_contains ( $r ['data'], 'CARRIED ALL THE WAY' )
               and $landed ) ? 'yes' : 'NO';

  // The write is the test's side effect. This run's file goes, and so does whatever a
  // crawl of the payload page left behind, so the directory does not fill up.

  padDeleteDataDir ( DATA . 'regression_output_file' );

  $output = 'file';

?>