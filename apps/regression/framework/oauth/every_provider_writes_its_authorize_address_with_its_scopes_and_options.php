<?php

  // Every provider of subs/oauth/types/ - the authorize address it sends the visitor to, the
  // scopes, whether PKCE and a nonce go along, what it adds of its own - with a GitLab and a
  // Forgejo of one's own (base), a Microsoft tenant, and the generic OpenID Connect provider
  // reading its endpoints from the issuer's discovery document (faked).

  $settings = [
    'apple'     => [ 'client_id' => 'com.example.web', 'team_id' => 'TEAM123456', 'key_id' => 'KEY1234567', 'private_key' => 'unused here' ],
    'gitlab'    => [ 'base' => 'https://git.example.com/' ],
    'forgejo'   => [ 'base' => 'https://forge.example.org' ],
    'microsoft' => [ 'tenant' => 'organizations' ],
    'oidc'      => [ 'issuer' => 'https://id.example.com/realms/main' ],
  ];

  padCurlFake ( [
    'https://id.example.com/realms/main/.well-known/openid-configuration' => [ 'data' => [
      'issuer'                 => 'https://id.example.com/realms/main',
      'authorization_endpoint' => 'https://id.example.com/realms/main/protocol/openid-connect/auth',
      'token_endpoint'         => 'https://id.example.com/realms/main/protocol/openid-connect/token',
      'userinfo_endpoint'      => 'https://id.example.com/realms/main/protocol/openid-connect/userinfo' ] ]
  ] );

  $providers = [];

  foreach ( padOAuthKinds () as $kind ) {

    $url = padOAuthUrl ( $kind, ( $settings [$kind] ?? [] ) + [ 'client_id' => 'cid', 'client_secret' => 'cs', 'redirect' => "https://shop.example/oauth/$kind" ] );

    parse_str ( parse_url ( $url, PHP_URL_QUERY ), $asked );

    $own = array_diff_key ( $asked, array_flip ( [ 'response_type', 'client_id', 'redirect_uri', 'scope', 'state', 'code_challenge', 'code_challenge_method', 'nonce' ] ) );

    $providers [] = [ 'kind'  => $kind,
                      'url'   => strtok ( $url, '?' ),
                      'scope' => $asked ['scope'] ?? '',
                      'pkce'  => isset ( $asked ['code_challenge'] ) ? 'PKCE' : 'no PKCE',
                      'nonce' => isset ( $asked ['nonce'] ) ? 'nonce' : 'no nonce',
                      'own'   => http_build_query ( $own ) ];

  }

?>
