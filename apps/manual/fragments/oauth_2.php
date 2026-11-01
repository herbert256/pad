<?php

  // GitHub is not asked here: the two requests a login makes - the code for a token, the
  // token for the user - are answered by fakes, as a test answers them.

  padCurlFake ( [
    'https://github.com/login/oauth/access_token' => [ 'data' => [ 'access_token' => 'gho_example', 'token_type' => 'bearer' ] ],
    'https://api.github.com/user'                 => [ 'data' => [ 'id' => 583231, 'login' => 'octocat', 'name' => 'The Octocat',
                                                                   'email' => 'octocat@example.com', 'avatar_url' => 'https://example.com/octocat.png' ] ]
  ] );

  // In an application these two stand in .env as GITHUB_CLIENT_ID and GITHUB_CLIENT_SECRET.

  $options = [ 'client_id' => 'example-id', 'client_secret' => 'example-secret', 'redirect' => 'https://shop.example/oauth/github' ];

  // 1. The start: padOAuthStart ( 'github' ) sends the visitor to this address.

  $start = padOAuthUrl ( 'github', $options );

  parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );

  // 2. GitHub sends the visitor back with a code and the state - here written into $_GET.

  $_GET ['code']  = 'a-code-from-github';
  $_GET ['state'] = $asked ['state'];

  $user = padOAuthUser ( 'github', $options );

  padCurlFakeStop ();

  $page   = strtok ( $start, '?' );
  $values = implode ( ', ', array_keys ( $asked ) );

?>
