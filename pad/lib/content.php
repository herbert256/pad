<?php

  // Merges what a tag produced back into the level's content, honouring the @content@ and
  // @else@ markers and the merge= parameter. Driven from level/go.php.
  //
  // padContentMerge       splits $new on @else@ and adds each half to the true or the
  //                       false content, depending on whether the tag hit
  // padContentSet         wraps one string around the other where @content@ appears, and
  //                       falls back to merge='top' (default), 'bottom' or 'replace'
  // padContentFill        puts the fill at every @content@ of the text's own level
  // padContentElse        finds the @else@ that sits outside any nested tag pair
  // padContentBeforeAfter the same search for @content@, returning the text either side
  //
  // Both searches use padOpenCloseCount from lib/template.php to skip markers that belong
  // to an inner tag, so nesting cannot be broken by a marker in a child. An @content@
  // inside a {slot} pair is that slot's frame for its fill (tags/slot.php), not the place
  // the tag's own content goes; padContentOwn says which pairs an @content@ belongs to.

  function padContentMerge ( &$true, &$false, $new, $condition ) {

    padContentElse ( $new, $newTrue, $newFalse ) ;

    if ( $condition ) $true  = padContentSet ( $true,  $newTrue );
    else              $false = padContentSet ( $false, $newFalse );

  }

  function padContentSet ( $base, $new ) {

    $set = padContentFill ( $new, $base );

    if ( $set !== FALSE )
      return $set;

    $set = padContentFill ( $base, $new );

    if ( $set !== FALSE )
      return $set;

    $merge = padTagParm ( 'merge', 'top' );

    if     ( $merge == 'bottom'  ) return $base . $new;
    elseif ( $merge == 'replace' ) return $new;

    // Any other word answered nothing at all, and the NULL ended the request in a PHP
    // deprecation far from the cause - merge='after'. Strict mode names it; the lenient walk
    // merges on top, the default.

    if ( $merge != 'top' and $GLOBALS ['padCheckSyntax'] )
      padError ( "there is no merge named '$merge' - it is top, bottom or replace" );

    return $new . $base;

  }

  // The text a construct marker is looked for in. A template read from its file - the page
  // itself, an application tag's .pad, an _include snippet - meets the marker searches before
  // the occurrence takes its whitespace control off (occurrence/init.php), and the balance
  // helpers know no ~: {~rows~} opened no rows pair there, so the @else@ inside it was taken
  // as the template's own and the text cut in the middle of that pair - the page ended at
  // [{~none~}x, a tag template lost the rest. This copy has the ~ of a tag's brace moved
  // just outside it, {~/rows~} reading " {/rows} ", so it is exactly as long as the template
  // and a position found in it is the same position in the template.
  //
  // Its comments are written over with spaces (padCommentStrip in lib/level.php): a comment
  // is gone before the template is scanned, and so is a marker the author wrote in one -
  // A{-- shows its @else@ part --}B cut the page inside the comment, and a tag template's
  // {-- the caller's content lands at @content@ --} took the caller's content into the
  // comment, which was then stripped with it.
  //
  // And a marker inside the braces of a tag is written over: it is a value of the tag - a
  // quoted parameter, {echo 'x@else@y'}, {if $s eq '@else@'} - never the content of a level.
  // The balance of the pairs did not see it, a single tag being no pair, and the level was
  // cut in the middle of the tag: "No close } found for open {".

  function padMarkerText ( $text ) {

    if ( ! is_string ( $text ) )
      return $text;

    $text = padCommentStrip ( $text, TRUE );

    if ( str_contains ( $text, '~' ) ) {
      $text = preg_replace ( '/\{~(?=[\/A-Za-z_$!#&?^@])/', ' {', $text );
      $text = preg_replace ( '/(\{[\/A-Za-z_$!#&?^@][^{}]*?)~\}/', '$1} ', $text );
    }

    return padMarkerInTags ( $text );

  }

  // The text an @else@ is looked for in: padMarkerText, with the content of its text pairs -
  // what an {ignore} or a {highlight} sample shows - written over with spaces as well
  // (padTextFree, lib/template.php), the same length. A sample is text: the marker in one is
  // no marker, and neither is an opener or closer it shows - but those were counted, a
  // {/echo} in a sample put echo among the pairs to balance and threw its count off, so
  // [{sequence '1..2'}{ignore}{/echo}{/ignore}{echo 'a'}{echo 'b'}@else@ x{/sequence}] left
  // the @else@ to no tag at all - "an @else@ stands where no tag takes it" - and so did a
  // {/dd} shown in front of two uses of a {define}d dd. With one {echo} the counts met by
  // chance. A text pair still open at the end leaves the text as it is.

  function padElseText ( $text ) {

    $text  = padMarkerText ( $text );
    $blank = is_string ( $text ) ? padTextFree ( $text ) : FALSE;

    return is_string ( $blank ) ? $blank : $text;

  }

  // A tag opens with a { that a name, a sigil or a slash follows, and ends at the } that
  // brings the brace depth back - a parameter may hold a tag of its own, {echo {$x}}. One
  // that never closes ends the search: what follows stays as it is.
  //
  // The text is read once and written once, the tags that hold an @ written over as they
  // are passed - rewriting the whole text for each of them made the search grow with the
  // size times the tags, 139 ms for a template of 375 KB full of {current@items}. Where the
  // next @ stands is kept, so a tag without one costs no search either.

  function padMarkerInTags ( $text ) {

    $out  = '';
    $last = 0;
    $at   = 0;
    $len  = strlen ( $text );
    $mark = strpos ( $text, '@' );

    while ( $mark !== FALSE and ( $at = strpos ( $text, '{', $at ) ) !== FALSE ) {

      if ( ! preg_match ( '/\G\{[\/A-Za-z_$!#&?^@]/', $text, $match, 0, $at ) ) {
        $at++;
        continue;
      }

      $depth = 0;

      for ( $i = $at; $i < $len; $i += 1 + strcspn ( $text, '{}', $i + 1 ) )
        if ( $text [$i] == '{' )
          $depth++;
        elseif ( ! --$depth )
          break;

      if ( $i >= $len )
        break;

      if ( $mark < $at )
        $mark = strpos ( $text, '@', $at );

      if ( $mark !== FALSE and $mark < $i ) {
        $out .= substr ( $text, $last, $at - $last )
              . preg_replace_callback ( '/@(else|content|start|end)@/', fn ( $m ) => str_repeat ( ' ', strlen ( $m [0] ) ),
                                        substr ( $text, $at, $i - $at + 1 ) );
        $last = $i + 1;
      }

      $at = $i + 1;

    }

    return $last ? $out . substr ( $text, $last ) : $text;

  }

  // Every @content@ of the text's own level takes the fill - a template may show the content
  // in more than one place, <a>@content@</a><b>@content@</b>. Only the first was filled, and
  // the strict check then blamed the second as one that "nothing merges content into". FALSE
  // when the text has none.

  function padContentFill ( $text, $fill ) {

    if ( ! padContentBeforeAfter ( $text, $before, $after ) )
      return FALSE;

    $out = $before . $fill;

    while ( padContentBeforeAfter ( $after, $before, $rest ) ) {
      $out  .= $before . $fill;
      $after = $rest;
    }

    return $out . $after;

  }

  // A level has one @else@, the separator of its two halves. A second one at the depth of
  // the first separates nothing - the false half printed it as text, {none}A@else@B@else@C
  // {/none} showing B@else@C - and strict mode names it, where level/split.php and
  // build/split.php cut at the first. $pos is the first, $pairs the pair list it was
  // placed with.
  //
  // The markers behind the first are judged in one walk: the openers and closers of the
  // pairs, read as padOpenCount and padOpenCloseCountOne read them, are taken in the order
  // they stand, and at each marker the pairs still open are known. Counting the whole text
  // in front of every marker again took seconds for a page with thousands of nested ones.

  function padElseSecond ( $text, $pos, $pairs ) {

    if ( strpos ( $text, '@else@', $pos + 6 ) === FALSE )
      return;

    $events = [];

    foreach ( $pairs as $tag => $dummy ) {

      $quoted = preg_quote ( $tag, '/' );

      preg_match_all ( '/\{' . $quoted . '(?=[\s}])/', $text, $found, PREG_OFFSET_CAPTURE );
      foreach ( $found [0] as list ( $open, $at ) )
        if ( $text [$at + strlen ( $open )] == '}' or ! padOpenSelfClosing ( $text, $at + strlen ( $open ) ) )
          $events [] = [ $at, $tag, 1 ];

      preg_match_all ( '/\{\/' . $quoted . '[\s}|]/', $text, $found, PREG_OFFSET_CAPTURE );
      foreach ( $found [0] as list ( $close, $at ) )
        $events [] = [ $at, $tag, -1 ];

    }

    usort ( $events, fn ( $a, $b ) => $a [0] <=> $b [0] );

    $depth = [];
    $open  = 0;
    $next  = 0;
    $count = count ( $events );

    for ( $at = strpos ( $text, '@else@', $pos + 6 ); $at !== FALSE; $at = strpos ( $text, '@else@', $at + 1 ) ) {

      for ( ; $next < $count and $events [$next] [0] < $at; $next++ ) {
        list ( , $tag, $step ) = $events [$next];
        $was           = $depth [$tag] ?? 0;
        $depth [$tag]  = $was + $step;
        $open         += ( $was == 0 ) - ( $depth [$tag] == 0 );
      }

      if ( ! $open )
        return padError ( "a tag has one @else@ - a second one stands beside the first" );

    }

  }

  // content= on a tag that chooses among branches - an {if}, a {case}, a condition tag -
  // whose own content frames it, {if $x, content='c'}<b>@content@</b>{/if}: the frame goes
  // round each branch of the stored text, the part behind each of its own {else}, {elseif}
  // or {when}, so whichever the tag keeps is framed whole. Put in at the one @content@, the
  // stored yes{else}no made <b>yes{else}no</b>, and the tag kept <b>yes or no</b> - the
  // frame's two halves in its two branches. For a {case} the text before its first {when}
  // stays as it is: it never renders. Without branch tags, or with a stored text that frames
  // the tag's own, it is padContentSet as for any tag.

  function padContentBranches ( $base, $new, $case ) {

    if ( padContentFill ( $new, '' ) !== FALSE or padContentFill ( $base, '' ) === FALSE )
      return padContentSet ( $base, $new );

    $words = $case ? 'when[\s}]|else\}' : 'elseif[\s}]|else\}';

    preg_match_all ( '/\{(?:' . $words . ')/', $new, $found, PREG_OFFSET_CAPTURE );

    $out  = '';
    $last = 0;

    foreach ( $found [0] as list ( $tag, $at ) ) {

      if ( $at < $last or ! padElseFree ( substr ( $new, 0, $at ), $new ) )
        continue;

      $part = substr ( $new, $last, $at - $last );
      $out .= ( $case and $last == 0 ) ? $part : padContentFill ( $base, $part );

      padBranchText ( $new, $at, 1, $end );

      $out .= substr ( $new, $at, $end - $at + 1 );
      $last = $end + 1;

    }

    if ( $last == 0 )
      return padContentSet ( $base, $new );

    return $out . padContentFill ( $base, substr ( $new, $last ) );

  }

  function padContentElse ( $input, &$before, &$after ) {

    $pos  = strpos ( $input, '@else@' );
    $text = ( $pos === FALSE ) ? '' : padElseText ( $input );
    $pos  = ( $pos === FALSE ) ? FALSE : strpos ( $text, '@else@' );
    $list = ( $pos === FALSE ) ? [] : padOpenCloseList ( $text ) ;

    while ( $pos !== FALSE) {

      if  ( padOpenCloseCount ( substr ( $text, 0, $pos ), $list ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+6  );
        return;
      }

      $pos = strpos ( $text, '@else@', $pos+1 );

    }

    $before = $input;
    $after  = '';

  }

  function padContentBeforeAfter ( $input, &$before, &$after ) {

    $pos  = strpos ( $input, '@content@' );
    $text = ( $pos === FALSE ) ? '' : padMarkerText ( $input );
    $pos  = ( $pos === FALSE ) ? FALSE : strpos ( $text, '@content@' );
    $own  = ( $pos === FALSE ) ? [] : padContentPairs ( $text );

    while ( $pos !== FALSE) {

      if  ( padContentOwn ( $own, $pos ) ) {
        $before = substr ( $input, 0, $pos );
        $after  = substr ( $input, $pos+9  );
        return TRUE;
      }

      $pos = strpos ( $text, '@content@', $pos+1 );

    }

    return FALSE;

  }

  // Whether an @content@ at this place is the text's own: not inside a {content} or {slot}
  // pair, whose frame it is, nor inside a {define} or {macro} written in the template - that
  // tag's own marker, filled where it is used - nor inside an {ignore} or {highlight} sample,
  // where it is text to show. Loops and conditions stay transparent: the content goes in
  // where the template puts it. A nested define's marker took the caller's content, and a
  // sample explaining @content@ printed the caller's template as its source.

  function padContentOwn ( $pairs, $pos ) {

    foreach ( $pairs as list ( $from, $to ) )
      if ( $from < $pos and $pos < $to )
        return FALSE;

    return TRUE;

  }

  // Where those pairs stand in the text: a closing tag pairs with the nearest opening tag
  // before it that has none yet, as the engine pairs them, and an opening tag left over is a
  // single tag that opens nothing - {highlight file='orders.pad'}, a {slot 'title'} with no
  // default. Counted as openers in front of the marker, as the balance of the pairs counts
  // them, they took the template's own @content@ for one inside a sample or a slot, and the
  // tag failed "an @content@ stands where nothing merges content into it".

  function padContentPairs ( $text ) {

    $pairs = [];

    preg_match_all ( '/\{(\/?)(content|slot|define|macro|ignore|highlight)(?=[\s}|])/', $text, $tags,
                     PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

    $open = [];

    foreach ( $tags as $tag ) {

      $name = $tag [2] [0];

      if ( $tag [1] [0] === '' ) {
        if ( ! padOpenSelfClosing ( $text, $tag [2] [1] + strlen ( $name ) ) )
          $open [$name] [] = $tag [0] [1];
      } elseif ( ! empty ( $open [$name] ) )
        $pairs [] = [ array_pop ( $open [$name] ), $tag [0] [1] ];

    }

    return $pairs;

  }

?>
