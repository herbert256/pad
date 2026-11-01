<?php

  // The visitor said no at Discord: the answer is error=access_denied with a description and
  // the login's own state. FALSE, the error says so in words with the provider's description
  // (a value of the request - printed as text), and the state is spent: the same answer
  // again is a state no longer known.

  $start = padOAuthUrl ( 'discord', [ 'client_id' => 'cid', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/?oauth/discord' ] );

  parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

  padCurlFake ( [ '*' => 500 ] );

  $_GET ['error']             = 'access_denied';
  $_GET ['error_description'] = 'The resource owner or authorization server denied the request <b>{php:getcwd}</b>';
  $_GET ['state']             = $asked ['state'];

  $denied  = padOAuthUser ( 'discord', [ 'client_id' => 'cid', 'client_secret' => 'cs' ] );
  $error   = padOAuthError ();

  unset ( $_GET ['error'], $_GET ['error_description'] );

  $_GET ['code'] = 'a-code';

  $again   = padOAuthUser ( 'discord', [ 'client_id' => 'cid', 'client_secret' => 'cs' ] );
  $error2  = padOAuthError ();
  $fetches = count ( padCurlRecorded () );

?>
