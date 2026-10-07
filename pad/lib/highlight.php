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
  // The languages: pad, php, html (and xml, svg), css, js (and json5, ts), json, yaml, sql
  // and bash (and sh, shell). The text is cut into tokens by one combined pattern per
  // language - PHP by PHP's own tokenizer - and every piece, coloured or not, is
  // HTML-escaped, so the result is safe to print raw. A token is a <span class="hl-...">:
  // com(ment), str(ing), num(ber), lit(eral), kwd (keyword), key, var, tag, att(ribute),
  // opt(ion), brc (PAD brace), htm (an HTML tag in PAD), fn (function), pun(ctuation), def.

  function padHighlightLanguage ( $lang ) {

    $lang = strtolower ( trim ( (string) $lang ) );

    return [ 'xml' => 'html', 'svg' => 'html', 'htm' => 'html', 'javascript' => 'js', 'ts' => 'js', 'typescript' => 'js',
             'json5' => 'js', 'mjs' => 'js', 'yml' => 'yaml', 'sh' => 'bash', 'shell' => 'bash', 'zsh' => 'bash' ] [$lang] ?? $lang;

  }

  function padHighlightKnown ( $lang ) {

    return in_array ( padHighlightLanguage ( $lang ), [ 'pad', 'php', 'html', 'css', 'js', 'json', 'yaml', 'sql', 'bash', 'text' ], TRUE );

  }

  function padHighlightRules ( $lang ) {

    $kwJs  = 'async|await|break|case|catch|class|const|continue|default|delete|do|else|export|extends|finally|for|from|function|if|import|in|instanceof|let|new|of|return|static|super|switch|this|throw|try|typeof|var|void|while|yield';
    $kwSql = 'select|from|where|and|or|not|in|is|null|like|between|join|left|right|inner|outer|full|cross|on|as|group|by|order|having|limit|offset|union|all|distinct|insert|into|values|update|set|delete|create|table|index|view|drop|alter|add|primary|key|foreign|references|default|unique|case|when|then|else|end|asc|desc|count|sum|avg|min|max|exists|with|returning';
    $kwSh  = 'if|then|else|elif|fi|for|while|until|do|done|case|esac|in|function|return|local|export|readonly|exit|break|continue|source|echo|cd|set|unset|shift|true|false';

    return [

      'json' => [ 'key' => '"(?:[^"\\\\\n]|\\\\.)*"(?=\s*:)',
                  'str' => '"(?:[^"\\\\\n]|\\\\.)*"',
                  'num' => '-?\b\d+(?:\.\d+)?(?:[eE][+-]?\d+)?\b',
                  'lit' => '\b(?:true|false|null)\b',
                  'pun' => '[{}\[\],:]' ],

      'yaml' => [ 'com' => '(?<![^\s])#[^\n]*',
                  'pun' => '^[ \t]*-(?=[ \t])|^---$',
                  'key' => '[A-Za-z_][\w .-]*(?=:(?:[ \t]|$))',
                  'str' => '\'[^\'\n]*\'|"[^"\n]*"',
                  'lit' => '(?<=:[ \t])(?:true|false|null|~)(?=[ \t]*$)',
                  'num' => '(?<=:[ \t]|-[ \t])-?\d+(?:\.\d+)?(?=[ \t]*$)' ],

      'pad'  => [ 'com' => '\{#.*?#\}|\{--\s.*?--\}|<!--.*?-->',
                  'tag' => '(?<=\{)\/?[A-Za-z][\w:@]*',
                  'brc' => '[{}]',
                  'htm' => '<\/?[A-Za-z][\w-]*|\/?>',
                  'opt' => '\b[A-Za-z][\w-]*(?==)',
                  'str' => '\'[^\'\n]*\'|"[^"\n]*"',
                  'var' => '[$%#!&^?]\$?\w+(?:\.\w+)*|@\w+@',
                  'num' => '\b\d+(?:\.\d+)?\b' ],

      'html' => [ 'com' => '<!--.*?-->',
                  'kwd' => '<!DOCTYPE[^>]*>|<\?xml.*?\?>',
                  'tag' => '<\/?[A-Za-z][\w:.-]*|\/?>',
                  'att' => '(?<=\s)[A-Za-z_:][\w:.-]*(?=\s*=)',
                  'str' => '"[^"]*"|\'[^\']*\'',
                  'lit' => '&[#\w]+;' ],

      'css'  => [ 'com' => '\/\*.*?\*\/',
                  'kwd' => '@[\w-]+',
                  'str' => '"[^"\n]*"|\'[^\'\n]*\'',
                  'key' => '(?<![\w-])--?[A-Za-z][\w-]*(?=\s*:(?!:))|\b[a-z-]+(?=\s*:[^:{};]*;)',
                  'num' => '#[0-9a-fA-F]{3,8}\b|-?\b\d+(?:\.\d+)?(?:%|[a-z]+)?\b',
                  'fn'  => '\b[\w-]+(?=\()',
                  'pun' => '[{}();:,]' ],

      'js'   => [ 'com' => '\/\/[^\n]*|\/\*.*?\*\/',
                  'str' => '"(?:[^"\\\\\n]|\\\\.)*"|\'(?:[^\'\\\\\n]|\\\\.)*\'|`(?:[^`\\\\]|\\\\.)*`',
                  'kwd' => "\\b(?:$kwJs)\\b",
                  'lit' => '\b(?:true|false|null|undefined|NaN|Infinity)\b',
                  'num' => '\b\d+(?:\.\d+)?(?:[eE][+-]?\d+)?\b|\b0x[0-9a-fA-F]+\b',
                  'fn'  => '\b[A-Za-z_$][\w$]*(?=\s*\()',
                  'pun' => '[{}\[\]();,.]|=>' ],

      'sql'  => [ 'com' => '--[^\n]*|\/\*.*?\*\/',
                  'str' => '\'(?:[^\']|\'\')*\'',
                  'key' => '"[^"\n]*"|`[^`\n]*`',
                  'kwd' => "(?i)\\b(?:$kwSql)\\b(?-i)",
                  'num' => '\b\d+(?:\.\d+)?\b',
                  'var' => '[:@?]\w*|\{\$\w+\}',
                  'pun' => '[(),;.*=<>]' ],

      'bash' => [ 'com' => '(?<![^\s])#[^\n]*',
                  'str' => '"(?:[^"\\\\]|\\\\.)*"|\'[^\']*\'',
                  'var' => '\$\{[^}\n]*\}|\$[\w@#?$!*-]',
                  'kwd' => "\\b(?:$kwSh)\\b",
                  'opt' => '(?<=\s)--?[A-Za-z][\w-]*',
                  'num' => '\b\d+\b',
                  'pun' => '[|&;<>]+|\$\(|[()]' ]

    ] [ padHighlightLanguage ( $lang ) ] ?? [];

  }

  function padHighlightTokens ( $text, $lang ) {

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

    if ( preg_match_all ( '/' . implode ( '|', $parts ) . '/ms', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL ) === FALSE )
      return padHighlightEscape ( $text );

    $html = '';
    $at   = 0;

    foreach ( $matches as $match ) {

      list ( $token, $offset ) = $match [0];

      foreach ( array_keys ( $rules ) as $name )
        if ( isset ( $match [$name] [0] ) )
          break;

      $html .= padHighlightEscape ( substr ( $text, $at, $offset - $at ) )
             . "<span class=\"hl-$name\">" . padHighlightEscape ( $token ) . '</span>';

      $at = $offset + strlen ( $token );

    }

    return $html . padHighlightEscape ( substr ( $text, $at ) );

  }

  // PHP is cut by PHP's own tokenizer, each token a class by its kind - flat spans, never
  // one inside another. A text without an opening tag is read as code anyway - the snippet
  // of a function body.

  function padHighlightPhp ( $text ) {

    $open   = ! preg_match ( '/<\?(php|=)/i', $text );
    $tokens = token_get_all ( $open ? "<?php $text" : $text );
    $html   = '';

    if ( $open )
      array_shift ( $tokens );

    foreach ( $tokens as $i => $token ) {

      if ( ! is_array ( $token ) ) {
        $html .= '<span class="hl-pun">' . padHighlightEscape ( $token ) . '</span>';
        continue;
      }

      list ( $id, $part ) = $token;

      $next = '';
      for ( $j = $i + 1; isset ( $tokens [$j] ); $j++ )
        if ( ! is_array ( $tokens [$j] ) or $tokens [$j] [0] != T_WHITESPACE ) {
          $next = is_array ( $tokens [$j] ) ? $tokens [$j] [1] : $tokens [$j];
          break;
        }

      switch ( TRUE ) {
        case $id == T_WHITESPACE:                                              $class = '';    break;
        case in_array ( $id, [ T_COMMENT, T_DOC_COMMENT ] ):                    $class = 'com'; break;
        case in_array ( $id, [ T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE, T_START_HEREDOC, T_END_HEREDOC ] ): $class = 'str'; break;
        case $id == T_VARIABLE:                                                $class = 'var'; break;
        case in_array ( $id, [ T_LNUMBER, T_DNUMBER ] ):                       $class = 'num'; break;
        case in_array ( $id, [ T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLOSE_TAG ] ): $class = 'tag'; break;
        case $id == T_INLINE_HTML:                                             $class = 'htm'; break;
        case $id == T_STRING and in_array ( strtolower ( $part ), [ 'true', 'false', 'null' ] ): $class = 'lit'; break;
        case $id == T_STRING and $next == '(':                                 $class = 'fn';  break;
        case $id == T_STRING:                                                  $class = 'def'; break;
        case ctype_alpha ( str_replace ( '_', '', $part ) ):                   $class = 'kwd'; break;
        default:                                                               $class = 'pun';
      }

      // The opening tag carries the line break after it: kept outside the span, so no
      // line starts inside one.

      $tail = ( $class == 'tag' ) ? substr ( $part, strlen ( rtrim ( $part ) ) ) : '';
      $part = substr ( $part, 0, strlen ( $part ) - strlen ( $tail ) );

      $html .= ( $class === '' ) ? padHighlightEscape ( $part ) : "<span class=\"hl-$class\">" . padHighlightEscape ( $part ) . '</span>' . $tail;

    }

    return $html;

  }

  function padHighlightEscape ( $text ) {

    return htmlspecialchars ( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The block: the text with its common indent taken off, coloured, line numbers when asked,
  // and the lines named in mark= ('3, 5-7') set apart.

  function padHighlight ( $text, $lang, $lines = FALSE, $mark = '' ) {

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

  function padHighlightMarked ( $mark ) {

    $marked = [];

    foreach ( explode ( ',', (string) $mark ) as $part )
      if ( preg_match ( '/^\s*(\d+)\s*(?:-\s*(\d+))?\s*$/', $part, $m ) )
        for ( $i = (int) $m [1]; $i <= (int) ( $m [2] ?? $m [1] ) and $i - $m [1] < 10000; $i++ )
          $marked [$i] = TRUE;

    return $marked;

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

    return '<style>' . $css
         . ':where(.pad-highlight .hl-com){font-style:italic}'
         . ':where(.pad-highlight .hl-line){display:inline-block;min-width:100%}'
         . ':where(.pad-highlight .hl-mark){background:rgba(255,200,0,.18)}'
         . ':where(.pad-highlight.hl-numbered .hl-line)::before{content:attr(data-line);display:inline-block;width:2.5em;margin-right:1em;text-align:right;opacity:.45;user-select:none}'
         . '</style>';

  }

?>
