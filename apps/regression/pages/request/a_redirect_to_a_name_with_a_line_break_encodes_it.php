<?php

  // A page name handed to padRedirect from the request - ?next=x%0d%0aX-Evil:%201 - goes to
  // the browser with its control characters percent-encoded, a 302 like any redirect: it
  // went into the Location header as it was, PHP refused the header, and every such request
  // ended as a 500 error report (no header was injected - PHP saw to that).

  if ( isset ( $_GET ['go'] ) )
    padRedirect ( padRequest ( 'next' ) );

  $lbGot = padCurl ( [ 'url'     => $padGoExt . 'request/a_redirect_to_a_name_with_a_line_break_encodes_it&go&next='
                                  . rawurlencode ( "x\r\nX-Evil: 1" ) . '&padInclude',
                       'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $lbResult = $lbGot ['result'] . ' ' . str_replace ( $padHost, '', $lbGot ['headers'] ['Location'] ?? '-' )
            . ' evil: ' . ( $lbGot ['headers'] ['X-Evil'] ?? 'none' );

?>
