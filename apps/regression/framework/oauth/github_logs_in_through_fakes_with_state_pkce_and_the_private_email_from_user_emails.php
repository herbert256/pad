<?php

  // The whole GitHub login without GitHub: the authorize address with its state and S256
  // challenge, the visitor coming back with the code, the code exchanged with the verifier,
  // the user fetched - and the address GitHub's /user answers null for a private one found
  // in /user/emails, the primary verified one. Every fetch answered by a fake.

  $start = padOAuthUrl ( 'github', [ 'client_id' => 'cid', 'client_secret' => 'csecret', 'redirect' => 'https://shop.example/?oauth/github' ] );

  parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

  padCurlFake ( [
    'https://github.com/login/oauth/access_token' => [ 'data' => [ 'access_token' => 'gho_token', 'token_type' => 'bearer', 'scope' => 'read:user,user:email' ] ],
    'https://api.github.com/user'                 => [ 'data' => [ 'id' => 583231, 'login' => 'octocat', 'name' => 'The Octocat', 'email' => NULL,
                                                                   'avatar_url' => 'https://avatars.githubusercontent.com/u/583231' ] ],
    'https://api.github.com/user/emails'          => [ 'data' => [ [ 'email' => 'old@example.com', 'primary' => FALSE, 'verified' => TRUE ],
                                                                   [ 'email' => 'octo@example.com', 'primary' => TRUE, 'verified' => TRUE ] ] ],
  ] );

  $_GET ['code']  = 'the-code';
  $_GET ['state'] = $asked ['state'];

  $user = padOAuthUser ( 'github', [ 'client_id' => 'cid', 'client_secret' => 'csecret' ] );

  $calls = padCurlRecorded ();

  parse_str ( $calls [0] ['post'], $exchanged );

  $report = [
    'authorize' => strtok ( $start, '?' ),
    'asked'     => array_keys ( $asked ),
    'method'    => $asked ['code_challenge_method'],
    'challenge' => $asked ['code_challenge'] === padCryptB64 ( hash ( 'sha256', $exchanged ['code_verifier'], TRUE ) ),
    'exchange'  => $calls [0] ['method'] . ' ' . $calls [0] ['url'],
    'sent'      => array_keys ( $exchanged ),
    'redirect'  => $exchanged ['redirect_uri'],
    'bearer'    => $calls [1] ['headers'] ['Authorization'],
    'fetched'   => array_map ( fn ( $call ) => $call ['url'], array_slice ( $calls, 1 ) ),
    'user'      => $user,
    'token'     => padOAuthToken () ['access_token'],
    'again'     => padOAuthUser ( 'github', [ 'client_id' => 'cid', 'client_secret' => 'csecret' ] ),
    'error'     => padOAuthError ()
  ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
