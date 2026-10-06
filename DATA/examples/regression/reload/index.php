<?php

  // Test fetches the sample page - live reload switched on for it alone - and asserts what
  // live reload promises: a local whole page carries the script, with the stamp the poll
  // answers; a file that changes moves the stamp; a bare fragment and a forwarded request
  // get no script, and the forwarded poll is an ordinary page; a pad export of the page
  // carries none either. A plain load only offers the link.

  $tested = isset ( $test ) ? 1 : 0;

  if ( $tested ) {

    $base  = $padHost . 'regression/reload/?sample';
    $page  = padCurl ( $base ) ['data'] ?? '';
    $poll  = trim ( padCurl ( "$base&padReload" ) ['data'] ?? '' );

    $script = strpos ( $page, '<script id="padReload">' );

    $local = ( $script !== FALSE and $script < strripos ( $page, '</body>' )
               and ctype_digit ( $poll ) and str_contains ( $page, "last = \"$poll\"" ) ) ? 'yes' : 'NO';

    // A save: the sample's file time moves past the stamp, and so does the answer.

    touch ( APP . 'sample.pad', max ( time (), (int) $poll + 1 ) );
    clearstatcache ();

    $after   = trim ( padCurl ( "$base&padReload" ) ['data'] ?? '' );
    $changed = ( ctype_digit ( $after ) and (int) $after > (int) $poll ) ? 'yes' : 'NO';

    $bare    = padCurl ( "$base&padInclude" ) ['data'] ?? '';
    $other   = padCurl ( [ 'url' => $base,              'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] ) ['data'] ?? '';
    $otherP  = padCurl ( [ 'url' => "$base&padReload",  'headers' => [ 'X-Forwarded-For' => '203.0.113.9' ] ] ) ['data'] ?? '';

    $elsewhere = ( str_contains ( $bare, 'sample' ) and ! str_contains ( $bare, 'padReload' )
                   and str_contains ( $other, 'sample' ) and ! str_contains ( $other, 'padReload' )
                   and str_contains ( $otherP, 'sample' ) and ! ctype_digit ( trim ( $otherP ) ) ) ? 'yes' : 'NO';

    // pad export renders the page as the web gets it - without the script.

    $php  = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
    $proc = proc_open ( [ $php, dirname ( APPS ) . '/apps/cli/pad', 'render', 'regression/reload', 'sample' ],
                        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'file', '/dev/null', 'w' ] ],
                        $pipes, NULL, array_merge ( getenv (), [ 'PAD_EXPORT' => '1' ] ) );
    $copy = is_resource ( $proc ) ? stream_get_contents ( $pipes [1] ) : '';

    if ( is_resource ( $proc ) ) {
      fclose ( $pipes [1] );
      proc_close ( $proc );
    }

    $export = ( str_contains ( $copy, 'sample' ) and ! str_contains ( $copy, 'padReload' ) ) ? 'yes' : 'NO';

    // A page a route reached - item/7, the file item/[id].pad - polls the address it was
    // asked at, and the poll answers the stamp: it named the file, which no request reaches,
    // and the poll was answered 404 for as long as the page stayed open.

    $item   = padCurl ( $padHost . 'regression/reload/?item/7' ) ['data'] ?? '';
    $itemAt = preg_match ( '/var url = ("[^"]*")/', $item, $itemMatch ) ? (string) json_decode ( $itemMatch [1] ) : '';
    $itemP  = $itemAt !== '' ? padCurl ( substr ( $padHost, 0, -strlen ( $padRoot ) ) . $itemAt ) : [];

    $routed = ( str_contains ( $item, 'item 7' ) and ( $itemP ['result'] ?? '' ) == 200
                and ctype_digit ( trim ( $itemP ['data'] ?? '' ) ) ) ? 'yes' : 'NO';

  }

?>
