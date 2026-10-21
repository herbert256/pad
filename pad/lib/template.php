<?php

  // String handling for template text: telling whether a stretch of source is balanced,
  // and the general splitting helpers the whole engine uses.
  //
  // The balance group answers "may I cut here without breaking a tag pair". padOpenCloseList
  // collects every tag that has a closing {/tag} in the string, padOpenCloseCountOne checks
  // that one tag opens as often as it closes (padOpenCount counts the openers, leaving out a
  // self-closing {tag .../}), padOpenCloseCount does that for a whole list,
  // padOpenCloseOk combines them for the text following a marker (padOpenClosePos says where
  // the level's own marker stands), and padCheckTag is a
  // single-tag shorthand. lib/content.php uses these to place @content@ and @else@ at the
  // right nesting depth.
  //
  // padSplit    splits once on a needle, trimming both halves
  // padBetween  extracts before/between/after around an open and close delimiter
  // padExplode  the engine's standard explode: trims, drops empty parts, reindexes, and
  //             restores the &pipe; &eq; &comma; escapes when splitting on | = ,
  // padMakeSafe flattens any value to a single-line, control-character-free, length-capped
  //             string, for log lines and error messages
  // padGetRange turns "1..10" (or "10", or nothing) into a PHP range
  // padGetList  splits a semicolon list, numeric entries becoming the numbers they spell

  function padOpenCloseOk ( $string, $check) {

    return padOpenClosePos ( $string, $check ) !== FALSE;

  }

  // Where the marker of the level the string is the content of stands: the first one with
  // every tag pair after it balanced - a marker of a nested pair has that pair's closing
  // tag after it. Only the first marker was looked at, so a nested level's @end@ in front
  // of the level's own hid it - {items}@start@{subs}(@start@{$s}@end@){/subs}@end@{/items}
  // failed the strict check as an @start@ without its @end@ - and the split took place at
  // the nested marker.
  //
  // The text is read as padMarkerText (lib/content.php) gives it: a custom tag's level holds
  // its template as the file has it, whose {~rows~} is a pair of rows too and whose comments
  // hold no marker.

  function padOpenClosePos ( $string, $check ) {

    $pos = strpos ( $string, $check );

    if ( $pos !== FALSE ) {
      $string = padMarkerText ( $string );
      $pos    = strpos ( $string, $check );
    }

    while ( $pos !== FALSE ) {

      $after = substr ( $string, $pos + strlen ( $check ) );

      if ( padOpenCloseCount ( $after, padOpenCloseList ( $after ) ) )
        return $pos;

      $pos = strpos ( $string, $check, $pos + 1 );

    }

    return FALSE;

  }

  // The @start@ and @end@ of a text that renders exactly once - a level no tag opened: the
  // page itself when its PHP answered no list, or a padCode pass. level/start.php cuts a
  // tag's level at its own markers into a prelude, a body walked per row and a coda; here
  // all three render once and in order, which is the text with its own markers taken out.
  // Only the markers that belong to this text are taken (padOpenClosePos), never a nested
  // tag's, and strict mode holds them to the order level/start.php does.

  function padSectionsOnce ( $text ) {

    $start = padOpenClosePos ( $text, '@start@' );
    $end   = padOpenClosePos ( $text, '@end@'   );

    if ( $start === FALSE and $end === FALSE )
      return $text;

    if ( $GLOBALS ['padCheckSyntax'] ) {

      if ( $start !== FALSE and $end === FALSE )
        padError ( "an @start@ needs its @end@ behind it" );

      if ( $end !== FALSE and $start === FALSE )
        padError ( "an @end@ needs its @start@ before it" );

      if ( $start !== FALSE and $end !== FALSE and $end < $start )
        padError ( "the @start@ stands before the @end@, not behind it" );

    }

    $cuts = [];

    if ( $start !== FALSE ) $cuts [$start] = 7;
    if ( $end   !== FALSE ) $cuts [$end]   = 5;

    krsort ( $cuts );

    foreach ( $cuts as $pos => $len )
      $text = substr_replace ( $text, '', $pos, $len );

    return $text;

  }

  function padOpenCloseList ( $string ) {

    $tags = [];

    $p1 = strpos($string, '{/', 0);

    while ($p1 !== FALSE) {

      // The name ends at white space, a pipe or the }, as level/tag.php reads a closer and
      // padOpenCloseCountOne counts one: ended at a space only, {/rows and a line break gave
      // the name "rows" with the break, which is no tag, and that pair went uncounted - the
      // @else@ inside it was taken for one of the level around it.

      if ( strpos ( $string, '}', $p1 ) !== FALSE ) {

        $tag = substr ( $string, $p1 + 2, strcspn ( $string, " \t\r\n|}", $p1 + 2 ) );
        if ( padValidTag ($tag) )
          $tags [$tag] = TRUE;

      }

      $p1 = strpos($string, '{/', $p1+1);

    }

    return $tags;

  }

  function padOpenCloseCount ( $string, $tags ) {

   foreach ( $tags as $tag => $dummy )
      if ( ! padOpenCloseCountOne ( $string, $tag ) )
        return FALSE;

    return TRUE;

  }

  function padOpenCloseCountOne ( $string, $tag ) {

    // A closer is its name followed by white space, the } or a pipe, as level/tag.php reads
    // one: {/rows|trim} and {/rows and a line break were not counted.

    if ( padOpenCount ( $string, $tag )
           !=
         preg_match_all ( '/\{\/' . preg_quote ( $tag, '/' ) . '[\s}|]/', $string ) )
      return FALSE;

    return TRUE;

  }

  // The openers of one tag in a string: {tag} and {tag ...}, but not a self-closing
  // {tag .../}, which opens nothing - counting it threw the balance of an enclosing pair of
  // the same name off by one, and that pair's {/tag} was left an orphan. The end of an
  // opener is found by brace depth, so a parameter holding a tag of its own, {tag x={$y}/},
  // is read to its real end; like level/tag.php, the slash must stand right before the }.
  // Any white space may follow the name, as the tag parser reads it: an {if written over
  // two lines was no opener here, and the pair around it closed at its {/if}. So may a pipe
  // without a space, {rows|trim}, which the balance of an enclosing {rows} did not count.

  function padOpenCount ( $string, $tag ) {

    $open   = '{' . $tag;
    $count  = 0;
    $offset = 0;

    while ( ( $pos = strpos ( $string, $open, $offset ) ) !== FALSE ) {

      $offset = $pos + strlen ( $open );
      $next   = $string [$offset] ?? '';

      if ( $next == '}' or ( $next !== '' and ( $next == '|' or ctype_space ( $next ) ) and ! padOpenSelfClosing ( $string, $offset ) ) )
        $count++;

    }

    return $count;

  }

  // The text between the braces is passed over by strcspn, where a loop in PHP looked at
  // every character of it - for every opener of every pair a page counts.

  function padOpenSelfClosing ( $string, $offset ) {

    $depth = 1;
    $end   = strlen ( $string );

    for ( $i = $offset + strcspn ( $string, '{}', $offset ); $i < $end; $i += 1 + strcspn ( $string, '{}', $i + 1 ) )
      if ( $string [$i] == '{' )
        $depth++;
      elseif ( ! --$depth )
        return ( $string [$i-1] == '/' );

    return FALSE;

  }

  // A closer may carry a pipe, {/if | upper}, so {/tag followed by a space closes too -
  // counting only {/tag} left such a nested pair looking open, and the enclosing tag's
  // {else} or {when} was skipped as if it belonged to the inner one.

  function padCheckTag ( $tag, $string, $full = NULL ) {

    return padPairBalanced ( $string, $tag ) and padTextFree ( $string, $full );

  }

  // The pairs whose content is text, not template: what {ignore} wraps, a {highlight}
  // sample, and the content of any pair whose opener carries the ignore option - {markdown
  // ignore}, {data 'd' ignore} - which level/split.php reads as text as it reads the other
  // two. A branch marker inside one - {ignore}{else}{/ignore}, a {when} in a sample - is
  // that text, and belongs to no tag: it was taken by the {if}, {case} or condition tag
  // around it, which cut its content there and left "the pair {ignore} never closes" - and
  // so it still was inside a {markdown ignore} sample. The @else@ split already looks past
  // them (padOpenCloseList balances every pair).

  const padTextPairs = [ 'ignore', 'highlight' ];

  function padTextFree ( $string, $full = NULL ) {

    return ! padTextScan ( $string, $full, is_string ( $string ) ? strlen ( $string ) : 0 ) ['open'];

  }

  // The content of every text pair of $text, outermost only, as [ from, to ] - from the end
  // of the opener to the start of its closer: what padTildeKeep sets aside.

  function padTextSpans ( $text ) {

    return padTextScan ( $text ) ['spans'];

  }

  // The text pairs of $string, read in order: a closer takes the nearest open opener of its
  // own name before it, as the engine pairs them, whether that opener holds text or not - a
  // {markdown} inside a {markdown ignore} sample closes before its outer one does. The single
  // form opens nothing - {highlight file='orders.php'}, which colours a file and has no
  // content, and a self-closing {tag .../} - nor does a closer with no opener before it.
  // Counted as the owners' pairs are counted (padPairBalanced), a single {highlight file=...}
  // read as a sample never closed, and every {else}, {elseif} and {when} behind it was taken
  // for its text: {if $a}{highlight file='x'}{else}none{/if} showed nothing for a=0.
  //
  // 'open' says whether a text pair is still open at the end of $string; 'spans' are the
  // contents of the text pairs that closed.

  function padTextScan ( $string, $full = NULL, $offset = 0 ) {

    if ( ! is_string ( $string ) or ( ! str_contains ( $string, 'ignore' ) and ! str_contains ( $string, 'highlight' ) ) )
      return [ 'open' => FALSE, 'spans' => [] ];

    preg_match_all ( '/\{~?(\/?)(?:pad:)?([A-Za-z_][\w:.-]*)(?=[\s}~|])/', $string, $all, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

    // While a text pair is open nothing else opens in it - an {ignore} shown in a
    // {highlight} sample is text - and only its own name counts, a nested opener of that
    // name closing first, as the engine pairs them. A tag that carries the ignore option is
    // a text pair only when a closer of its name follows - in $string, or in $full behind
    // $offset when $string is the part in front of a branch marker: the option is one of
    // any tag, and a single {echo 'ab' ignore} has no closer. Counted as open, it took the
    // {else} behind it for text and kept the {/if} around it from closing.

    $name  = NULL;
    $depth = 0;
    $from  = 0;
    $pairs = [];

    foreach ( $all as $one ) {

      $tag = $one [2] [0];
      $at  = $one [0] [1];

      if ( $name !== NULL ) {

        if ( $tag !== $name )
          continue;

        if ( $one [1] [0] === '/' ) {
          if ( ! --$depth ) {
            $pairs [$from] = $at;
            $name          = NULL;
          }
        } elseif ( ( $end = strpos ( $string, '}', $at ) ) !== FALSE and ! padOpenSelfClosing ( $string, $at + strlen ( $one [0] [0] ) ) )
          $depth++;

        continue;

      }

      if ( $one [1] [0] === '/' )
        continue;

      $after = $at + strlen ( $one [0] [0] );
      $end   = strpos ( $string, '}', $at );

      if ( $end === FALSE or padOpenSelfClosing ( $string, $after ) )
        continue;

      $parms = substr ( $string, $after, $end - $after );

      if ( in_array ( $tag, padTextPairs, TRUE ) )
        $text = ! ( $tag == 'highlight' and preg_match ( '/[\s,]file\s*=/', $parms ) );
      else
        $text = ( padTextOption ( $parms )
                  and ( strpos ( $string, '{/' . $tag, $end ) !== FALSE
                        or ( $full !== NULL and strpos ( $full, '{/' . $tag, $offset ) !== FALSE ) ) );

      if ( $text ) {
        $name  = $tag;
        $depth = 1;
        $from  = $end + 1;
      }

    }

    $spans = [];

    foreach ( $pairs as $one => $two )
      $spans [] = [ $one, $two ];

    return [ 'open' => ( $name !== NULL ), 'spans' => $spans ];

  }

  // The text between a pair's opener and a closer the closer search tries (level/tag.php),
  // its text pairs written over with spaces - the same length, as padMarkerText blanks a
  // comment - so an opener or closer of the pair's own name in a sample is not counted; FALSE
  // when the closer stands inside a text pair: {ignore}{/xs}{/ignore} is a sample, no closer
  // of the loop around it. NULL when there is no text pair in the stretch at all - looked for
  // with substr_count, which reads only that stretch, not the rest of the page.

  function padTextBlank ( $string, $from, $to ) {

    if ( $to <= $from or ( ! substr_count ( $string, 'ignore', $from, $to - $from ) and ! substr_count ( $string, 'highlight', $from, $to - $from ) ) )
      return NULL;

    $full   = $string;
    $string = substr ( $string, $from, $to - $from );
    $scan   = padTextScan ( $string, $full, $to );

    if ( $scan ['open'] )
      return FALSE;

    foreach ( $scan ['spans'] as list ( $one, $two ) )
      $string = substr_replace ( $string, str_repeat ( ' ', $two - $one ), $one, $two - $one );

    return $string;

  }

  // Whether the parameters of an opener - the text behind its name - hold the ignore option:
  // the word ignore as an option of its own, bare or with a value, in front of any pipe.

  function padTextOption ( $parms ) {

    if ( ! str_contains ( $parms, 'ignore' ) )
      return FALSE;

    $parms = explode ( '|', $parms, 2 ) [0];

    return (bool) preg_match ( '/(^|[\s,])ignore\s*(=|,|~?$)/', rtrim ( $parms ) );

  }

  // Whether the pairs of a tag in front of a place are all closed, the openings and the
  // closings counted as the tag parser reads them: with or without the pad: prefix that
  // names the built-in tag, any white space after the name - a line break too - and a pipe
  // on the closer. Counting only {tag and a space, or {tag}, left a nested {pad:if ...} or
  // an {if written over two lines unseen, and its {else}, {elseif or {when was taken for
  // the enclosing tag's: a valid template ended in an error, and with the strict check off
  // the wrong half showed - a guest saw what {auth} keeps for the logged in. A pipe may follow
  // the name of an opening as well, {guest|upper}, which the tag parser reads as the tag: only
  // the closer counted it, and the {else} of such a pair inside an {if} was the if's.

  function padPairBalanced ( $string, $tag ) {

    $tag = preg_quote ( $tag, '/' );

    return preg_match_all ( '/\{(?:pad:)?' . $tag . '[\s}|]/', $string )
        == preg_match_all ( '/\{\/(?:pad:)?' . $tag . '[\s}|]/', $string );

  }

  // The tags that own an {else}: {if} and {case}, and the condition tags of the login,
  // the gates and the feature flags (lib/auth.php, gate.php, feature.php). An {else}
  // belongs to the tag being run only when every one of them is balanced in the text in
  // front of it - one inside a nested pair is that pair's. The condition tags may stand
  // without a parameter, {auth}, so the bare form counts as an opening too.

  const padElseOwners = [ 'if', 'case', 'auth', 'guest', 'can', 'cannot', 'feature' ];

  function padElseFree ( $string, $full = NULL ) {

    foreach ( padElseOwners as $tag )
      if ( ! padPairBalanced ( $string, $tag ) )
        return FALSE;

    return padTextFree ( $string, $full );

  }

  // The text of a branch tag of an {if} or a {case} - the {elseif ...} or {when ...} whose
  // name, $length long with its brace, starts at $at in $content - and in $end the place of
  // the } that closes it. A bare {elseif} has the empty text: the condition was cut from one
  // past its }, a negative length, and substr took the rest of the content but its last
  // character - {if $a}A{elseif}B{else}C{/if} tested 'B{else}' and the strict "has no
  // condition" never came. The brace is found by depth, so a nested tag is read whole and
  // can be named (padBranchNested): the first } closed {elseif {$x} eq 'b'} at the field's
  // own, and the condition was '{$x'.

  function padBranchText ( $content, $at, $length, &$end ) {

    $depth = 1;
    $from  = $at + $length;
    $size  = strlen ( $content );

    for ( $i = $from + strcspn ( $content, '{}', $from ); $i < $size; $i += 1 + strcspn ( $content, '{}', $i + 1 ) )
      if ( $content [$i] == '{' )
        $depth++;
      elseif ( ! --$depth ) {
        $end = $i;
        return trim ( substr ( $content, $from, $i - $from ) );
      }

    $end = $size;

    return trim ( substr ( $content, $from ) );

  }

  // A branch tag's text is read raw, from the content of its {if} or {case} before any tag
  // in it ran: a tag nested in it was never resolved, and the condition evaluated its
  // braces. The opening tag can hold one - its parameters are spliced before it runs - a
  // branch tag cannot, and the strict check says so; the lenient walk evaluates the text as
  // it stands, which comes to nothing.

  function padBranchNested ( $text, $tag, $example ) {

    if ( $GLOBALS ['padCheckSyntax'] and str_contains ( $text, '{' ) )
      padError ( "$tag takes no nested tag - write $example" );

  }

  // An {else} written with white space before its brace - {else }, {else and a line break
  // - is the tag {else}, as {echo $x } is {echo $x} and {/if } is {/if}. The owners of an
  // {else} - {if}, {case} and the condition tags (padElseCut) - look for its exact text, so
  // the branch was lost in silence when the condition failed, and "there is no tag named
  // 'else'" ended the page when it held. Written as {else} where a level's text is set out
  // for an occurrence (occurrence/init.php), what a {highlight} shows kept as written, as
  // padTildeStrip keeps it.

  function padElseSpace ( $text ) {

    if ( ! is_string ( $text ) or ! str_contains ( $text, '{else' ) or ! preg_match ( '/\{else\s+\}/', $text ) )
      return $text;

    $kept = [];
    $text = padTildeKeep ( $text, $kept );
    $text = preg_replace ( '/\{else\s+\}/', '{else}', $text );

    return $kept ? strtr ( $text, $kept ) : $text;

  }

  // A condition tag's own {else}: the content is cut to the part in front of it when the
  // condition holds, to the part after it when not - TRUE then; FALSE when there is none,
  // and the tag answers the way any tag does, its @else@ half rendering on FALSE.

  function padElseCut ( &$content, $held ) {

    $at = strpos ( $content, '{else}' );

    while ( $at !== FALSE and ! padElseFree ( substr ( $content, 0, $at ), $content ) )
      $at = strpos ( $content, '{else}', $at + 6 );

    if ( $at === FALSE )
      return FALSE;

    $content = $held ? substr ( $content, 0, $at ) : substr ( $content, $at + 6 );

    return TRUE;

  }

  function padSplit ( $needle, $haystack, &$before, &$after ) {

    $array = explode ( $needle, $haystack, 2 );

    $before = trim ( $array [0] ?? '' );
    $after  = trim ( $array [1] ?? '' );

  }

  function padBetween ( $string, $open, $close, &$before, &$between, &$after ) {

    $before = $between = $after = '';

    $p1 = strpos ( $string, $open );
    if ( $p1 === FALSE ) return FALSE;

    $start = $p1 + strlen($open);
    $p2 = strpos ( $string, $close, $start );
    if ( $p2 === FALSE ) return FALSE;

    if ( $p1 > 0 )
      $before = substr ( $string, 0, $p1 );

    $between = substr ( $string, $start, $p2 - $start );

    $afterPos = $p2 + strlen($close);
    if ( $afterPos < strlen ( $string ) )
      $after = substr ( $string, $afterPos );

    return TRUE;

  }

  function padExplode ( $haystack, $limit, $number=0 ) {

    if ($number)
      $explode = explode ( $limit, $haystack, $number );
    else
      $explode = explode ( $limit, $haystack );

    foreach ($explode as $key => $value ) {

      $explode [$key] = trim($value);

      if ( $limit == '|' ) $explode [$key] = str_replace ( '&pipe;',  '|', $explode [$key] );
      if ( $limit == '=' ) $explode [$key] = str_replace ( '&eq;',    '=', $explode [$key] );
      if ( $limit == ',' ) $explode [$key] = str_replace ( '&comma;', ',', $explode [$key] );

      if ( $explode [$key] === '' )
        unset ( $explode [$key] );

    }

    return array_values ( $explode );

  }

  function padMakeSafe ( $input, $len=2048 ) {

    if ( is_array($input) or is_object($input) )
      $input = padJson ($input);

    // Text stays the UTF-8 it is: control characters - C0, DEL and the C1 range - become
    // spaces, and a byte that is no UTF-8 at all becomes a ?. Every byte above 0x7F was
    // blanked, so "Zoë Müller" came out "Zo M ller". The cut keeps whole characters.

    $input = mb_scrub ( (string) $input, 'UTF-8' );
    $input = preg_replace('/[\x{00}-\x{1F}\x{7F}-\x{9F}]/u', ' ', $input);
    $input = preg_replace('/\s+/u', ' ', $input);

    if ( strlen($input) > $len )
      $input = mb_strcut ( $input, 0, $len, 'UTF-8' );

    $input = trim($input);

    return $input;

  }

  function padGetRange ( $input, $increment=1 ) {

    global $padCheckSyntax;

    // '1..' quietly became the single row 1, and '..5' read as 5 - neither is what the
    // author meant by writing the dots. Strict mode asks for the missing end.

    if ( $padCheckSyntax and str_contains ( (string) $input, '..' ) ) {

      if ( str_ends_with ( trim ( $input ), '..' ) )
        padError ( "the range '" . trim ( $input ) . "' has no end" );

      if ( str_starts_with ( trim ( $input ), '..' ) )
        padError ( "the range '" . trim ( $input ) . "' has no start" );

    }

    $parts = padExplode ($input, '..');

    $p1 = $parts[0] ?? '';
    $p2 = $parts[1] ?? '';

    // An end of 0 is an end: tested for truth, '5..0' lost it and became 1..5, '0..0' the
    // default 1..10, and so did a bare '0', which is 1..0 as any bare 'b' is 1..b.

    if     ( $p2 !== '' ) { }
    elseif ( $p1 !== '' ) { $p2 = $p1; $p1 = 1;  }
    else                  { $p1 = 1;   $p2 = 10; }

    // range() counts in steps, so a step of zero or no step at all never arrives anywhere and
    // is an error rather than an empty answer; a step given the wrong way round is the same
    // walk in reverse, which range() works out from the two ends by itself.
    //
    // A step between 0 and 1 is the step it says - '0..3' with increment=0.5 is 0 0.5 1 1.5 2
    // 2.5 3 and '0.1..0.5' with increment=0.1 counts five tenths - where its whole part, 0,
    // made it a step of 1 in silence: 0 1 2 3, and 0.1 alone. Letters count in whole steps.

    if ( ! is_numeric ( $increment ) or ! ( $increment + 0 ) )
      $increment = 1;

    // '1..z' asks for a range between a number and a letter, which has no meaning; both ends
    // have to be of one kind, and if they are not there are no values to answer with. Strict
    // mode names it, as it names a missing end - '1..3;7' and '7;1..3', a list with a range in
    // it, split there too, and all three answered nothing in silence.

    if ( is_numeric ( $p1 ) != is_numeric ( $p2 ) ) {

      if ( $padCheckSyntax )
        padError ( "the range '" . trim ( $input ) . "' has ends of two kinds"
                 . ( str_contains ( (string) $input, ';' ) ? " - a list cannot hold a range" : '' ) );

      return [];

    }

    // A letter range runs from one letter to another, a single character at each end: 'x..ab',
    // 'aa..ad' or 'a..é' - an end of more than one byte - ended the request on PHP's "ord():
    // Providing a string that is not one byte long is deprecated". Strict mode names it; the
    // lenient walk has no values for it, as for '1..z'.

    if ( ! is_numeric ( $p1 ) and ( strlen ( $p1 ) != 1 or strlen ( $p2 ) != 1 ) ) {

      if ( $padCheckSyntax )
        padError ( "the range '" . trim ( $input ) . "' needs a single letter at each end" );

      return [];

    }

    if ( ! is_numeric ( $p1 ) and ! (int) $increment )
      $increment = 1;

    $increment = abs ( is_numeric ( $p1 ) ? $increment + 0 : (int) $increment );

    // A step wider than the two ends are apart cannot be taken even once, which range() calls
    // an error: the range is its start alone, as a range counts from its start and stops
    // before it passes the end - '1..10' with increment=4 is 1 5 9 - and as the loop form
    // from=1, to=3, increment=5 gives 1. The step was cut down to the distance, which landed
    // on the end: '1..3', increment=5 answered 1 3 and '0.1..0.5' 0.1 0.5. Letters are
    // measured the way range() walks them, by character code.

    $span = is_numeric ( $p1 ) ? abs ( $p2 - $p1 ) : abs ( ord ( $p2 ) - ord ( $p1 ) );

    if ( $span and $increment > $span )
      return [ is_numeric ( $p1 ) ? $p1 + 0 : $p1 ];

    // A range is built whole, so one of more values than $padSeqMaxTries is refused: '1..
    // 100000000' asked PHP for a hundred-million-element array. Strict mode says so; the
    // lenient walk keeps the first $padSeqMaxTries values.

    $max   = $GLOBALS ['padSeqMaxTries'] ?? 1000000;
    $count = $increment ? floor ( $span / $increment + 1e-9 ) + 1 : 1;

    if ( is_numeric ( $p1 ) and $count > $max ) {

      if ( $padCheckSyntax )
        padError ( "the range '" . trim ( $input ) . "' has $count values, more than the $max a range may have" );

      $p2    = $p1 + ( ( $p2 >= $p1 ) ? 1 : -1 ) * ( $max - 1 ) * $increment;
      $count = $max;

    }

    // A range with a fraction in it - in an end or in the step - is counted here, each value
    // the decimal it stands for, rounded to the decimals written: range() counts in binary,
    // where 0.3 / 0.1 is 2.9999999999999996, so '0..0.3' by 0.1 lost its 0.3, '0..0.7' had 7
    // values, and its fourth value was 0.30000000000000004, which {if $sequence eq 0.3} never
    // met.

    if ( is_numeric ( $p1 ) and ( fmod ( $p1 + 0, 1 ) or fmod ( $p2 + 0, 1 ) or fmod ( $increment, 1 ) ) ) {

      $decimals = max ( padRangeDecimals ( $p1 ), padRangeDecimals ( $p2 ), padRangeDecimals ( $increment ) );
      $sign     = ( $p2 >= $p1 ) ? 1 : -1;
      $values   = [];

      for ( $i = 0; $i < $count; $i++ )
        $values [] = round ( $p1 + $sign * $i * $increment, $decimals );

      return $values;

    }

    return range ( $p1, $p2, $increment );

  }

  // The decimals a number of a range is written with - 0.25 two, 3 none, 1e-3 the fourteen a
  // float holds surely.

  function padRangeDecimals ( $number ) {

    $text = (string) $number;

    if ( stripos ( $text, 'e' ) !== FALSE )
      return 14;

    return str_contains ( $text, '.' ) ? strlen ( rtrim ( substr ( $text, strpos ( $text, '.' ) + 1 ), '0' ) ) : 0;

  }

  // Each numeric entry becomes the number it spells: intval cut a decimal to its whole part
  // and clamped an integer too long for PHP's int, so {sequence '1;2.5;4'} gave 1 2 4 and
  // '9223372036854775808' became 9223372036854775807. An integer that fits is an int, an
  // integer that does not stays the digits it is, any other number - 2.5, 1e3 - is a float.
  //
  // It is decided by the number, not by the spelling: '07' is 7, where the integer filter
  // turned leading zeros down and {sequence '00;07'} printed 00 07. And a number past what a
  // float holds stays the text written, as an over-long integer does: 1e400 became INF, a
  // value no field could be resolved to, and {sequence '3;1e400'} ended the request on
  // "Field '$sequence' not found".
  //
  // Every entry is trimmed, as a number already was, and an empty one is no entry: a ';'
  // after the last item - what a list joined with one after every item gives - was a last
  // term of nothing, so {sequence '1;2;'} counted 3 and ended on an empty value, '1;;2' had
  // one between, and 'red; green' gave ' green'.

  function padGetList ( $list ) {

    $list = array_values ( array_filter ( array_map ( 'trim', explode ( ';', $list ) ),
                                          fn ( $value ) => $value !== '' ) );

    foreach ( $list as $key => $value)
      if ( is_numeric ($value) ) {

        $number = trim ( $value ) + 0;

        if     ( is_int ( $number )                             ) $list [$key] = $number;
        elseif ( preg_match ( '/^\s*[+-]?[0-9]+\s*$/', $value ) ) $list [$key] = trim ( $value );
        elseif ( is_finite ( $number )                          ) $list [$key] = $number;
        else                                                       $list [$key] = trim ( $value );

      }

    return $list;

  }

?>
