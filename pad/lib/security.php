<?php

  // The security headers every web response carries, and the per-request nonce that lets
  // an inline script run under a strict Content-Security-Policy.
  //
  // padNonce            this request's nonce: 18 random bytes, base64 - made on first use
  //                     and the same for the rest of the request, a restart included
  // padSecurityHeaders  sends $padSecurityHeaders and the $padCsp policy, with 'nonce' in
  //                     the policy replaced by 'nonce-<the nonce>'; a header the page's own
  //                     PHP already sent stands - the page has the last word
  //
  // The output step (lib/output.php) sent content type, caching, Vary and ETag headers, but
  // nothing that tells a browser to refuse to sniff a type, to keep the address out of the
  // Referer of other sites, or to stay out of another site's frame, and an application had
  // no way to allow its own inline scripts and no others.
  //
  // The nonce is written by {nonce} where the template asks for it, never added to the
  // page's <script> tags by the engine: a script an attacker managed to get into the page
  // would be given it too, and the policy would let it run.

  function padNonce () {

    global $padNonce;

    if ( ! isset ( $padNonce ) or $padNonce === '' )
      $padNonce = base64_encode ( random_bytes ( 18 ) );

    return $padNonce;

  }

  function padSecurityHeaders () {

    global $padSecurityHeaders, $padCsp;

    if ( headers_sent () )
      return;

    $sent = [];

    foreach ( headers_list () as $header )
      $sent [ strtolower ( trim ( explode ( ':', $header, 2 ) [0] ) ) ] = TRUE;

    $headers = is_array ( $padSecurityHeaders ?? NULL ) ? $padSecurityHeaders : [];

    if ( is_string ( $padCsp ?? NULL ) and trim ( $padCsp ) !== '' )
      $headers ['Content-Security-Policy'] = str_replace ( "'nonce'", "'nonce-" . padNonce () . "'", trim ( $padCsp ) );

    foreach ( $headers as $name => $value ) {

      if ( $value === FALSE or $value === NULL or trim ( (string) $value ) === '' )
        continue;

      if ( isset ( $sent [ strtolower ( $name ) ] ) )
        continue;

      padHeader ( $name . ': ' . str_replace ( [ "\r", "\n" ], ' ', (string) $value ) );

    }

  }

?>
