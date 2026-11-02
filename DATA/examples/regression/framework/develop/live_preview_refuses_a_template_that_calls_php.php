<?php

  // The post carries the token of Live preview's own form, fetched first with its cookie.

  $form  = padCurl ( $padHost . 'live/' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';

  $curl   = padCurl ( [ 'url'     => $padHost . 'live/?render',
                        'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
                        'post'    => [ 'source' => '{php:getcwd/}', 'padCsrfToken' => $token ] ] );
  $answer = $curl ['result'] . ': ' . ( preg_match ( '/getcwd\S* is not allowed by/', $curl ['data'] ) ? 'refused' : 'ran' );

?>
