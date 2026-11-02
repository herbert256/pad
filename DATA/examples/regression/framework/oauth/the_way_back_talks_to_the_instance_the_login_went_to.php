<?php

  // The record of a login keeps the token endpoint and the issuer it was started at, and the
  // way back must resolve to the same: handled with another base=, issuer= or tenant= - a page
  // that picks the instance by a request value, a tenant per customer - it is refused before
  // the code, the PKCE verifier and the client secret go anywhere (RFC 9207's mix-up). The
  // same options both ways log in as ever.

  $options = [ 'client_id' => 'shop', 'client_secret' => 'cs', 'redirect' => 'https://shop.example/oauth/gitlab' ];

  padCurlFake ( [ '*/oauth/token' => [ 'data' => [ 'access_token' => 'a', 'token_type' => 'Bearer' ] ],
                  '*/api/v4/user' => [ 'data' => [ 'id' => 7, 'username' => 'ann', 'name' => 'Ann' ] ],
                  'https://login.microsoftonline.com/*' => [ 'status' => 400, 'data' => [ 'error' => 'invalid_grant' ] ] ] );

  $report = [];

  foreach ( [ 'the same instance' => 'https://gitlab.com', 'another instance' => 'https://evil.example' ] as $label => $back ) {

    padCurlFake ( [] );

    parse_str ( parse_url ( padOAuthUrl ( 'gitlab', [ 'base' => 'https://gitlab.com' ] + $options ), PHP_URL_QUERY ), $asked );

    $_GET = [ 'code' => 'code-issued-by-gitlab.com', 'state' => $asked ['state'] ];
    $user = padOAuthUser ( 'gitlab', [ 'base' => $back ] + $options );

    $report [$label] = [ 'fetched' => array_map ( fn ( $call ) => $call ['method'] . ' ' . $call ['url'], padCurlRecorded () ),
                         'user'    => $user ? $user ['id'] : padOAuthError () ];

  }

  // Microsoft: started for one tenant, back for another.

  padCurlFake ( [] );

  parse_str ( parse_url ( padOAuthUrl ( 'microsoft', [ 'tenant' => 'contoso.com', 'redirect' => 'https://shop.example/oauth/microsoft' ] + $options ), PHP_URL_QUERY ), $asked );

  $_GET = [ 'code' => 'c', 'state' => $asked ['state'] ];

  $report ['another tenant'] = [ 'user'    => padOAuthUser ( 'microsoft', [ 'tenant' => 'fabrikam.com', 'redirect' => 'https://shop.example/oauth/microsoft' ] + $options )
                                              ?: padOAuthError (),
                                 'fetched' => count ( padCurlRecorded () ) ];

  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

?>
