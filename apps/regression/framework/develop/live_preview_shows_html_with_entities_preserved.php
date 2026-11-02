<?php

  $form = padCurl ( $padHost . 'live/' );
  $token = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $match ) ? $match [1] : '';
  $result = padCurl ( [ 'url' => $padHost . 'live/?render&view=source',
                        'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
                        'post' => [ 'source' => '<p>&lt; &amp;</p>', 'padCsrfToken' => $token ] ] );
  $answer = $result ['result'] . ': ' . trim ( $result ['data'] );

?>
