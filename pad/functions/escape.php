<?php

  // Pipe function escape(strategy): escapes the value for the context it is written into.
  //
  //   html (the default)  htmlspecialchars - text and quoted attributes
  //   attr                every character but letters, digits and , - . _ as &#xHH; - safe
  //                       even in an unquoted attribute
  //   js                  every character but letters, digits and , . _ as \xHH or \uHHHH -
  //                       for inside a JavaScript string: '<script>var s = "{$x | escape('js')}"'
  //   css                 every character but letters and digits as \HH  - for a CSS value
  //   url                 rawurlencode - one part of a path or a query
  //
  // The sanitize chain a {$field} ends with encodes without encoding an entity twice, and
  // the js, css and url forms leave nothing it would change, so the two do not stack.
  // The stand-ins of the quotes and the backslash are escaped as the characters they stand
  // for (padUnprotectQuotes) - left alone, html let them through to become live quotes.

  $strategy = strtolower ( (string) ( $parm [0] ?? 'html' ) );
  $text     = is_scalar ( $value ) ? padUnprotectQuotes ( (string) $value ) : '';

  if ( $strategy == 'html' )
    return htmlspecialchars ( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  if ( $strategy == 'url' )
    return rawurlencode ( $text );

  if ( ! in_array ( $strategy, [ 'attr', 'js', 'css' ] ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "escape has no strategy named '" . padMakeSafe ( $strategy, 20 ) . "' - html, attr, js, css or url" );
    return htmlspecialchars ( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
  }

  $keep = [ 'attr' => '/[a-zA-Z0-9,\-._]/', 'js' => '/[a-zA-Z0-9,._]/', 'css' => '/[a-zA-Z0-9]/' ] [$strategy];
  $out  = '';

  foreach ( mb_str_split ( $text, 1, 'UTF-8' ) as $char ) {

    if ( preg_match ( $keep, $char ) ) {
      $out .= $char;
      continue;
    }

    $code = mb_ord ( $char, 'UTF-8' );

    if ( $code === FALSE )
      $code = 0xFFFD;

    if ( $strategy == 'attr' )
      $out .= sprintf ( '&#x%02X;', $code );
    elseif ( $strategy == 'css' )
      $out .= sprintf ( '\\%X ', $code );
    elseif ( $code < 0x80 )
      $out .= sprintf ( '\\x%02X', $code );
    elseif ( $code < 0x10000 )
      $out .= sprintf ( '\\u%04X', $code );
    else {
      $code -= 0x10000;
      $out  .= sprintf ( '\\u%04X\\u%04X', 0xD800 | ( $code >> 10 ), 0xDC00 | ( $code & 0x3FF ) );
    }

  }

  return $out;

?>
