<?php

  // The id_token's signature is not checked because it comes straight from the token
  // endpoint over TLS - which holds only when the endpoint is https. An issuer, a base= and
  // every endpoint a discovery document names must be https, else the client secret and the
  // tokens travel in clear and anyone on the wire can hand in an id_token of their own: an
  // http one is refused before anything is fetched - an issuer or a base the author's error,
  // a document's endpoint the provider's. http://localhost and 127.0.0.1 stay, for
  // development.

  $padCheckSyntax = FALSE;

  $options = [ 'client_id' => 'shop', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/x' ];

  $report = [];

  $start = function ( $label, $kind, $more ) use ( &$report, $options ) {
    padCurlFake ( [] );
    $url = padOAuthUrl ( $kind, $more + $options );
    $report [$label] = [ 'authorize' => $url === '' ? padOAuthError () : strtok ( $url, '?' ), 'fetched' => count ( padCurlRecorded () ) ];
  };

  padCurlFake ( [
    'https://id.example.com/.well-known/openid-configuration' => [ 'data' => [
      'issuer' => 'https://id.example.com', 'authorization_endpoint' => 'https://id.example.com/auth',
      'token_endpoint' => 'http://id.example.com/token', 'userinfo_endpoint' => 'http://id.example.com/userinfo' ] ],
    'http://plain.example.com/.well-known/openid-configuration' => [ 'data' => [
      'issuer' => 'http://plain.example.com', 'authorization_endpoint' => 'http://plain.example.com/auth', 'token_endpoint' => 'http://plain.example.com/token' ] ],
    'http://localhost:8080/realms/dev/.well-known/openid-configuration' => [ 'data' => [
      'issuer' => 'http://localhost:8080/realms/dev', 'authorization_endpoint' => 'http://localhost:8080/realms/dev/auth',
      'token_endpoint' => 'http://localhost:8080/realms/dev/token' ] ],
    'http://id.example.com/token' => [ 'data' => [ 'access_token' => 'a', 'id_token' => 'e30.e30.Zm9yZ2Vk' ] ],
    '*' => 404 ] );

  $start ( 'an http issuer',                         'oidc',    [ 'issuer' => 'http://plain.example.com' ] );
  $start ( 'a document naming http endpoints',       'oidc',    [ 'issuer' => 'https://id.example.com' ] );
  $start ( 'an http GitLab',                         'gitlab',  [ 'base' => 'http://gitlab.example.org' ] );
  $start ( 'an http Forgejo',                        'forgejo', [ 'base' => 'http://forge.example.org' ] );
  $start ( 'an issuer on localhost',                 'oidc',    [ 'issuer' => 'http://localhost:8080/realms/dev' ] );
  $start ( 'a GitLab on 127.0.0.1',                  'gitlab',  [ 'base' => 'http://127.0.0.1:8929' ] );
  $start ( 'https',                                  'gitlab',  [ 'base' => 'https://gitlab.example.org' ] );

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
