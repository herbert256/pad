<?php

  // Syntax colouring on the server - the {highlight} tag and the highlight pipe.
  //
  //   {highlight 'php'} echo $hello; {/highlight}
  //   {highlight 'json', file='_data/products.json', lines}
  //   {echo $snippet | highlight('sql')}
  //
  // padHighlight        a block: <pre class="pad-highlight"><code> with the colours, a light
  //                     default look under :where() that follows the page's color-scheme
  // padHighlightTokens  the coloured text alone, for a frame of the page's own
  //
  // Their workings - the languages, the patterns, PHP's tokenizer, the escaping - are
  // tags/_lib/highlight.php, which they read when they colour a text.
  //
  // The languages: pad, php, html (and xml, svg), css, js (and json5, ts), json, yaml, sql
  // and bash (and sh, shell). The text is cut into tokens by one combined pattern per
  // language - PHP by PHP's own tokenizer - and every piece, coloured or not, is
  // HTML-escaped, so the result is safe to print raw. A token is a <span class="hl-...">:
  // com(ment), str(ing), num(ber), lit(eral), kwd (keyword), key, var, tag, att(ribute),
  // opt(ion), brc (PAD brace), htm (an HTML tag in PAD), fn (function), pun(ctuation), def.

  function padHighlightKnown ( $lang ) {

    include_once PAD . 'tags/_lib/highlight.php';

    return in_array ( padHighlightLanguage ( $lang ), [ 'pad', 'php', 'html', 'css', 'js', 'json', 'yaml', 'sql', 'bash', 'text' ], TRUE );

  }

  function padHighlightTokens ( $text, $lang ) {

    include_once PAD . 'tags/_lib/highlight.php';

    $text = (string) $text;
    $lang = padHighlightLanguage ( $lang );

    if ( $lang == 'php' )
      return padHighlightPhp ( $text );

    $rules = padHighlightRules ( $lang );

    if ( ! $rules )
      return padHighlightEscape ( $text );

    $parts = [];
    foreach ( $rules as $name => $rule )
      $parts [] = "(?P<$name>$rule)";

    // A PAD comment is one as the engine reads it (padCommentStrip): {# closes at the first
    // #} with no {# before it, and is no comment as the option sigil {#title}; {-- closes at
    // the first --}. A {-- after the last --} can never close: it is hidden from the scan -
    // the same length, the text shown is the text given. Each opener that never closed
    // scanned on to the end of the text, and fifty thousand took seconds.

    $scan = $text;

    if ( $lang == 'pad' and str_contains ( $text, '{--' ) ) {
      $from = strrpos ( $text, '--}' );
      $from = ( $from === FALSE ) ? 0 : $from + 3;
      $scan = substr ( $text, 0, $from ) . str_replace ( '{--', "{\x01-", substr ( $text, $from ) );
    }

    if ( preg_match_all ( '/' . implode ( '|', $parts ) . '/ms', $scan, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL ) === FALSE )
      return padHighlightEscape ( $text );

    $html = '';
    $at   = 0;

    foreach ( $matches as $match ) {

      list ( $token, $offset ) = $match [0];

      $token = substr ( $text, $offset, strlen ( $token ) );

      foreach ( array_keys ( $rules ) as $name )
        if ( isset ( $match [$name] [0] ) )
          break;

      $html .= padHighlightEscape ( substr ( $text, $at, $offset - $at ) )
             . "<span class=\"hl-$name\">" . padHighlightEscape ( $token ) . '</span>';

      $at = $offset + strlen ( $token );

    }

    return $html . padHighlightEscape ( substr ( $text, $at ) );

  }

  // The block: the text with its common indent taken off, coloured, line numbers when asked,
  // and the lines named in mark= ('3, 5-7') set apart.

  function padHighlight ( $text, $lang, $lines = FALSE, $mark = '' ) {

    include_once PAD . 'tags/_lib/highlight.php';

    $text   = rtrim ( padChartDedent ( str_replace ( "\r\n", "\n", (string) $text ) ) );
    $text   = ltrim ( $text, "\n" );
    $lang   = padHighlightLanguage ( $lang );
    $html   = padHighlightTokens ( $text, $lang );
    $marked = padHighlightMarked ( $mark );

    if ( $lines or $marked )
      $html = padHighlightLines ( $html, $lines, $marked );

    return padHighlightStyle ()
         . '<pre class="pad-highlight' . ( $lines ? ' hl-numbered' : '' ) . '" data-lang="' . padHighlightEscape ( $lang ) . '"><code>' . $html . '</code></pre>';

  }

  // A span per line - a span of a token that runs over lines is closed at the line's end and
  // opened again on the next, so every line stands on its own.

  function padHighlightLines ( $html, $numbered, $marked ) {

    $out  = [];
    $open = [];

    foreach ( explode ( "\n", $html ) as $i => $line ) {

      preg_match_all ( '#<span class="[^"]*">|</span>#', $line, $tags );

      $line = implode ( '', $open ) . $line;

      foreach ( $tags [0] as $tag )
        if ( $tag == '</span>' )
          array_pop ( $open );
        else
          $open [] = $tag;

      $class = 'hl-line' . ( isset ( $marked [$i + 1] ) ? ' hl-mark' : '' );
      $out [] = "<span class=\"$class\"" . ( $numbered ? ' data-line="' . ( $i + 1 ) . '"' : '' ) . '>' . $line . str_repeat ( '</span>', count ( $open ) ) . '</span>';

    }

    return implode ( "\n", $out );

  }

  // The default look: a light and a dark set of colours through light-dark(), every rule
  // under :where() so the page's own CSS wins.

  function padHighlightStyle () {

    $colours = [ 'com' => [ '#6a737d', '#8b949e' ], 'str' => [ '#0a7d32', '#a5d6a7' ], 'num' => [ '#b35900', '#ffb86b' ],
                 'lit' => [ '#b35900', '#ffb86b' ], 'kwd' => [ '#c2185b', '#ff7ab2' ], 'key' => [ '#1565c0', '#7db8ff' ],
                 'var' => [ '#8a5a00', '#e6c07b' ], 'tag' => [ '#c2185b', '#ff7ab2' ], 'att' => [ '#1565c0', '#7db8ff' ],
                 'opt' => [ '#1565c0', '#7db8ff' ], 'brc' => [ '#6a737d', '#8b949e' ], 'htm' => [ '#00796b', '#5fd4c4' ],
                 'fn'  => [ '#6f42c1', '#c3a6ff' ], 'pun' => [ '#6a737d', '#8b949e' ], 'def' => [ '#24292e', '#d7dae6' ] ];

    $css = ':where(.pad-highlight){margin:1em 0;padding:12px 16px;overflow:auto;border-radius:6px;font:13px/1.55 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;tab-size:2;'
         . 'color:#24292e;background:#f6f8fa}'
         . '@supports (color:light-dark(#000,#fff)){:where(.pad-highlight){color:light-dark(#24292e,#d7dae6);background:light-dark(#f6f8fa,#161b22)}}';

    foreach ( $colours as $class => list ( $day, $night ) )
      $css .= ":where(.pad-highlight .hl-$class){color:$day}"
            . "@supports (color:light-dark(#000,#fff)){:where(.pad-highlight .hl-$class){color:light-dark($day,$night)}}";

    return '<style' . padStyleNonce () . '>' . $css
         . ':where(.pad-highlight .hl-com){font-style:italic}'
         . ':where(.pad-highlight .hl-line){display:inline-block;min-width:100%}'
         . ':where(.pad-highlight .hl-mark){background:rgba(255,200,0,.18)}'
         . ':where(.pad-highlight.hl-numbered .hl-line)::before{content:attr(data-line);display:inline-block;width:2.5em;margin-right:1em;text-align:right;opacity:.45;user-select:none}'
         . '</style>';

  }

?>
