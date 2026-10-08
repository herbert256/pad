<?php

  // The mails of the 'file' transport, DATA/mail/<app>/*.eml, read back: the headers, and
  // the text and HTML parts of the multipart/alternative body padMail writes (quoted-
  // printable or base64, any charset iconv knows).

  function adminMailRead ( $file ) {

    $raw = str_replace ( "\r\n", "\n", (string) @file_get_contents ( $file ) );

    [ $head, $body ] = array_pad ( explode ( "\n\n", $raw, 2 ), 2, '' );

    $headers = adminMailHeaders ( $head );
    $parts   = [ 'text' => '', 'html' => '' ];

    adminMailParts ( $headers, $body, $parts );

    return [ 'headers' => $headers, 'text' => $parts ['text'], 'html' => $parts ['html'], 'raw' => $raw ];

  }

  function adminMailHeaders ( $head ) {

    $headers = [];

    foreach ( explode ( "\n", preg_replace ( '/\n[ \t]+/', ' ', $head ) ) as $line )
      if ( str_contains ( $line, ':' ) ) {
        [ $name, $value ] = explode ( ':', $line, 2 );
        $headers [ strtolower ( trim ( $name ) ) ] = trim ( iconv_mime_decode ( trim ( $value ), ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8' ) ?: $value );
      }

    return $headers;

  }

  function adminMailParts ( $headers, $body, &$parts, $depth = 0 ) {

    $type = strtolower ( $headers ['content-type'] ?? 'text/plain' );

    if ( $depth < 4 and str_starts_with ( $type, 'multipart/' ) and preg_match ( '/boundary="?([^";]+)"?/i', $headers ['content-type'], $match ) ) {

      foreach ( explode ( '--' . $match [1], $body ) as $chunk ) {

        $chunk = ltrim ( $chunk, "\n" );

        if ( $chunk === '' or str_starts_with ( $chunk, '--' ) )
          continue;

        [ $head, $inner ] = array_pad ( explode ( "\n\n", $chunk, 2 ), 2, '' );

        adminMailParts ( adminMailHeaders ( $head ), $inner, $parts, $depth + 1 );

      }

      return;

    }

    $encoding = strtolower ( $headers ['content-transfer-encoding'] ?? '' );

    if ( $encoding == 'quoted-printable' ) $body = quoted_printable_decode ( $body );
    elseif ( $encoding == 'base64' )       $body = (string) base64_decode ( $body );

    if ( preg_match ( '/charset="?([^";]+)"?/i', $type, $match ) and strtolower ( $match [1] ) !== 'utf-8' )
      $body = (string) @iconv ( $match [1], 'UTF-8//IGNORE', $body );

    if ( str_starts_with ( $type, 'text/html' ) and $parts ['html'] === '' )
      $parts ['html'] = $body;
    elseif ( str_starts_with ( $type, 'text/plain' ) and $parts ['text'] === '' )
      $parts ['text'] = rtrim ( $body );

  }

?>
