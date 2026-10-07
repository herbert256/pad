<?php

  // A small syntax colouring for the source cells: JSON, YAML, PHP and PAD. The text is cut
  // into tokens by one combined pattern per language; every piece, coloured or not, is
  // HTML-escaped, so the result is safe to print raw.

  function chartsHighlight ( $text, $lang ) {

    if ( $lang == 'php' )
      return chartsHighlightPhp ( $text );

    $rules = [

      'json' => [ 'key' => '"(?:[^"\\\\\n]|\\\\.)*"(?=\s*:)',
                  'str' => '"(?:[^"\\\\\n]|\\\\.)*"',
                  'num' => '-?\b\d+(?:\.\d+)?(?:[eE][+-]?\d+)?\b',
                  'lit' => '\b(?:true|false|null)\b',
                  'pun' => '[{}\[\],:]' ],

      'yaml' => [ 'com' => '#[^\n]*',
                  'pun' => '^[ \t]*-(?=[ \t])',
                  'key' => '[A-Za-z_][\w .-]*(?=:(?:[ \t]|$))',
                  'str' => '\'[^\'\n]*\'|"[^"\n]*"',
                  'num' => '(?<=:[ \t])-?\d+(?:\.\d+)?(?=[ \t]*$)' ],

      'pad'  => [ 'com' => '\{#.*?#\}',
                  'tag' => '(?<=\{)\/?[A-Za-z][\w:]*',
                  'brc' => '[{}]',
                  'htm' => '<\/?[A-Za-z][\w-]*|\/?>',
                  'opt' => '\b[A-Za-z][\w-]*(?==)',
                  'str' => '\'[^\'\n]*\'|"[^"\n]*"',
                  'var' => '\$\w+',
                  'num' => '\b\d+(?:\.\d+)?\b' ]

    ] [$lang] ?? [];

    if ( ! $rules )
      return chartsEscape ( $text );

    $parts = [];
    foreach ( $rules as $name => $rule )
      $parts [] = "(?P<$name>$rule)";

    preg_match_all ( '/' . implode ( '|', $parts ) . '/m', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL );

    $html = '';
    $at   = 0;

    foreach ( $matches as $match ) {

      list ( $token, $offset ) = $match [0];

      foreach ( array_keys ( $rules ) as $name )
        if ( isset ( $match [$name] [0] ) )
          break;

      $html .= chartsEscape ( substr ( $text, $at, $offset - $at ) )
             . "<span class=\"tk-$name\">" . chartsEscape ( $token ) . '</span>';

      $at = $offset + strlen ( $token );

    }

    return $html . chartsEscape ( substr ( $text, $at ) );

  }

  // PHP colours itself: highlight_string, its colours set to the classes of the others.

  function chartsHighlightPhp ( $text ) {

    foreach ( [ 'comment' => '#com', 'default' => '#def', 'html' => '#htm', 'keyword' => '#kwd', 'string' => '#str' ] as $what => $marker )
      ini_set ( "highlight.$what", $marker );

    $html = highlight_string ( $text, TRUE );
    $html = preg_replace ( '#^<pre><code[^>]*>|</code></pre>$#', '', trim ( $html ) );
    $html = preg_replace ( '#^<code><span style="color: \#\w+">\n?|</span>\n?</code>$#', '', $html );
    $html = preg_replace ( '#<span style="color: \#(\w+)">#', '<span class="tk-$1">', $html );

    return str_replace ( [ '<br />', '&nbsp;' ], [ "\n", ' ' ], $html );

  }

  function chartsEscape ( $text ) {

    return htmlspecialchars ( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
