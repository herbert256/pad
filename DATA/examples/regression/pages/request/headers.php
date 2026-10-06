<?php

  // Every web response carries the security headers - no type sniffing, the address kept
  // out of other sites' Referer, no framing by another site - and a page with a strict
  // policy gets this request's nonce in its script-src, the same one {nonce} wrote into its
  // script tag; a header the page sent itself stands. There were none of these headers.

  $plain  = padCurl ( $padGoExt . 'request/vars&padInclude' );
  $nonced = padCurl ( $padGoExt . 'request/nonced&padInclude' );

  $csp    = $nonced ['headers'] ['Content-Security-Policy'] ?? '';
  $inBody = preg_match ( '/nonce="([^"]+)"/', $nonced ['data'], $match ) ? $match [1] : '-';

  $headersResult = 'nosniff: '   . ( $plain ['headers'] ['X-Content-Type-Options'] ?? 'none' )
                 . ' | referrer: ' . ( $plain ['headers'] ['Referrer-Policy'] ?? 'none' )
                 . ' | csp: '      . ( $plain ['headers'] ['Content-Security-Policy'] ?? 'none' )
                 . ' | strict: '   . str_replace ( $inBody, '<nonce>', $csp )
                 . ' | same nonce: ' . ( $inBody !== '-' && str_contains ( $csp, "'nonce-$inBody'" ) ? 'yes' : 'no' )
                 . ' | own header: ' . ( $nonced ['headers'] ['Referrer-Policy'] ?? 'none' );

?>
