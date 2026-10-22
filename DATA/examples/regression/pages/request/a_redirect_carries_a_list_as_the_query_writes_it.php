<?php

  // The values a redirect carries go into the query as padUrl writes them - a list as
  // ids[0]=1&ids[1]=2, a NULL left out, a name with & in it encoded - from padRedirect and
  // from {redirect}: padAddGet urlencoded the value as text, and a list or a NULL ended the
  // request in a 500 TypeError.

  if ( isset ( $_GET ['go'] ) ) {

    if ( padRequest ( 'how' ) == 'php' )
      padRedirect ( 'globals', [ 'ids' => [ 1, 2 ], 'none' => NULL, 'a&b' => 'c d' ] );

    $lsResult = '';

  } else {

    $lsResult = '';

    foreach ( [ 'php', 'tag' ] as $lsHow ) {

      $lsGot = padCurl ( [ 'url'     => $padGoExt . "request/a_redirect_carries_a_list_as_the_query_writes_it&go&how=$lsHow&padInclude",
                           'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

      $lsResult .= "$lsHow: " . $lsGot ['result'] . ' ' . str_replace ( $padHost, '', $lsGot ['headers'] ['Location'] ?? '-' ) . ' | ';

    }

  }

?>
