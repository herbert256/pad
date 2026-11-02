<?php

  // A provider's sub is unique within its own instance alone (OpenID Connect Core 2: within
  // the issuer) - a GitLab of one's own counts its users from 1 as gitlab.com does, and so do a
  // Forgejo and a company's OpenID provider. The id of the user row is the provider's kind and
  // the sub for the default instance - gitlab.com, Codeberg - and has the instance's address
  // in it for every other, every OpenID issuer too: two people never share one, and padLogin
  // never logs the one in as the other.

  $ids = [];

  $login = function ( $kind, $options, $fakes ) use ( &$ids ) {
    padCurlFake ( $fakes );
    $options += [ 'client_id' => 'shop', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/' . $kind ];
    parse_str ( parse_url ( padOAuthUrl ( $kind, $options ), PHP_URL_QUERY ), $asked );
    $GLOBALS ['caseNonce'] = $asked ['nonce'] ?? '';
    $_GET  = [ 'code' => 'c', 'state' => $asked ['state'] ];
    $user  = padOAuthUser ( $kind, $options );
    $ids [] = $user ? $user ['id'] . ' - ' . $user ['name'] : padOAuthError ();
  };

  $gitlab = fn ( $base, $name ) => [ "$base/oauth/token"   => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'Bearer' ] ],
                                     "$base/api/v4/user"   => [ 'data' => [ 'id' => 42, 'username' => 'u42', 'name' => $name ] ] ];

  $forgejo = fn ( $base, $name ) => [ "$base/login/oauth/access_token" => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'bearer' ] ],
                                      "$base/api/v1/user"              => [ 'data' => [ 'id' => 1, 'login' => 'u1', 'full_name' => $name ] ] ];

  $oidc = fn ( $issuer, $name ) => [
    "$issuer/.well-known/openid-configuration" => [ 'data' => [ 'issuer' => $issuer, 'authorization_endpoint' => "$issuer/auth",
                                                                'token_endpoint' => "$issuer/token", 'userinfo_endpoint' => "$issuer/userinfo" ] ],
    "$issuer/token"    => function () use ( $issuer ) {
      $claims = [ 'iss' => $issuer, 'aud' => 'shop', 'exp' => time () + 300, 'sub' => '1', 'nonce' => $GLOBALS ['caseNonce'] ];
      return [ 'data' => [ 'access_token' => 'at', 'id_token' => 'e30.' . padCryptB64 ( json_encode ( $claims ) ) . '.c2ln' ] ];
    },
    "$issuer/userinfo" => [ 'data' => [ 'sub' => '1', 'name' => $name ] ] ];

  $login ( 'gitlab',  [ 'base' => 'https://gitlab.com' ],          $gitlab ( 'https://gitlab.com', 'Ann at gitlab.com' ) );
  $login ( 'gitlab',  [ 'base' => 'https://git.example.org' ],     $gitlab ( 'https://git.example.org', 'Mallory at a GitLab of open sign-up' ) );
  $login ( 'gitlab',  [ 'base' => 'https://git.example.org:8443' ], $gitlab ( 'https://git.example.org:8443', 'Bo at another port' ) );
  $login ( 'forgejo', [],                                          $forgejo ( 'https://codeberg.org', 'Cy at Codeberg' ) );
  $login ( 'forgejo', [ 'base' => 'https://forge.example.org' ],   $forgejo ( 'https://forge.example.org', 'Di at a Forgejo' ) );
  $login ( 'oidc',    [ 'issuer' => 'https://sso.company.example' ],          $oidc ( 'https://sso.company.example', 'Ed of the company' ) );
  $login ( 'oidc',    [ 'issuer' => 'https://forge.community.example/realms/x' ], $oidc ( 'https://forge.community.example/realms/x', 'Fay of a community' ) );

  $json = json_encode ( $ids, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
