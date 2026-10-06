<?php

  // A page that shows this machine something it shows nobody else - a {debug} - is not
  // stored: built for a local request it went into the cache, and every visitor after it
  // was answered with the value {debug} keeps from them. The second fetch says it was
  // forwarded, as a visitor's request through a proxy on this machine does.

  $url = $padHost . 'regression/cache_file/?debugged&padInclude&debug';

  $local  = padCurl ( $url );
  $remote = padCurl ( [ 'url' => $url, 'headers' => [ 'X-Forwarded-For' => '203.0.113.5' ] ] );

  $debugResult = 'local sees it: '    . ( str_contains ( $local  ['data'], 'for this machine only' ) ? 'yes' : 'no' )
               . ', visitor sees it: ' . ( str_contains ( $remote ['data'], 'for this machine only' ) ? 'YES' : 'no' );

?>
