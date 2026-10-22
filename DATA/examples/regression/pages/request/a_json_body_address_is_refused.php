<?php

  // An address posted as a JSON body is a request value as a form post of it is: padRequest
  // hands the body to the page as POST input, and the address rule did not count it - the
  // form post was refused, the same address posted as application/json fetched.

  $curl = padCurl ( [ 'url'     => $padGoExt . 'request/json_feed&padInclude',
                      'post'    => json_encode ( [ 'feed' => 'http://127.0.0.1:9/probe' ] ),
                      'headers' => [ 'Content-Type' => 'application/json' ] ] );

  $jsonResult = $curl ['result'] . ' '
              . ( str_contains ( $curl ['data'], 'reads as an address to fetch' ) ? 'refused' : padEscape ( trim ( $curl ['data'] ) ) );

?>
