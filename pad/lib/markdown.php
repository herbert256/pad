<?php

  // Markdown to HTML - the renderer behind the {markdown} tag and the markdown pipe.
  //
  // There is no Composer in PAD, so this is a small built-in CommonMark subset rather than a
  // library: ATX and setext headings, paragraphs, emphasis and strong emphasis, code spans,
  // fenced and indented code blocks, bullet and ordered lists (nested, tight or loose),
  // links, images, autolinks, block quotes, thematic breaks, hard line breaks and backslash
  // escapes. Reference-style links, tables and footnotes are left out.
  //
  // It is safe for content a visitor wrote: raw HTML in the text is escaped, never passed
  // on, and a link or image whose URL names a scheme other than http, https, mailto, ftp or
  // tel (javascript:, data:, vbscript: ...) loses its URL and keeps only its text. Only the
  // author of a template can let raw HTML through, with the html option of the tag. The
  // escaping leaves an entity that is already there alone, the way the sanitize chain does,
  // so a field the tag form rendered first - already encoded - and the &open; stand-ins of
  // the ignore option arrive in the page as they were meant.
  //
  // The text is dedented first: a {markdown} block indented along with the template around
  // it would otherwise turn into one big code block.

  function padMarkdown ( $text, $html = FALSE ) {

    if ( ! is_scalar ( $text ) )
      return '';

    $text = str_replace ( [ "\r\n", "\r", "\x1A" ], [ "\n", "\n", '' ], (string) $text );
    $text = padMarkdownTabs ( $text );

    $lines = explode ( "\n", padMarkdownDedent ( $text ) );
    $hold  = [];

    $out = padMarkdownBlocks ( $lines, $html, FALSE, $hold );

    return trim ( padMarkdownRestore ( $out, $hold ) );

  }

  // A tab at the start of a line counts as four spaces, the CommonMark tab stop, so an
  // indented list or code block measures the same however it was typed.

  function padMarkdownTabs ( $text ) {

    return preg_replace_callback ( '/^[ \t]+/m',
      function ( $m ) {
        $out = '';
        foreach ( str_split ( $m [0] ) as $char )
          $out .= ( $char == "\t" ) ? str_repeat ( ' ', 4 - strlen ( $out ) % 4 ) : ' ';
        return $out;
      },
      $text );

  }

  function padMarkdownDedent ( $text ) {

    $least = NULL;

    foreach ( explode ( "\n", $text ) as $line )
      if ( trim ( $line ) !== '' ) {
        $indent = strlen ( $line ) - strlen ( ltrim ( $line, ' ' ) );
        $least  = ( $least === NULL ) ? $indent : min ( $least, $indent );
      }

    if ( ! $least )
      return $text;

    return preg_replace ( '/^ {0,' . $least . '}/m', '', $text );

  }

  // HTML escaping that leaves an existing entity - &amp; &#123; &#x7B; &open; - as it is.
  // Only a whole reference counts: &#58top; is no entity, and left alone a browser reads
  // its &#58 as a colon, so [a](javascript&#58top;alert(1)) was a javascript: link.

  function padMarkdownEscape ( $text ) {

    $text = preg_replace ( '/&(?!(?:[a-zA-Z][a-zA-Z0-9]*|#[0-9]+|#[xX][0-9a-fA-F]+);)/', '&amp;', $text );

    return str_replace ( [ '<', '>', '"' ], [ '&lt;', '&gt;', '&quot;' ], $text );

  }

  // Finished HTML for a piece of inline text is parked in $hold and stands in the text as
  // \x1A<n>\x1A, so the later inline steps - escaping, emphasis - cannot touch it again.

  function padMarkdownHold ( $html, &$hold ) {

    $hold [] = $html;

    return "\x1A" . ( count ( $hold ) - 1 ) . "\x1A";

  }

  function padMarkdownRestore ( $text, $hold ) {

    for ( $i = 0; $i < 20 and str_contains ( $text, "\x1A" ); $i++ )
      $text = preg_replace_callback ( '/\x1A(\d+)\x1A/', fn ( $m ) => $hold [ (int) $m [1] ] ?? '', $text );

    return $text;

  }

  // The text of what was held inside a link's URL or title, which are attribute values: a
  // backslash escape is its character again, a code span or an autolink its text without
  // the tags. The URL is judged after this - javascript\:alert(1) is javascript:alert(1),
  // not a word with a stand-in where its colon was - and no held tag lands, quotes and
  // all, inside the attribute.

  function padMarkdownText ( $text, $hold ) {

    return preg_replace_callback ( '/\x1A(\d+)\x1A/',
      fn ( $m ) => strip_tags ( padMarkdownRestore ( $hold [ (int) $m [1] ] ?? '', $hold ) ),
      $text );

  }

  function padMarkdownIndent ( $line ) {

    return strlen ( $line ) - strlen ( ltrim ( $line, ' ' ) );

  }

  // A list item marker at the start of the line: [ indent, marker, delimiter type, start
  // number, width up to the content, content ] or FALSE.

  function padMarkdownItem ( $line ) {

    if ( ! preg_match ( '/^( {0,3})([-+*]|(\d{1,9})([.)]))( +|$)(.*)$/', $line, $m ) )
      return FALSE;

    if ( padMarkdownRule ( $line ) )
      return FALSE;

    $spaces = strlen ( $m [5] );

    if ( $spaces > 4 or $m [6] === '' )
      $spaces = 1;

    $type = ( $m [3] !== '' ) ? 'ol' . $m [4] : 'ul' . $m [2];

    return [
      'indent'  => strlen ( $m [1] ),
      'type'    => $type,
      'start'   => ( $m [3] !== '' ) ? (int) $m [3] : 1,
      'width'   => strlen ( $m [1] ) + strlen ( $m [2] ) + $spaces,
      'content' => ( strlen ( $m [5] ) > 4 ) ? substr ( $m [5], 1 ) . $m [6] : $m [6]
    ];

  }

  function padMarkdownRule ( $line ) {

    return preg_match ( '/^ {0,3}(?:(?:\*[ \t]*){3,}|(?:-[ \t]*){3,}|(?:_[ \t]*){3,})$/', $line );

  }

  function padMarkdownFence ( $line ) {

    if ( preg_match ( '/^( {0,3})(`{3,}|~{3,})[ \t]*([^`]*?)[ \t]*$/', $line, $m ) )
      if ( $m [2][0] == '~' or ! str_contains ( $m [3], '`' ) )
        return [ strlen ( $m [1] ), $m [2], $m [3] ];

    return FALSE;

  }

  // Does this line start a block of its own, so it ends a paragraph above it rather than
  // continuing it. An ordered item only interrupts a paragraph when it starts at 1.

  function padMarkdownStarts ( $line ) {

    if ( preg_match ( '/^ {0,3}(#{1,6}([ \t]|$)|>)/', $line ) ) return TRUE;
    if ( padMarkdownRule  ( $line ) )                           return TRUE;
    if ( padMarkdownFence ( $line ) )                           return TRUE;

    $item = padMarkdownItem ( $line );

    return $item and $item ['content'] !== '' and ( $item ['type'][0] == 'u' or $item ['start'] == 1 );

  }

  // The block structure: one pass over the lines, each block taking the lines it owns. A
  // list item and a block quote hand their own lines to a recursive call. $tight is set for
  // the items of a tight list, whose paragraphs are written without <p>.

  function padMarkdownBlocks ( $lines, $html, $tight, &$hold ) {

    $out = '';
    $n   = count ( $lines );
    $i   = 0;

    while ( $i < $n ) {

      $line = $lines [$i];

      if ( trim ( $line ) === '' ) {
        $i++;
        continue;
      }

      // Fenced code: ``` or ~~~, with an optional language that becomes the class.

      if ( $fence = padMarkdownFence ( $line ) ) {

        list ( $indent, $marker, $info ) = $fence;

        $code = [];
        $i++;

        while ( $i < $n and ! preg_match ( '/^ {0,3}' . preg_quote ( $marker [0], '/' ) . '{' . strlen ( $marker ) . ',}[ \t]*$/', $lines [$i] ) ) {
          $code [] = preg_replace ( '/^ {0,' . $indent . '}/', '', $lines [$i] );
          $i++;
        }

        $i++;

        $lang  = strtok ( $info, " \t" );
        $class = ( $lang !== FALSE and $lang !== '' ) ? ' class="language-' . padMarkdownEscape ( $lang ) . '"' : '';
        $body  = $code ? padMarkdownEscape ( implode ( "\n", $code ) ) . "\n" : '';

        $out .= "<pre><code$class>$body</code></pre>\n";
        continue;

      }

      // Indented code: four spaces in, blank lines inside it kept.

      if ( padMarkdownIndent ( $line ) >= 4 ) {

        $code = [];

        while ( $i < $n and ( trim ( $lines [$i] ) === '' or padMarkdownIndent ( $lines [$i] ) >= 4 ) )
          $code [] = substr ( $lines [$i++], 4 );

        while ( $code and trim ( end ( $code ) ) === '' )
          array_pop ( $code );

        $out .= '<pre><code>' . padMarkdownEscape ( implode ( "\n", $code ) ) . "\n</code></pre>\n";
        continue;

      }

      // ATX heading: # to ######, an optional closing run of # dropped.

      if ( preg_match ( '/^ {0,3}(#{1,6})(?:[ \t]+(.*?))?(?:[ \t]+#+)?[ \t]*$/', $line, $m ) ) {

        $level = strlen ( $m [1] );
        $text  = preg_replace ( '/^#+$/', '', $m [2] ?? '' );

        $out .= "<h$level>" . padMarkdownInline ( $text, $html, $hold ) . "</h$level>\n";
        $i++;
        continue;

      }

      if ( padMarkdownRule ( $line ) ) {
        $out .= "<hr />\n";
        $i++;
        continue;
      }

      // Block quote: the > lines, and the lines that lazily continue its paragraph.

      if ( preg_match ( '/^ {0,3}>/', $line ) ) {

        $quote = [];

        while ( $i < $n and trim ( $lines [$i] ) !== '' ) {

          if ( preg_match ( '/^ {0,3}> ?(.*)$/', $lines [$i], $m ) )
            $quote [] = $m [1];
          elseif ( $quote and ! padMarkdownStarts ( $lines [$i] ) )
            $quote [] = $lines [$i];
          else
            break;

          $i++;

        }

        $out .= "<blockquote>\n" . padMarkdownBlocks ( $quote, $html, FALSE, $hold ) . "</blockquote>\n";
        continue;

      }

      if ( padMarkdownItem ( $line ) ) {
        $out .= padMarkdownList ( $lines, $i, $html, $hold );
        continue;
      }

      // A block of raw HTML, for the html option only: a line that opens with a block-level
      // element or a comment, kept as it is up to a blank line. Inline markup - <b> - starts
      // an ordinary paragraph.

      if ( $html and preg_match ( '/^ {0,3}<(\/?(address|article|aside|blockquote|details|dialog|div|dl|fieldset|figcaption|figure|footer|form|h[1-6]|header|hr|li|main|nav|ol|p|pre|section|table|tbody|td|tfoot|th|thead|tr|ul|script|style)(\s|\/?>|$)|!--)/i', $line ) ) {

        $block = [];

        while ( $i < $n and trim ( $lines [$i] ) !== '' )
          $block [] = $lines [$i++];

        $out .= implode ( "\n", $block ) . "\n";
        continue;

      }

      // A paragraph, unless a line of = or - under it makes it a setext heading.

      $para = [ $line ];
      $i++;

      while ( $i < $n and trim ( $lines [$i] ) !== '' ) {

        if ( preg_match ( '/^ {0,3}(=+|-+)[ \t]*$/', $lines [$i], $m ) ) {
          $level = ( $m [1][0] == '=' ) ? 1 : 2;
          $out  .= "<h$level>" . padMarkdownInline ( padMarkdownLines ( $para ), $html, $hold ) . "</h$level>\n";
          $para  = [];
          $i++;
          break;
        }

        if ( padMarkdownStarts ( $lines [$i] ) )
          break;

        $para [] = $lines [$i++];

      }

      if ( ! $para )
        continue;

      $text = padMarkdownInline ( padMarkdownLines ( $para ), $html, $hold );

      $out .= $tight ? "$text\n" : "<p>$text</p>\n";

    }

    return $out;

  }

  function padMarkdownLines ( $lines ) {

    return rtrim ( implode ( "\n", array_map ( 'ltrim', $lines ) ) );

  }

  // A list: items of the same marker kind, each with the lines indented under it. The list
  // is loose - paragraphs in <p> - when a blank line separates two items or two blocks
  // inside one item.

  function padMarkdownList ( $lines, &$i, $html, &$hold ) {

    $n     = count ( $lines );
    $first = padMarkdownItem ( $lines [$i] );
    $type  = $first ['type'];
    $items = [];
    $loose = FALSE;

    while ( $i < $n ) {

      $item = padMarkdownItem ( $lines [$i] );

      if ( ! $item or $item ['type'] != $type )
        break;

      $width = $item ['width'];
      $body  = [ $item ['content'] ];
      $blank = FALSE;
      $i++;

      while ( $i < $n ) {

        $line = $lines [$i];

        if ( trim ( $line ) === '' ) {
          $body [] = '';
          $blank   = TRUE;
          $i++;
          continue;
        }

        if ( padMarkdownIndent ( $line ) >= $width ) {
          if ( $blank and trim ( implode ( '', $body ) ) !== '' )
            $loose = TRUE;
          $body [] = substr ( $line, $width );
          $blank   = FALSE;
          $i++;
          continue;
        }

        if ( $blank or padMarkdownStarts ( $line ) or padMarkdownItem ( $line ) )
          break;

        $body [] = $line;
        $i++;

      }

      // Blank lines at the end of an item belong between it and the next one.

      $trailing = FALSE;

      while ( $body and trim ( end ( $body ) ) === '' ) {
        array_pop ( $body );
        $trailing = TRUE;
      }

      if ( $trailing and $i < $n and ( $next = padMarkdownItem ( $lines [$i] ) ) and $next ['type'] == $type )
        $loose = TRUE;

      $items [] = $body;

      if ( $trailing and ! ( $i < $n and ( $next = padMarkdownItem ( $lines [$i] ) ) and $next ['type'] == $type ) )
        break;

    }

    $tag   = ( $type [0] == 'o' ) ? 'ol' : 'ul';
    $start = ( $tag == 'ol' and $first ['start'] != 1 ) ? ' start="' . $first ['start'] . '"' : '';
    $out   = "<$tag$start>\n";

    foreach ( $items as $body ) {

      $inner = rtrim ( padMarkdownBlocks ( $body, $html, ! $loose, $hold ) );

      if ( $loose )
        $out .= "<li>\n$inner\n</li>\n";
      elseif ( preg_match ( '/(<\/(ul|ol|pre|blockquote|h[1-6])>|<hr \/>)$/', $inner ) )
        $out .= "<li>$inner\n</li>\n";
      else
        $out .= "<li>$inner</li>\n";

    }

    return "$out</$tag>\n";

  }

  // A link or image URL is kept when it names no scheme - a relative path, a ?page link, an
  // #anchor - or one of the harmless ones. Anything else answers FALSE.

  function padMarkdownUrl ( $url ) {

    $url = trim ( $url );

    if ( str_starts_with ( $url, '<' ) and str_ends_with ( $url, '>' ) )
      $url = substr ( $url, 1, -1 );

    $plain = preg_replace ( '/[\x00-\x20]/', '', html_entity_decode ( $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

    if ( preg_match ( '/^([a-zA-Z][a-zA-Z0-9+.\-]*):/', $plain, $m ) )
      if ( ! in_array ( strtolower ( $m [1] ), [ 'http', 'https', 'mailto', 'ftp', 'tel' ] ) )
        return FALSE;

    return str_replace ( ' ', '%20', padMarkdownEscape ( $url ) );

  }

  // The inline structure of one block's text. Code spans and backslash escapes go first,
  // in one left-to-right pass - the one that starts first wins - then autolinks, raw HTML
  // (html option only), images and links; what they produce is held, the rest is escaped,
  // and emphasis and line breaks are marked up on the escaped text.

  function padMarkdownInline ( $text, $html, &$hold ) {

    $text = preg_replace_callback ( '/\\\\([!-\/:-@\[-`{-~])|(`+)(?!`)(.+?)(?<!`)\2(?!`)/s',
      function ( $m ) use ( &$hold ) {
        if ( $m [1] !== '' )
          return padMarkdownHold ( padMarkdownEscape ( $m [1] ), $hold );
        $code = str_replace ( "\n", ' ', $m [3] );
        if ( preg_match ( '/^ .*[^ ].* $/s', $code ) )
          $code = substr ( $code, 1, -1 );
        return padMarkdownHold ( '<code>' . padMarkdownEscape ( $code ) . '</code>', $hold );
      },
      $text );

    $text = preg_replace_callback ( '/<((?:https?|ftp|mailto):[^\s<>]*|[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)+)>/',
      function ( $m ) use ( &$hold ) {
        $href = str_contains ( $m [1], ':' ) ? $m [1] : 'mailto:' . $m [1];
        return padMarkdownHold ( '<a href="' . padMarkdownEscape ( $href ) . '">' . padMarkdownEscape ( $m [1] ) . '</a>', $hold );
      },
      $text );

    if ( $html )
      $text = preg_replace_callback ( '/<\/?[a-zA-Z][a-zA-Z0-9-]*(?:\s[^<>]*)?\/?>|<!--.*?-->/s',
        function ( $m ) use ( &$hold ) { return padMarkdownHold ( $m [0], $hold ); },
        $text );

    $text = preg_replace_callback (
      '/(!?)\[((?:[^\[\]]|\[[^\[\]]*\])*)\]\([ \t\n]*(<[^<>\n]*>|[^\s()]*(?:\([^\s()]*\)[^\s()]*)*)(?:[ \t\n]+("[^"]*"|\'[^\']*\'))?[ \t\n]*\)/',
      function ( $m ) use ( $html, &$hold ) {
        $image = ( $m [1] == '!' );
        $url   = padMarkdownUrl ( padMarkdownText ( $m [3], $hold ) );
        $title = isset ( $m [4] ) ? ' title="' . padMarkdownEscape ( padMarkdownText ( substr ( $m [4], 1, -1 ), $hold ) ) . '"' : '';
        $label = padMarkdownInline ( $m [2], $html, $hold );
        if ( $image ) {
          $alt = padMarkdownEscape ( strip_tags ( padMarkdownRestore ( $label, $hold ) ) );
          if ( $url === FALSE )
            return padMarkdownHold ( $alt, $hold );
          return padMarkdownHold ( "<img src=\"$url\" alt=\"$alt\"$title />", $hold );
        }
        if ( $url === FALSE )
          return padMarkdownHold ( $label, $hold );
        return padMarkdownHold ( "<a href=\"$url\"$title>$label</a>", $hold );
      },
      $text );

    $text = padMarkdownEscape ( $text );

    // Emphasis and line breaks. A pattern that gives up on a pathological text (PCRE's
    // backtrack limit) leaves the text as it was rather than empty.

    $marks = [
      '/(?<![*\w])\*\*\*(?=\S)(.+?)(?<=\S)\*\*\*(?![*\w])/s' => '<em><strong>$1</strong></em>',
      '/\*\*(?=[^\s*])(.+?)(?<=[^\s*])\*\*/s'                 => '<strong>$1</strong>',
      '/(?<!\w)__(?=[^\s_])(.+?)(?<=[^\s_])__(?!\w)/s'        => '<strong>$1</strong>',
      '/\*(?=[^\s*])(.+?)(?<=[^\s*])\*/s'                     => '<em>$1</em>',
      '/(?<!\w)_(?=[^\s_])(.+?)(?<=[^\s_])_(?!\w)/s'          => '<em>$1</em>',
      '/(?: {2,}|\\\\)\n/'                                  => "<br />\n",
      '/ +\n/'                                               => "\n"
    ];

    foreach ( $marks as $pattern => $replace )
      $text = preg_replace ( $pattern, $replace, $text ) ?? $text;

    return $text;

  }

?>
