<?php

  // A login was started - its state is in the session - but the answer that comes back
  // carries another one: a forged answer, or one meant for another browser. It is refused
  // before the code is exchanged, and the login that was started is still waiting.

  $start = padOAuthUrl ( 'gitlab', [ 'client_id' => 'cid', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/?oauth/gitlab' ] );

  parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

  padCurlFake ( [ '*' => 500 ] );

  $_GET ['code']  = 'stolen-code';
  $_GET ['state'] = 'not-' . $asked ['state'];

  $forged  = padOAuthUser ( 'gitlab', [ 'client_id' => 'cid', 'client_secret' => 'cs' ] );
  $error   = padOAuthError ();
  $fetches = count ( padCurlRecorded () );
  $waiting = isset ( $_SESSION ['padOAuth'] ['regression/framework'] [ $asked ['state'] ] ) ? 'yes' : 'no';

?>
