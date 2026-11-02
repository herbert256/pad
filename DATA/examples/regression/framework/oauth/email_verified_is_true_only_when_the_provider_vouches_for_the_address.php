<?php

  // The email of the user row is the address the provider holds - a contact, which the
  // account's owner may have typed without proving it is theirs - and email_verified says
  // whether the provider vouches for it: Google's, Apple's and an OpenID provider's
  // email_verified claim, Discord's verified, GitLab's confirmed_at, the verified flag of the
  // address in GitHub's and Forgejo's /user/emails, Microsoft's optional claim xms_edov (the
  // domain's owner verified). Microsoft's email is the email claim alone - preferred_username,
  // which any tenant can set to any address, is the username. An application links a login to
  // an existing account by its address only when email_verified is TRUE.

  include_once PAD . 'subs/oauth/_lib/jwt.php';

  $rows = [];

  $idToken = fn ( $claims ) => [ 'data' => [ 'access_token' => 'at', 'token_type' => 'Bearer',
                                             'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims + [ 'nonce' => $GLOBALS ['caseNonce'], 'exp' => time () + 300 ] ) ) . '.c2ln' ] ];

  $login = function ( $label, $kind, $options, $fakes ) use ( &$rows ) {
    padCurlFake ( $fakes );
    $options += [ 'client_id' => 'shop', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/' . $kind ];
    parse_str ( parse_url ( padOAuthUrl ( $kind, $options ), PHP_URL_QUERY ), $asked );
    $GLOBALS ['caseNonce'] = $asked ['nonce'] ?? '';
    $_GET  = [ 'code' => 'c', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( $kind, $options );
    $rows [] = "$label: " . ( $user ? "'" . $user ['email'] . "' " . ( array_key_exists ( 'email_verified', $user ) ? var_export ( $user ['email_verified'], TRUE ) : '(no flag)' )
                                    : padOAuthError () );
  };

  foreach ( [ TRUE, FALSE ] as $yes )
    $login ( 'google ' . ( $yes ? 'verified' : 'unverified' ), 'google', [], [
      'https://oauth2.googleapis.com/token' => fn () => $idToken ( [ 'iss' => 'https://accounts.google.com', 'aud' => 'shop', 'sub' => '1' ] ),
      'https://openidconnect.googleapis.com/v1/userinfo' => [ 'data' => [ 'sub' => '1', 'email' => 'ann@gmail.com', 'email_verified' => $yes ] ] ] );

  $tid = '11111111-2222-3333-4444-555555555555';

  foreach ( [ 'preferred_username alone' => [ 'preferred_username' => 'ceo@victim-company.com' ],
              'email of a domain verified' => [ 'preferred_username' => 'bo@contoso.com', 'email' => 'bo@contoso.com', 'xms_edov' => TRUE ],
              'email unverified' => [ 'preferred_username' => 'cy@contoso.com', 'email' => 'cy@contoso.com' ] ] as $label => $claims )
    $login ( "microsoft, $label", 'microsoft', [], [
      'https://login.microsoftonline.com/common/oauth2/v2.0/token' => fn () => $idToken ( [ 'iss' => "https://login.microsoftonline.com/$tid/v2.0", 'tid' => $tid,
                                                                                            'aud' => 'shop', 'sub' => 'AAA' ] + $claims ),
      'https://graph.microsoft.com/oidc/userinfo' => [ 'data' => [ 'sub' => 'AAA', 'name' => 'Someone' ] + array_intersect_key ( $claims, [ 'email' => 1 ] ) ] ] );

  foreach ( [ TRUE, FALSE ] as $yes )
    $login ( 'discord ' . ( $yes ? 'verified' : 'unverified' ), 'discord', [], [
      'https://discord.com/api/oauth2/token' => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'Bearer' ] ],
      'https://discord.com/api/users/@me'    => [ 'data' => [ 'id' => '80351110224678912', 'username' => 'ann', 'email' => 'admin@shop.example', 'verified' => $yes ] ] ] );

  foreach ( [ TRUE, FALSE ] as $yes )
    $login ( 'github public address ' . ( $yes ? 'verified' : 'not in the verified list' ), 'github', [], [
      'https://github.com/login/oauth/access_token' => [ 'data' => [ 'access_token' => 'gho', 'token_type' => 'bearer' ] ],
      'https://api.github.com/user'                 => [ 'data' => [ 'id' => 1, 'login' => 'ann', 'email' => 'ann@example.com' ] ],
      'https://api.github.com/user/emails'          => [ 'data' => [ [ 'email' => 'ann@example.com', 'verified' => $yes, 'primary' => TRUE ] ] ] ] );

  foreach ( [ '2024-01-02T03:04:05Z', NULL ] as $at )
    $login ( 'gitlab ' . ( $at ? 'confirmed' : 'unconfirmed' ), 'gitlab', [ 'base' => 'https://gitlab.com' ], [
      'https://gitlab.com/oauth/token'   => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'Bearer' ] ],
      'https://gitlab.com/api/v4/user'   => [ 'data' => [ 'id' => 42, 'username' => 'ann', 'email' => 'ann@example.com', 'confirmed_at' => $at ] ] ] );

  foreach ( [ TRUE, FALSE ] as $yes )
    $login ( 'forgejo ' . ( $yes ? 'verified' : 'unverified' ), 'forgejo', [ 'base' => 'https://codeberg.org' ], [
      'https://codeberg.org/login/oauth/access_token' => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'bearer' ] ],
      'https://codeberg.org/api/v1/user'              => [ 'data' => [ 'id' => 7, 'login' => 'ann', 'email' => 'ann@example.com' ] ],
      'https://codeberg.org/api/v1/user/emails'       => [ 'data' => [ [ 'email' => 'ann@example.com', 'verified' => $yes, 'primary' => TRUE ] ] ] ] );

  $issuer = 'https://id.example.com';

  foreach ( [ 'email_verified true' => [ 'email_verified' => TRUE ], 'no email_verified' => [] ] as $label => $claims )
    $login ( "oidc, $label", 'oidc', [ 'issuer' => $issuer ], [
      "$issuer/.well-known/openid-configuration" => [ 'data' => [ 'issuer' => $issuer, 'authorization_endpoint' => "$issuer/auth",
                                                                  'token_endpoint' => "$issuer/token", 'userinfo_endpoint' => "$issuer/userinfo" ] ],
      "$issuer/token"    => fn () => $idToken ( [ 'iss' => $issuer, 'aud' => 'shop', 'sub' => 's1' ] ),
      "$issuer/userinfo" => [ 'data' => [ 'sub' => 's1', 'email' => 'ed@example.com' ] + $claims ] ] );

  [ $d, $point ] = padEcNew ();

  foreach ( [ 'true', 'false' ] as $said )
    $login ( "apple, email_verified '$said'", 'apple', [ 'team_id' => 'TEAM123456', 'key_id' => 'KEY1234567', 'private_key' => padEcPem ( $d, $point ) ], [
      'https://appleid.apple.com/auth/token' => fn () => $idToken ( [ 'iss' => 'https://appleid.apple.com', 'aud' => 'shop', 'sub' => '001234.a',
                                                                      'email' => 'x7q2@privaterelay.appleid.com', 'email_verified' => $said ] ) ] );

  $json = json_encode ( $rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
