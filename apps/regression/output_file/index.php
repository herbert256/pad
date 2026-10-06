<?php

  // The file writer's promise: the page lands on disk instead of travelling - the response
  // is the $padFileNextPage page delivered as normal web, with the payload's body nowhere
  // in it, and the written file holding that body under the configured name.

  // The directory starts empty, so the file found below is this fetch's: the suite fetches
  // the payload page on its own as well, and the file that left behind satisfied the check
  // on the next run whether or not the writer still worked.

  padDeleteDataDir ( DATA . 'regression_output_file' );

  $r = padCurl ( $padHost . 'regression/output_file/?payload&padInclude' );

  $landed = FALSE;

  foreach ( glob ( DATA . 'regression_output_file/payload_*.html' ) as $file )
    if ( str_contains ( padFileGet ( $file ), 'CARRIED ALL THE WAY' ) )
      $landed = TRUE;

  // A {file} tag inside the page writes its own file and leaves the page's name alone: it
  // set the six name settings of the file writer, and the page was then written over the
  // tag's file - side.txt held the page, and no payload_*.html was there.

  $side = ( padFileGet ( DATA . 'regression_output_file/side.txt' ) === 'SIDE BY SIDE' ) ? 'yes' : 'NO';

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
