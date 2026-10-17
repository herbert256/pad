<?php

  // The workings of the syntax colouring (lib/highlight.php): the language a name stands
  // for, the combined pattern of each language, PHP through PHP's own tokenizer, the
  // escaping, the marked lines - read by padHighlightTokens, padHighlight and
  // padHighlightKnown when they colour a text, and with the {highlight} tag.

  function padHighlightLanguage ( $lang ) {

    $lang = strtolower ( trim ( (string) $lang ) );

    return [ 'xml' => 'html', 'svg' => 'html', 'htm' => 'html', 'javascript' => 'js', 'ts' => 'js', 'typescript' => 'js',
             'json5' => 'js', 'mjs' => 'js', 'yml' => 'yaml', 'sh' => 'bash', 'shell' => 'bash', 'zsh' => 'bash' ] [$lang] ?? $lang;

  }

  // A string is matched as a run of plain characters with its escapes between, each run
  // possessive: as a group repeated per character, a string of some nine thousand - a base64
  // picture, an HTML template - ran out of PCRE's JIT stack, and the whole text went
  // uncoloured.

  function padHighlightRules ( $lang ) {

    $kwJs  = 'async|await|break|case|catch|class|const|continue|default|delete|do|else|export|extends|finally|for|from|function|if|import|in|instanceof|let|new|of|return|static|super|switch|this|throw|try|typeof|var|void|while|yield';
    $kwSql = 'select|from|where|and|or|not|in|is|null|like|between|join|left|right|inner|outer|full|cross|on|as|group|by|order|having|limit|offset|union|all|distinct|insert|into|values|update|set|delete|create|table|index|view|drop|alter|add|primary|key|foreign|references|default|unique|case|when|then|else|end|asc|desc|count|sum|avg|min|max|exists|with|returning';
    $kwSh  = 'if|then|else|elif|fi|for|while|until|do|done|case|esac|in|function|return|local|export|readonly|exit|break|continue|source|echo|cd|set|unset|shift|true|false';

    return [

      'json' => [ 'key' => '"[^"\\\\\n]*+(?:\\\\.[^"\\\\\n]*+)*+"(?=\s*:)',
                  'str' => '"[^"\\\\\n]*+(?:\\\\.[^"\\\\\n]*+)*+"',
                  'num' => '-?\b\d+(?:\.\d+)?(?:[eE][+-]?\d+)?\b',
                  'lit' => '\b(?:true|false|null)\b',
                  'pun' => '[{}\[\],:]' ],

      'yaml' => [ 'com' => '(?<![^\s])#[^\n]*',
                  'pun' => '^[ \t]*-(?=[ \t])|^---$',
                  'key' => '[A-Za-z_][\w .-]*(?=:(?:[ \t]|$))',
                  'str' => '\'[^\'\n]*\'|"[^"\n]*"',
                  'lit' => '(?<=:[ \t])(?:true|false|null|~)(?=[ \t]*$)',
                  'num' => '(?<=:[ \t]|-[ \t])-?\d+(?:\.\d+)?(?=[ \t]*$)' ],

      'pad'  => [ 'com' => '\{#(?![A-Za-z_]\w*\s*[}|])(?:[^{#]++|\{(?!#)|#(?!\}))*+#\}|\{--\s.*?--\}|<!--.*?-->',
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
                  'str' => '"[^"\\\\\n]*+(?:\\\\.[^"\\\\\n]*+)*+"|\'[^\'\\\\\n]*+(?:\\\\.[^\'\\\\\n]*+)*+\'|`[^`\\\\]*+(?:\\\\.[^`\\\\]*+)*+`',
                  'kwd' => "\\b(?:$kwJs)\\b",
                  'lit' => '\b(?:true|false|null|undefined|NaN|Infinity)\b',
                  'num' => '\b\d+(?:\.\d+)?(?:[eE][+-]?\d+)?\b|\b0x[0-9a-fA-F]+\b',
                  'fn'  => '\b[A-Za-z_$][\w$]*(?=\s*\()',
                  'pun' => '[{}\[\]();,.]|=>' ],

      'sql'  => [ 'com' => '--[^\n]*|\/\*.*?\*\/',
                  'str' => '\'[^\']*+(?:\'\'[^\']*+)*\'',
                  'key' => '"[^"\n]*"|`[^`\n]*`',
                  'kwd' => "(?i)\\b(?:$kwSql)\\b(?-i)",
                  'num' => '\b\d+(?:\.\d+)?\b',
                  'var' => '[:@?]\w*|\{\$\w+\}',
                  'pun' => '[(),;.*=<>]' ],

      'bash' => [ 'com' => '(?<![^\s])#[^\n]*',
                  'str' => '"[^"\\\\]*+(?:\\\\.[^"\\\\]*+)*+"|\'[^\']*\'',
                  'var' => '\$\{[^}\n]*\}|\$[\w@#?$!*-]',
                  'kwd' => "\\b(?:$kwSh)\\b",
                  'opt' => '(?<=\s)--?[A-Za-z][\w-]*',
                  'num' => '\b\d+\b',
                  'pun' => '[|&;<>]+|\$\(|[()]' ]

    ] [ padHighlightLanguage ( $lang ) ] ?? [];

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

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  function padHighlightMarked ( $mark ) {

    $marked = [];

    foreach ( explode ( ',', (string) $mark ) as $part )
      if ( preg_match ( '/^\s*(\d+)\s*(?:-\s*(\d+))?\s*$/', $part, $m ) )
        for ( $i = (int) $m [1]; $i <= (int) ( $m [2] ?? $m [1] ) and $i - $m [1] < 10000; $i++ )
          $marked [$i] = TRUE;

    return $marked;

  }

?>
