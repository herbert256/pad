<?php

  // The text surgery behind the tag loop. A level works on $padOut[$pad] and locates the
  // current tag with $padStart[$pad] and $padEnd[$pad], the braces either side of it.
  //
  // padLevelEnd / padLevelStart  find the next } and then the { that opens it, which is
  //                    how the parser always takes the innermost tag first
  // padLevelBetween    lifts the text between those braces into $padBetween (and keeps
  //                    the untouched original in $padOrgSet)
  // padLevel           replaces the tag, braces included, with its result
  // padLevelNo         puts the tag back escaped as &open;...&close;, so it is left in the
  //                    output as literal text rather than parsed again; padLevelNoSingle,
  //                    padLevelNoPair and padLevelNoOpen are the variants for a single
  //                    tag, a whole pair, and just neutralising the opening brace
  // padCommentCheck / padCommentGo  a {#...#} comment, dropped from the output
  //
  // Quote-aware splitters used while parsing a tag: padPipeSplit cuts at the first | that
  // is not inside quotes, padSplitOnUnquotedColon does the same for the type prefix.
  //
  // padFindContinueBreak resolves the target of {continue}, {cease} and {break}: a level
  // name, a negative offset, an absolute level number, or by default the nearest enclosing
  // level that is not an if or case - so loop control skips over conditionals.

function padSplitOnUnquotedColon ( $str ) {

    $len = strlen($str);

    $inSingleQuote = false;
    $inDoubleQuote = false;

    for ($i = 0; $i < $len; $i++) {

        $char = $str[$i];

        if ($char === '\\' && $i + 1 < $len) {
            $i++;
            continue;
        }

        if     ( $char === "'" && !$inDoubleQuote ) $inSingleQuote = !$inSingleQuote;
        elseif ( $char === '"' && !$inSingleQuote ) $inDoubleQuote = !$inDoubleQuote;

        if ($char === ':' && !$inSingleQuote && !$inDoubleQuote)
            return [
                substr($str, 0, $i),
                substr($str, $i + 1)
            ];

    }

    return [$str, ''];

}

  function padFindContinueBreak ( $parm ) {

    global $pad, $padName, $padTag, $padCheckSyntax;

    // A negative offset counts levels up from the control tag. One that reaches past the
    // page root indexed a level that is not there - {cease -9} ended in a raw 'Undefined
    // array key -7'. Strict mode names it; the lenient walk falls to the nearest loop, as a
    // name that matches nothing does.

    if ( $parm and is_numeric ($parm) and $parm < 0 ) {

      if ( $pad + (int) $parm >= 0 )
        return $pad + (int) $parm;

      if ( $padCheckSyntax )
        padError ( "the offset $parm of {" . $padTag [$pad] . "} reaches past the page" );

    }

    if ( $parm )
      for ( $key = $pad-1; $key >=0 ; $key-- )
        if ( $padName [$key] == $parm )
          return $key;

    if ( $parm and is_numeric ($parm) )
      for ( $key = $pad-1; $key >=0 ; $key-- )
        if ( $key == $parm )
          return $key;

    // A name that matches nothing used to fall through to the nearest loop, and the
    // control tag silently worked on the wrong level. Strict mode says so instead.

    if ( $parm and ! is_numeric ( $parm ) and $padCheckSyntax )
      padError ( "there is no enclosing level named '$parm' for {" . $padTag [$pad] . "}" );

    for ( $key = $pad-1; $key >=0 ; $key-- )
      if ( $padTag [$key] != 'if' and $padTag [$key] != 'case' ) {

        // The nearest thing left may be the page root - loop control aimed at nothing.
        // Strict mode says so; the lenient walk keeps the old answer.

        if ( $padCheckSyntax and $padTag [$key] == 'internal' )
          padError ( "there is no enclosing loop for {" . $padTag [$pad] . "}" );

        return $key;

      }

    return $pad - 1;

  }

  function padPipeSplit ($input) {

    $inSingle = false;
    $inDouble = false;
    $length   = strlen($input);
    $splitPos = null;

    for ($i = 0; $i < $length; $i++) {

        $ch = $input[$i];

        if ($ch === '\\' && $i + 1 < $length && ($input[$i + 1] === "'" || $input[$i + 1] === '"')) {
            $i++;
      } elseif ($ch === "'" && !$inDouble) {
            $inSingle = !$inSingle;
        } elseif ($ch === '"' && !$inSingle) {
            $inDouble = !$inDouble;
        } elseif ($ch === '|' && !$inSingle && !$inDouble) {
            $splitPos = $i;
            break;
        }

    }

    if ( $splitPos === null)
        return [ $input, '' ];

    $left  = substr($input, 0, $splitPos);
    $right = substr($input, $splitPos + 1);

    return [$left, $right];

  }

  // A field spliced into the parameters of the tag around it, outside any quotes - the
  // {$v} of {echo {$v}} - becomes part of that tag's expression, and a value of php:getcwd
  // there was a call. Under $padProtectValues level/var.php splices it as a quoted string
  // instead, unless it is a plain number; its own quotes are protected, so it cannot close
  // the quote. A value inside quotes, in text no tag encloses, or glued to the tag's own
  // word - the name built by {${$hi}} - is left as it is. A tag's rendered answer is not
  // passed through here: written inside another tag it is template text by design, the
  // way the manual builds an option list from {notFirst},skipOpen{/notFirst}.
  //
  // The enclosing tag is the last { before the splice: the scanner resolves the innermost
  // tag first and left to right, so every complete tag before this point is gone already.

  function padSpliceQuote ( $value ) {

    global $pad, $padOut, $padStart;

    if ( ! is_scalar ( $value ) or is_bool ( $value ) )
      return $value;

    $value = (string) $value;
    $start = $padStart [$pad];

    if ( preg_match ( '/^-?[0-9]+(\.[0-9]+)?$/', $value ) or $start < 1 )
      return $value;

    // Looked for within the 4 KB before the splice: no tag writes that much before a field
    // in its parameters, and searching the whole resolved text before it made a page of
    // many fields quadratic.

    $window = min ( $start, 4096 );
    $before = substr ( $padOut [$pad], $start - $window, $window );
    $open   = strrpos ( $before, '{' );

    if ( $open === FALSE or $open >= $window - 1 )
      return $value;

    $inside = substr ( $before, $open + 1 );

    // A { followed by whitespace or a double quote opens no tag (padWhiteCheck), so the
    // value stands in text: literal JSON, {"id": {$id}}, had quotes put round it.

    if ( ctype_space ( $inside [0] ) or $inside [0] == '"' or str_contains ( $inside, '}' ) or ! preg_match ( '/\s/', $inside ) )
      return $value;

    $quote = '';

    for ( $i = 0, $len = strlen ( $inside ); $i < $len; $i++ )
      if ( $quote ) {
        if     ( $inside [$i] == '\\'   ) $i++;
        elseif ( $inside [$i] == $quote ) $quote = '';
      } elseif ( $inside [$i] == "'" or $inside [$i] == '"' )
        $quote = $inside [$i];

    return $quote ? $value : "'$value'";

  }

  function padLevel ( $value ) {

    global $padOut, $padStart, $padEnd, $pad, $padScan;

    $padOut [$pad] = substr ( $padOut [$pad], 0, $padStart [$pad] )
                   . $value
                   . substr ( $padOut [$pad], $padEnd [$pad]+1 );

    $padScan [$pad] = $padStart [$pad];

  }


  function padCloseCheck () {

    global $padBetween;

    return ( $padBetween != '' and $padBetween [0] == '/' ) ;

  }


  function padWhiteCheck () {

    global $padBetween, $padCheckSyntax;

    // An empty {} is nothing at all: strict mode reports it, the lenient walk treats it
    // like a whitespace brace and keeps it as literal text. Reading [0] of the empty
    // string was a PHP error before either could speak.

    if ( $padBetween == '' ) {

      if ( $padCheckSyntax )
        padError ( 'an empty tag: {}' );

      return TRUE;

    }

    // A { followed by a double quote cannot open a tag either - no tag, field or sigil
    // starts with one - and it is how JSON opens an object: compact JSON in a _data file,
    // [{"a":"1"}], read as a tag named "a":"1" and failed under the strict check.

    return ( ctype_space ( $padBetween [0] ) or $padBetween [0] == '"' );

  }


  // Takes the {# ... #} comments out of a template before it is scanned. The scanner
  // handles the first } and the { before it, so a tag inside a comment ran before the
  // comment was ever seen: {# {set $x = 99} #}{$x} printed 99. A comment opens with {#
  // and closes at the first #} after it. Not a comment: the option sigil {#name} or
  // {#name | pipe}, and a {# whose span reaches another {# before any #} - the strict
  // check names an unclosed one, and the lenient walk reads {# x } as it always did.

  function padCommentStrip ( $text ) {

    if ( ! is_string ( $text ) )
      return $text;

    if ( str_contains ( $text, '{--' ) )
      $text = padCommentStripDash ( $text );

    if ( ! str_contains ( $text, '{#' ) )
      return $text;

    $pos = 0;

    while ( ( $pos = strpos ( $text, '{#', $pos ) ) !== FALSE ) {

      if ( preg_match ( '/\G\{#[A-Za-z_][A-Za-z0-9_]*\s*[}|]/', $text, $match, 0, $pos ) ) {
        $pos += 2;
        continue;
      }

      $close = strpos ( $text, '#}', $pos + 2 );
      $next  = strpos ( $text, '{#', $pos + 2 );

      if ( $close === FALSE or ( $next !== FALSE and $next < $close ) ) {
        $pos += 2;
        continue;
      }

      $text = substr ( $text, 0, $pos ) . substr ( $text, $close + 2 );

    }

    return $text;

  }


  // Whitespace control: a ~ just inside a brace takes the whitespace on that side of it,
  // newlines included - {~items} what stands before the tag, {/items~} what follows it,
  // {items~} the start of the content. Only a brace that opens a tag counts, so a ~ in
  // text, in CSS or in a quoted parameter is left alone.

  function padTildeStrip ( $text ) {

    if ( ! is_string ( $text ) or ! str_contains ( $text, '~' ) )
      return $text;

    $text = preg_replace ( '/\s*\{~(?=[\/A-Za-z_$!#&?^@])/', '{', $text );
    $text = preg_replace ( '/(\{[\/A-Za-z_$!#&?^@][^{}]*?)~\}\s*/', '$1}', $text );

    return $text;

  }


  // The second comment form, {-- ... --}, the one the reference and every editor kit
  // write. The {-- must be followed by whitespace, so a CSS custom property inside an
  // {ignore} block - :root{--gap:4px} - is never read as a comment; the comment closes at
  // the first --} after it, and one that never closes stays as it is.

  function padCommentStripDash ( $text ) {

    $pos = 0;

    while ( ( $pos = strpos ( $text, '{--', $pos ) ) !== FALSE ) {

      $after = $text [$pos + 3] ?? '';
      $close = strpos ( $text, '--}', $pos + 3 );

      if ( ( $after !== '' and ! ctype_space ( $after ) ) or $close === FALSE ) {
        $pos += 3;
        continue;
      }

      $text = substr ( $text, 0, $pos ) . substr ( $text, $close + 3 );

    }

    return $text;

  }

  function padCommentCheck () {

    global $padBetween;

    return ( str_starts_with( $padBetween, '#' ) and str_ends_with($padBetween, '#') );

  }


  function padCloseHit () {

    global $padBetween, $padCheckSyntax;

    if ( $padCheckSyntax )
      padError ( "Closing tag found without an open tag: {" . $padBetween . "}" );

    return padLevelNo ( $padBetween );

  }


  function padWhiteHit () {

    global $padBetween;

    padLevelNo ( $padBetween );

  }


  function padCommentHit () {

    return padLevel ( '' );

  }


  function padLevelNo ( $no ) {

    // The kept span goes back as literal text in one piece, inner braces included. It
    // used to keep them raw, leaving the walk an orphan brace to meet later - shipped
    // silently for years, and an error the moment the strict syntax check watched it.

    $no = str_replace ( [ '{', '}' ], [ '&open;', '&close;' ], $no );

    padLevel ( "&open;$no&close;" );

  }

  function padLevelNoSingle () {

    global $padBetweenOrg;

    padLevelNo ( $padBetweenOrg );

  }

  function padLevelNoPair () {

    global $padOut, $padStart, $padEnd, $pad;

    padLevelNo ( substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 ) );

  }

  function padLevelBetween () {

    global $padOut, $padStart, $padEnd, $pad, $padBetween, $padOrgSet;

    $padBetween = substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );

    $padOrgSet = $padBetween;

  }

  function padLevelNoStart () {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );

  }

  function padLevelStart () {

    global $padOut, $padStart, $padEnd, $pad;

    $padStart [$pad] = strrpos ( $padOut [$pad], '{', $padEnd [$pad] - strlen ( $padOut [$pad] ) );

    return $padStart [$pad];

  }

  // The search for the next } starts where the last splice began, not at 0: everything
  // before that point is resolved and holds no }, since the tag that was spliced there had
  // the first one. Searching from 0 on every tag made one large occurrence quadratic -
  // 160,000 fields took six seconds. $padScan is reset when an occurrence gets its copy.

  function padLevelEnd () {

    global $padOut, $padStart, $padEnd, $pad, $padScan;

    $from = min ( $padScan [$pad] ?? 0, strlen ( $padOut [$pad] ) );

    $padEnd [$pad] = strpos ( $padOut [$pad], '}', $from );

    return $padEnd [$pad];

  }

?>