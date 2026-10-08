<?php

  // An htmx request names the element it swaps in HX-Target: the page answers the fragment
  // of that name alone - and renders whole when it has no such fragment, when the header
  // comes without HX-Request, or when padFragment names another fragment, which wins.

  $padTargetUrl = $padHost . 'regression/pages/?fragments/target&padInclude';

  foreach ( [ 'target rows'        => [ '',                   [ 'HX-Request' => 'true', 'HX-Target' => 'rows'  ] ],
              'target other'       => [ '',                   [ 'HX-Request' => 'true', 'HX-Target' => 'other' ] ],
              'no htmx'            => [ '',                   [ 'HX-Target'  => 'rows' ] ],
              'padFragment wins'   => [ '&padFragment=rows',  [ 'HX-Request' => 'true', 'HX-Target' => 'other' ] ] ]
            as $padTargetLabel => [ $padTargetQuery, $padTargetHeaders ] ) {

    $padTargetCurl = padCurl ( [ 'url' => $padTargetUrl . $padTargetQuery, 'headers' => $padTargetHeaders ] );

    echo "$padTargetLabel: " . $padTargetCurl ['result'] . ' ' . str_replace ( "\n", ' ', trim ( $padTargetCurl ['data'] ) ) . "\n";

  }

?>
