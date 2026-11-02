<?php

  // padOAuthUrl keeps a login for every address it writes, so a login page that writes its
  // buttons with it - six providers, or three opened twice - keeps them all: the session holds
  // twenty logins, the ones over their ten minutes dropped first, and the first button's
  // state still works. The twenty-first login pushes the oldest out.

  $options = [ 'client_id' => 'x', 'client_secret' => 'y', 'redirect' => 'https://shop.example/oauth' ];

  $state = fn ( $kind ) => parse_url ( padOAuthUrl ( $kind, $options ), PHP_URL_QUERY );

  padCurlFake ( [ 'https://github.com/login/oauth/access_token' => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'bearer' ] ],
                  'https://api.github.com/user'                 => [ 'data' => [ 'id' => 1, 'login' => 'ann' ] ],
                  'https://api.github.com/user/emails'          => [ 'data' => [] ] ] );

  $back = function ( $query ) use ( $options ) {
    parse_str ( $query, $asked );
    $_GET = [ 'code' => 'c', 'state' => $asked ['state'] ];
    $user = padOAuthUser ( 'github', $options );
    return $user ? $user ['id'] : padOAuthError ();
  };

  // A login of an hour ago, still in the session: dropped before any of the new ones.

  $first = $state ( 'github' );

  $_SESSION ['padOAuth'] [$padApp] = [ 'old' => [ 'kind' => 'github', 'app' => $padApp, 'time' => time () - 3600 ] ] + $_SESSION ['padOAuth'] [$padApp];

  foreach ( [ 'gitlab', 'discord', 'forgejo', 'github', 'gitlab' ] as $kind )
    $state ( $kind );

  $report ['six buttons, the first'] = $back ( $first );
  $report ['the hour-old login kept'] = isset ( $_SESSION ['padOAuth'] [$padApp] ['old'] );

  $first = $state ( 'github' );

  for ( $i = 1; $i <= 20; $i++ )
    $state ( 'gitlab' );

  $report ['twenty-one logins, the first'] = $back ( $first );
  $report ['kept'] = count ( $_SESSION ['padOAuth'] [$padApp] );

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
