<?php

  // Test fetches the sample page four ways and asserts what the debug toolbar promises:
  // a local request for a whole page gets the bar, before </body>, with the tag tree, the
  // SQL and the template it read; a bare fragment (&padInclude) gets none; and a request
  // that says it was forwarded - somebody else's, behind a proxy - gets none either; nor
  // does a replay of a recorded case (lib/replay.php), which compares what the page rendered
  // with the recording, taken before the bar went in. A plain load only offers the link.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $page  = padCurl ( $padHost . 'regression/toolbar/?sample' ) ['data'] ?? '';
    $bare  = padCurl ( $padHost . 'regression/toolbar/?sample&padInclude' ) ['data'] ?? '';
    $other = padCurl ( [ 'url'     => $padHost . 'regression/toolbar/?sample',
                         'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] ) ['data'] ?? '';
    $again = padCurl ( $padHost . 'regression/toolbar/?sample&padReplay' ) ['data'] ?? '';

    $bar = strpos ( $page, '<div id="padToolbar"' );

    $local = ( $bar !== FALSE
               and $bar < strripos ( $page, '</body>' )
               and str_contains ( $page, '{colors}' )
               and str_contains ( $page, 'SQL (1)' )
               and str_contains ( $page, '6 * 7' )
               and str_contains ( $page, 'apps/regression/toolbar/sample.pad' ) ) ? 'yes' : 'NO';

    $fragment  = ( str_contains ( $bare,  '<span>green</span>' ) and ! str_contains ( $bare,  'padToolbar' ) ) ? 'yes' : 'NO';
    $forwarded = ( str_contains ( $other, '<span>green</span>' ) and ! str_contains ( $other, 'padToolbar' ) ) ? 'yes' : 'NO';
    $replayed  = ( str_contains ( $again, '<span>green</span>' ) and ! str_contains ( $again, 'padToolbar' ) ) ? 'yes' : 'NO';

  }

?>
