<?php

  // An <img src="http://localhost/pad/develop/?clean&go=1"> on a page of another site comes
  // from this machine's own browser - local - but the browser says where it was sent from.

  $curl   = padCurl ( [ 'url' => $padHost . 'develop/?coverage', 'headers' => [ 'Sec-Fetch-Site' => 'cross-site' ] ] );
  $answer = $curl ['result'] . ': ' . trim ( $curl ['data'] );

?>
