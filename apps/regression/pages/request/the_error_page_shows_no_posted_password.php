<?php

  // The error page shows this machine what the failed request brought: its request values
  // with the password redacted, and then the raw body under Input - the password in clear,
  // a few lines under the redacted one. Asked as a browser asks: a tool's user agent gets
  // the JSON report, which has no body block.

  $curl = padCurl ( [ 'url'  => $padHost . 'regression/pages/?request/a_post_that_fails&padInclude',
                      'post' => 'user=bob&password=zzPostedSecret',
                      'options' => [ 'USERAGENT' => 'Mozilla/5.0' ] ] );

  $shown = ( $curl ['result'] == '500' and str_contains ( $curl ['data'], 'zzPostedSecret' ) ) ? 'YES' : 'no';

?>
