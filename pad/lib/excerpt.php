<?php

  // Excerpts - the {excerpt} tag. A long text cut to a number of whole words, with an
  // ellipsis where it was cut; given search words, the cut is centred on the first place
  // one of them stands and every match is marked.
  //
  //   {excerpt $post, words=40}
  //   {excerpt $page.body, words=30, highlight=$q, html}
  //
  // padExcerpt        the excerpt as HTML: escaped, the matches in <mark>
  // padExcerptTerms   the search words of a query, longest first - fifty at most, each cut
  //                   to 64 letters
  //
  // A search word matches at the start of a word, whatever its case: 'templ' finds
  // Templates, and the whole word is marked. The text is escaped piece by piece
  // and only the <mark> around a match is markup, so nothing of the text can become HTML.

  function padExcerptTerms ( $query ) {

    $terms = [];

    // A broken byte - ?q=%FF - made preg_split answer FALSE and ended the request.

    // A word matches at the start of a word, so its first 64 letters find what it finds; a
    // query of thousands of words - a search form that posts - or one word of tens of
    // thousands of letters made a pattern PCRE refuses to compile, and the request ended.

    foreach ( preg_split ( '/[^\p{L}\p{N}_]+/u', mb_scrub ( (string) $query, 'UTF-8' ), -1, PREG_SPLIT_NO_EMPTY ) as $term )
      $terms [ mb_strtolower ( mb_substr ( $term, 0, 64, 'UTF-8' ), 'UTF-8' ) ] = TRUE;

    $terms = array_keys ( $terms );

    usort ( $terms, fn ( $a, $b ) => mb_strlen ( $b ) <=> mb_strlen ( $a ) ?: strcmp ( $a, $b ) );

    return array_slice ( $terms, 0, 50 );

  }

  function padExcerpt ( $text, $words, $query, $html, $ellipsis ) {

    $text = mb_scrub ( (string) $text, 'UTF-8' );

    // A block tag and a <br> part the words on their sides, as a reader sees them: dropped
    // whole, step.</p><p>PAD read as the one word step.PAD.

    if ( $html )
      $text = html_entity_decode ( strip_tags ( preg_replace ( '#<(?=/?(?:p|div|li|h[1-6]|br|hr|tr|td|th|dt|dd|ul|ol|dl|table|caption|pre|blockquote|section|article|aside|header|footer|nav|main|figure|figcaption|address|details|summary)\b)#i', ' <', padExcerptCode ( $text ) ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );

    $tokens = preg_split ( '/\s+/u', trim ( (string) $text ), -1, PREG_SPLIT_NO_EMPTY );
    $terms  = padExcerptTerms ( $query );
    $match  = $terms ? '/(?<![\p{L}\p{N}_])(?:' . implode ( '|', array_map ( fn ( $t ) => preg_quote ( $t, '/' ), $terms ) ) . ')[\p{L}\p{N}_]*/iu' : '';
    $count  = count ( $tokens );
    $start  = 0;

    // The window: the first match in its middle, moved in when it would run past an end.

    if ( $count > $words ) {

      if ( $match !== '' )
        foreach ( $tokens as $i => $token )
          if ( preg_match ( $match, $token ) ) {
            $start = max ( 0, min ( $count - $words, $i - intdiv ( $words, 2 ) ) );
            break;
          }

      $tokens = array_slice ( $tokens, $start, $words );

    }

    $out = [];

    foreach ( $tokens as $token )
      $out [] = ( $match === '' ) ? padExcerptEscape ( $token ) : padExcerptMark ( $token, $match );

    $before = ( $start > 0 ) ? $ellipsis . ' ' : '';
    $after  = ( $start + count ( $tokens ) < $count ) ? ' ' . $ellipsis : '';

    return padExcerptStyle () . '<span class="pad-excerpt">' . padExcerptEscape ( $before ) . implode ( ' ', $out ) . padExcerptEscape ( $after ) . '</span>';

  }

  // HTML without its scripts and styles, a space where each stood - an opener without its
  // closer loses its own tag. One pass from closer to closer: the lazy pattern scanned to
  // the end of the text from every opener without one, seconds for half a megabyte, and
  // gave up past a megabyte - the whole excerpt lost.

  function padExcerptCode ( $html ) {

    $out  = '';
    $at   = 0;
    $none = [];

    while ( preg_match ( '#<(script|style)\b#i', $html, $open, PREG_OFFSET_CAPTURE, $at ) ) {

      $name  = strtolower ( $open [1] [0] );
      $from  = $open [0] [1];
      $close = isset ( $none [$name] ) ? FALSE : stripos ( $html, "</$name", $from );

      if ( $close === FALSE ) {
        $none [$name] = TRUE;
        $close        = $from;
      }

      $end  = strpos ( $html, '>', $close );
      $out .= substr ( $html, $at, $from - $at ) . ' ';
      $at   = ( $end === FALSE ) ? strlen ( $html ) : $end + 1;

    }

    return $out . substr ( $html, $at );

  }

  // One word: the pieces between the matches escaped, the matches escaped inside a <mark>.

  function padExcerptMark ( $token, $match ) {

    $html = '';

    foreach ( preg_split ( $match, $token, -1, PREG_SPLIT_OFFSET_CAPTURE ) as $n => list ( $piece, $at ) ) {
      if ( $n > 0 ) {
        $end   = $last [1] + strlen ( $last [0] );
        $html .= '<mark>' . padExcerptEscape ( substr ( $token, $end, $at - $end ) ) . '</mark>';
      }
      $html .= padExcerptEscape ( $piece );
      $last  = [ $piece, $at ];
    }

    return $html;

  }

  function padExcerptEscape ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The marks, once per page: their colour a custom property with a light-dark() default.

  function padExcerptStyle () {

    if ( ! padWidgetOnce ( 'style:excerpt' ) )
      return '';

    return '<style' . padStyleNonce () . '>'
         . ':where(.pad-excerpt){--pad-excerpt-mark:#fde68a;--pad-excerpt-marked:#1a1a19}'
         . '@supports (color:light-dark(#000,#fff)){:where(.pad-excerpt){--pad-excerpt-mark:light-dark(#fde68a,#6b5310);--pad-excerpt-marked:light-dark(#1a1a19,#fdf6dc)}}'
         . ':where(.pad-excerpt mark){background:var(--pad-excerpt-mark);color:var(--pad-excerpt-marked);border-radius:3px;padding:0 2px}'
         . '</style>';

  }

?>
