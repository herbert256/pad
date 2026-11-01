<?php

  // Sign in with Slack as docs.slack.dev has it: the scopes openid, email and profile, a
  // nonce, no PKCE, the code exchanged with the id and secret in the body, the issuer
  // https://slack.com, the user the openid.connect.userInfo answer. The sub is the workspace
  // and the user, T0R7GR/U0R7JM, as a Slack user id is unique within its workspace alone; a
  // token without a workspace logs no one in. team= asks Slack for that workspace and
  // refuses another's token - the login's record keeps it, so a way back handled without
  // team= (the workspace picked at the start alone) refuses one too, as one with another
  // team= does. Slack's methods answer an error with HTTP 200 and ok false - at the token
  // endpoint and at userinfo both, the error is said.

  $nonce  = '';
  $claims = [];
  $token  = NULL;
  $info   = NULL;

  padCurlFake ( [
    'https://slack.com/api/openid.connect.token' => function () use ( &$nonce, &$claims, &$token ) {
      if ( $token )
        return [ 'data' => $token ];
      $all = $claims + [ 'iss' => 'https://slack.com', 'sub' => 'U0R7JM', 'aud' => '25259531569.1115258246291', 'exp' => time () + 300, 'iat' => time (),
                         'auth_time' => time (), 'nonce' => $nonce, 'https://slack.com/user_id' => 'U0R7JM', 'email' => 'krane@slack-corp.com',
                         'email_verified' => TRUE, 'name' => 'krane' ];
      return [ 'data' => [ 'ok' => TRUE, 'access_token' => 'xoxp-1234', 'token_type' => 'Bearer',
                           'id_token' => 'e30.' . padCryptB64 ( json_encode ( array_filter ( $all, fn ( $value ) => $value !== NULL ) ) ) . '.c2ln' ] ];
    },
    'https://slack.com/api/openid.connect.userInfo' => function () use ( &$info ) {
      return [ 'data' => $info ?? [ 'ok' => TRUE, 'sub' => 'U0R7JM', 'https://slack.com/user_id' => 'U0R7JM', 'https://slack.com/team_id' => 'T0R7GR',
                                    'email' => 'krane@slack-corp.com', 'email_verified' => TRUE, 'date_email_verified' => 1622128723, 'name' => 'krane',
                                    'picture' => 'https://secure.gravatar.com/avatar/krane.png', 'given_name' => 'Bront', 'family_name' => 'Labradoodle',
                                    'locale' => 'en-US', 'https://slack.com/team_name' => 'kraneflannel', 'https://slack.com/team_domain' => 'kraneflannel' ] ];
    },
  ] );

  $rows  = [];
  $first = NULL;

  $login = function ( $label, $options, $set, $back = NULL ) use ( &$rows, &$first, &$nonce, &$claims, &$token, &$info ) {
    [ $claims, $token, $info ] = $set + [ [], NULL, NULL ];
    $app      = [ 'client_id' => '25259531569.1115258246291', 'client_secret' => 'slack-secret', 'redirect' => 'https://shop.example/oauth/slack' ];
    $options += $app;
    $start    = padOAuthUrl ( 'slack', $options );
    parse_str ( parse_url ( $start, PHP_URL_QUERY ), $asked );
    $nonce = $asked ['nonce'] ?? '';
    $_GET  = [ 'code' => '4724469134.4644010092847', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( 'slack', ( $back === NULL ) ? $options : $back + $app );
    $first = $first ?? [ 'asked' => array_keys ( $asked ), 'user' => $user ];
    $rows [$label] = ( isset ( $asked ['team'] ) ? 'team=' . $asked ['team'] . ': ' : '' ) . ( $user ? $user ['id'] : padOAuthError () );
  };

  $login ( 'any workspace',             [],                    [ [ 'https://slack.com/team_id' => 'T0R7GR' ] ] );
  $login ( 'its own workspace',         [ 'team' => 'T0R7GR' ], [ [ 'https://slack.com/team_id' => 'T0R7GR' ] ] );
  $login ( 'another workspace',         [ 'team' => 'T0999ZZ' ], [ [ 'https://slack.com/team_id' => 'T0R7GR' ] ] );
  $login ( 'another, back without team', [ 'team' => 'T0999ZZ' ], [ [ 'https://slack.com/team_id' => 'T0R7GR' ] ], [] );
  $login ( 'another, back with its team', [ 'team' => 'T0999ZZ' ], [ [ 'https://slack.com/team_id' => 'T0R7GR' ] ], [ 'team' => 'T0R7GR' ] );
  $login ( 'its own, back without team', [ 'team' => 'T0R7GR' ], [ [ 'https://slack.com/team_id' => 'T0R7GR' ] ], [] );
  $login ( 'a token without workspace', [],                    [ [], NULL, [ 'ok' => TRUE, 'sub' => 'U0R7JM', 'name' => 'krane' ] ] );
  $login ( 'a refused code',            [],                    [ [], [ 'ok' => FALSE, 'error' => 'invalid_code' ] ] );
  $login ( 'a refused token',           [],                    [ [ 'https://slack.com/team_id' => 'T0R7GR' ], NULL, [ 'ok' => FALSE, 'error' => 'invalid_auth' ] ] );

  $exchange = padCurlRecorded () [0];

  parse_str ( $exchange ['post'], $body );

  $json = json_encode ( [ 'authorize' => 'https://slack.com/openid/connect/authorize', 'asked' => $first ['asked'], 'body' => array_keys ( $body ),
                          'basic' => $exchange ['headers'] ['Authorization'] ?? '(none)', 'user' => $first ['user'], 'logins' => $rows ],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
