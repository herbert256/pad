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
  // is not inside quotes or brackets - a || is the or operator, never a pipe, and a | in
  // ( ) or [ ] is a pipe of that group's own (lib/eval/tree.php) - and
  // padSplitOnUnquotedColon does the same for the type prefix.
  //
  // padFindContinueBreak resolves the target of {continue}, {cease} and {break}: a level
  // name, a negative offset, an absolute level number, or by default the nearest enclosing
  // loop - past every level that is a part of the row (padLevelInRow), the conditions among
  // them.

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

  // Whether a level is part of the row it stands in rather than a loop of its own: it renders
  // its content once - its data is the single empty occurrence a tag answering TRUE gets
  // (padIsDefaultData) - and it is no {while} or {until}, whose every round is such an
  // occurrence too. An {if}, a {case}, an {ifchanged}, a {guest}, {auth}, {can} or {feature},
  // a property pair such as {notFirst@items}, a {spaceless}, a {fragment}, a snippet, a
  // custom tag: each is a new level on every row of the loop around it, and what asks for
  // "the loop" - {ifchanged}'s memory, a property without a target - looks past it.

  function padLevelInRow ( $lvl ) {

    global $padData, $padTag;

    return padIsDefaultData ( $padData [$lvl] ?? [] )
       and ! in_array ( $padTag [$lvl] ?? '', [ 'while', 'until' ] );

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

    // The nearest loop is past every level that renders its content once for the row it
    // stands in (padLevelInRow) - only an {if} and a {case} were, and a nameless control
    // inside an {ifchanged}, a property pair, a {guest} or a {spaceless} acted on that
    // level: its one occurrence ended and the loop ran on, in silence - {rows}{notFirst@rows}
    // {break}{/notFirst@rows}{$a}{/rows} printed every row. The root of a pass - the page,
    // or a nested pass such as a {code} - ends the search, as it did.

    for ( $key = $pad-1; $key >=0 ; $key-- )
      if ( $padTag [$key] == 'internal' or ! padLevelInRow ( $key ) ) {

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
    $depth    = 0;

    // A run of characters that are none of \ ' " ( ) [ ] | changes nothing - inside quotes
    // or out - and is stepped over at once, as padParseOptions does: the loop looked at
    // every character of every tag's text in PHP.

    for ($i = 0; $i < $length; $i++) {

        $i += strcspn($input, "\\'\"()[]|", $i);

        if ($i >= $length)
            break;

        $ch = $input[$i];

        // An escaped backslash is skipped whole as well, so the one in 'a\\' cannot escape
        // the quote behind it and hide the pipe that follows.

        if ($ch === '\\' && $i + 1 < $length && ($input[$i + 1] === "'" || $input[$i + 1] === '"' || $input[$i + 1] === '\\')) {
            $i++;
      } elseif ($ch === "'" && !$inDouble) {
            $inSingle = !$inSingle;
        } elseif ($ch === '"' && !$inSingle) {
            $inDouble = !$inDouble;
        } elseif ($inSingle || $inDouble) {
            continue;
        } elseif ($ch === '(' || $ch === '[') {
            $depth++;
        } elseif ($ch === ')' || $ch === ']') {
            $depth--;
        } elseif ($ch === '|' && ($input[$i + 1] ?? '') === '|') {
            $i++;     // || is the or operator of the expression, no pipe
        } elseif ($ch === '|' && $depth <= 0) {
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
  // the quote. A value inside quotes or in text no tag encloses is left as it is, and so is
  // a value glued to a sigil that builds an application field name - {${$hi}}. A value in
  // the tag's own place - bare, {{$x}}, or completing the tag's word, {php:{$x}} - is never
  // a tag. A tag's rendered answer comes through here only for that place ($field FALSE,
  // level/end.php): written in another tag's parameters it is template text by design, the
  // way the manual builds an option list from {notFirst},skipOpen{/notFirst}.
  //
  // The enclosing tag is the last { before the splice: the scanner resolves the innermost
  // tag first and left to right, so every complete tag before this point is gone already.

  // Whether a field name taken from a value - {$$x}, {!$x}, {?$x}, {^$x} - may be read: an
  // application field or a path into one, and nothing the @ resolver would carry further.
  // The head is letters, digits, _ and - (first-name, _id, café) and starts with no digit,
  // which is an ordinal into the globals; the parts after it, joined by . (a key) or @ (a tag
  // or level), take what a key of a dotted path takes (at/_lib/check.php) - x#y, p%q, a+b -
  // and no part is an engine name - pad*, pq*, a superglobal. Refused everywhere is what the
  // resolver reads as more than a name and so picks a global the head does not name: * and ?
  // (any key, a random one when last), : (the prefix form searches the globals for what
  // follows it), a condition operator, [ ] and / \ - and a quote, angle bracket, space,
  // control character, brace or pipe, which would close the attribute the value is written
  // into or open a tag.

  function padValueName ( $name ) {

    if ( ! preg_match ( '/^[\p{L}_][\p{L}\p{M}\p{N}_-]*([.@][^.@*?:\[\]=<>\/\\\\\'"`\s\x00-\x1F\x7F{}|]+)*$/u', $name ) )
      return FALSE;

    foreach ( preg_split ( '/[.@]/', $name ) as $part )
      if ( padEngineName ( $part ) )
        return FALSE;

    return TRUE;

  }

  // The } that closes the { at $from, by brace depth - so an inner tag's braces are stepped
  // over. Used to turn a whole {...} into literal text when a value would otherwise re-enter
  // the scan as a tag or a glued reference. FALSE when the brace never closes.

  function padSpliceMatch ( $text, $from ) {

    $depth = 0;
    $len   = strlen ( $text );

    for ( $i = $from; $i < $len; $i++ )
      if     ( $text [$i] == '{' ) $depth++;
      elseif ( $text [$i] == '}' and ! --$depth ) return $i;

    return FALSE;

  }

  function padSpliceQuote ( $value, $field = TRUE ) {

    global $pad, $padOut, $padStart, $padEnd;

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

    // A { that opens no tag - followed by whitespace or a double quote, as padWhiteCheck
    // reads it - is passed over to the one before it, as the scanner passes it over: inside
    // a quoted parameter, {echo '{"q": ' . {$q} . '}'}, it was taken for the tag's own brace,
    // the value judged to stand in text and left bare, and a request value's php:abs(-3) ran
    // as part of the {echo}.

    while ( $open !== FALSE and $open < $window - 1 and ( ctype_space ( $before [$open + 1] ) or $before [$open + 1] == '"' ) )
      $open = $open ? strrpos ( $before, '{', $open - $window - 1 ) : FALSE;

    // No tag opens within the window: text, or the far part of a tag's parameters longer than
    // the window - where='...' of 4 KB - which nothing here can tell apart. Kept, so the
    // evaluators still refuse it as part of their code (padSpliceInCode): it went in bare and
    // unrecorded, and a request value's php:abs(-3) ran past both guards.

    if ( $open === FALSE ) {
      padSpliceRecord ( $value );
      return $value;
    }

    $inside = ( $open >= $window - 1 ) ? '' : substr ( $before, $open + 1 );

    // A value spliced in the tag's own place re-enters the scan as a tag - {{$x}} ran the
    // value as one, PHP calls and all, {php:{$x}} let it pick the PHP function and
    // {{echo $x}} did the same with a tag's answer - and a value glued to a sigil builds a
    // field reference from it, {${$k}}. "Values are text": the value never becomes a tag
    // or part of a tag's word, and a field name built with it passes the check a name {$$k}
    // takes from a value passes (padValueName), so the two spellings read the same names -
    // first-name and _id included, x:padPage, an engine name or a wildcard never. Refused,
    // the enclosing { is turned into its &open; stand-in, so the whole {...} prints as the
    // literal text it wraps, and the inner tag's own span, which the escape lengthens the
    // buffer before, moves along by that growth. Glued means nothing between the { and the
    // value that ends a tag's word - no space, no quote, no } - so literal JSON stays text.

    if ( $inside === '' )
      $padSpliceTag = TRUE;
    elseif ( preg_match ( '/[\s"\'}]/', $inside ) )
      $padSpliceTag = FALSE;
    elseif ( $sigils = strspn ( $inside, '$!?^#&' ) )
      $padSpliceTag = ! padValueName ( substr ( $inside, $sigils ) . $value );
    else
      $padSpliceTag = TRUE;

    if ( $padSpliceTag ) {

      $bracePos = $start - $window + $open;
      $closePos = padSpliceMatch ( $padOut [$pad], $bracePos );

      if ( $closePos !== FALSE ) {

        $grow = strlen ( '&open;' ) - 1;

        // The close stands after the inner tag's span, so its escape leaves the span where
        // it is; done after it, since replacing the earlier open would shift the close.

        $padOut   [$pad]  = substr_replace ( $padOut [$pad], '&close;', $closePos, 1 );
        $padOut   [$pad]  = substr_replace ( $padOut [$pad], '&open;',  $bracePos, 1 );
        $padStart [$pad] += $grow;
        $padEnd   [$pad] += $grow;

      }

      return $value;

    }

    padSpliceRecord ( $value );

    // A { followed by whitespace or a double quote opens no tag (padWhiteCheck), so the
    // value stands in text: literal JSON, {"id": {$id}}, had quotes put round it; a value
    // glued as above stands as it was too. A tag's answer is quoted nowhere.

    if ( ! $field or ctype_space ( $inside [0] ) or $inside [0] == '"' or str_contains ( $inside, '}' ) or ! preg_match ( '/\s/', $inside ) )
      return $value;

    $quote   = '';
    $quoteAt = 0;

    for ( $i = 0, $len = strlen ( $inside ); $i < $len; $i++ )
      if ( $quote ) {
        if     ( $inside [$i] == '\\'   ) $i++;
        elseif ( $inside [$i] == $quote ) $quote = '';
      } elseif ( $inside [$i] == "'" or $inside [$i] == '"' ) {
        $quote   = $inside [$i];
        $quoteAt = $i;
      }

    // A value standing bare as the whole of where= - where={$cond} - would be the per-row
    // expression itself once quoted: a request value with php:abs(-3) in it ran PHP. That is
    // a whole condition from a value, which where.php refuses for where=$cond as well - and
    // the same for the expression of the eval sequence (sequence/types/eval/loop.php), in each
    // of its spellings: eval=, {eval {$q}}, sequence:eval(2, {$q}).

    if ( $quote === '' and $padSpliceWhat = padSpliceExpression ( $inside ) ) {
      padError ( "$padSpliceWhat is an expression the template writes: give it as a quoted string, and put a value in it as \$name" );
      return "''";
    }

    if ( $quote === '' )
      return "'$value'";

    // Inside the quoted expression of where= or of the eval sequence - where='$n eq {$f}',
    // {eval '@ * {$q}'}, make='eval|@ * {$q}' - the text is evaluated again for every row, and
    // a value standing in it bare became part of that expression: a request value 2 or $n eq
    // php:abs(-3) called PHP. It goes in as a string of the expression, in the other quote,
    // unless it stands in one already; its own quotes are protected, so it closes neither.

    if ( padSpliceExpression ( substr ( $inside, 0, $quoteAt ), substr ( $inside, $quoteAt + 1 ) ) ) {
      $other = ( $quote == "'" ) ? '"' : "'";
      if ( ! padSpliceInString ( substr ( $inside, $quoteAt + 1 ), $quote ) )
        return $other . $value . $other;
    }

    return $value;

  }

  // Whether a splice stands in an expression the engine evaluates - $head the tag's text up to
  // the splice or up to the quote it stands in, $text what that quote holds before it - and
  // which one, for the refusal: the where= and eval= options, the item of {eval} (written
  // sequence:eval, make:eval ... too) and of {sequence eval, ...}, the parameter of
  // sequence:eval() - or eval() - in an expression, and the text after eval| in make=, keep=,
  // remove= and flag=. The eval sequence itself refuses what no spelling here catches. In any case, as a file system that
  // ignores case finds the option or tag for WHERE= and Eval= as well: those passed the guard
  // and their value ran.

  // What goes into a tag's text is kept for the request when it could call code or read a
  // variable - a name of two letters or more and anything beside it, a space included - so an
  // evaluator of a tag's text can refuse an expression that holds it outside its own strings
  // (padSpliceInCode), whatever spelling it stood in: a guard that recognised each spelling let
  // eval(2, '@ * {$q}') and {sequence eval, '@ * {$q}'} run the value's php:abs(-3). A bare
  // name calls a function as well - strlen 5 - so a ( or a : is not needed for that. A single
  // word, a number or operators alone are not kept: a - printed in a tag's parameters made
  // eval='@ - 1' a refusal.

  function padSpliceRecord ( $value ) {

    if ( is_string ( $value ) and preg_match ( '/[a-z_]{2}/i', $value ) and preg_match ( '/[^\w]/u', $value ) )
      $GLOBALS ['padSpliceValues'] [$value] = TRUE;

  }

  // Whether an expression about to be evaluated - the eval sequence's, a where=, a format= of
  // {datatable} - holds a value kept by padSpliceRecord outside the expression's own strings,
  // where it is part of the code.

  function padSpliceInCode ( $expression ) {

    static $memo = [];

    $key = count ( $GLOBALS ['padSpliceValues'] ?? [] ) . ':' . $expression;

    if ( isset ( $memo [$key] ) )
      return $memo [$key];

    $code = preg_replace ( '/\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"/s', '', (string) $expression );

    foreach ( array_keys ( $GLOBALS ['padSpliceValues'] ?? [] ) as $one )
      if ( str_contains ( $code, (string) $one ) )
        return $memo [$key] = TRUE;

    return $memo [$key] = FALSE;

  }

  // Whether a splice stands inside a string of the expression - $text the quoted option up to
  // it, read as the expression will read it once the option's own quote is unescaped, each
  // quote closing only a string it opened. The other quote was counted, and a string of it
  // written before the splice - where='$s ne \'"\' and $n eq {$q}' - made the value seem to
  // stand in one: it went in bare and ran.

  function padSpliceInString ( $text, $quote ) {

    $text = str_replace ( '\\' . $quote, $quote, $text );
    $in   = '';

    for ( $i = 0, $len = strlen ( $text ); $i < $len; $i++ )
      if ( $in ) {
        if     ( $text [$i] == '\\' ) $i++;
        elseif ( $text [$i] == $in  ) $in = '';
      } elseif ( $text [$i] == "'" or $text [$i] == '"' )
        $in = $text [$i];

    return $in !== '';

  }

  function padSpliceExpression ( $head, $text = '' ) {

    if ( preg_match ( '/(^|[\s,])(where|eval)\s*=\s*$/i', $head, $match ) )
      return strtolower ( $match [2] ) . '=';

    if ( preg_match ( '/^((pad|sequence|make|keep|remove|flag):)?eval\s(.*,)?\s*$/is', $head ) )
      return '{eval}';

    if ( preg_match ( '/^\s*eval\s*\|/i', $text ) )
      return 'eval|';

    if ( preg_match ( '/^((pad|sequence):)?sequence\s(.*[\s,])?eval\s*,(.*,)?\s*$/is', $head ) )
      return '{sequence eval}';

    if ( preg_match_all ( '/(?<![\w$:])(?:sequence:)?eval\s*\(/i', $head, $match, PREG_OFFSET_CAPTURE ) ) {
      $rest = substr ( $head, end ( $match [0] ) [1] + strlen ( end ( $match [0] ) [0] ) );
      $rest = preg_replace ( '/\'[^\'\\\\]*(\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(\\\\.[^"\\\\]*)*"/s', '', $rest );
      while ( preg_match ( '/\([^()]*\)/', $rest ) )
        $rest = preg_replace ( '/\([^()]*\)/', '', $rest );
      if ( ! str_contains ( $rest, ')' ) and str_contains ( $rest, ',' ) )
        return 'sequence:eval()';
    }

    return '';

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

    $kept = [];
    $text = padTildeKeep ( $text, $kept );

    $text = preg_replace ( '/\s*\{~(?=[\/A-Za-z_$!#&?^@])/', '{', $text );
    $text = preg_replace ( '/(\{[\/A-Za-z_$!#&?^@][^{}]*?)~\}\s*/', '$1}', $text );

    return $kept ? strtr ( $text, $kept ) : $text;

  }

  // The content of a {highlight} pair is code shown as it stands - a sample of the ~ syntax
  // lost every ~ and the line breaks beside them. It is set aside while the rest is
  // stripped; the pair's own tags keep their ~.
  //
  // A closing tag pairs with the nearest opening tag before it that has none yet, as the
  // engine pairs them; an opening tag left over is a single {highlight file=...}. Counted
  // as an opener that never closed, a single tag in front of a pair kept every pair after
  // it from being set aside.

  function padTildeKeep ( $text, &$kept ) {

    if ( ! str_contains ( $text, 'highlight' ) )
      return $text;

    preg_match_all ( '/\{~?(\/?)highlight(?=[\s}~|])[^{}]*\}/', $text, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

    $open  = [];
    $pairs = [];

    foreach ( $tags as $tag )
      if ( $tag [1] [0] === '' )
        $open [] = $tag [0] [1] + strlen ( $tag [0] [0] );
      elseif ( $open )
        $pairs [array_pop ( $open )] = $tag [0] [1];

    ksort ( $pairs );

    $spans = [];
    $last  = -1;

    foreach ( $pairs as $from => $to )
      if ( $from > $last ) {
        $spans [] = [ $from, $to ];
        $last     = $to;
      }

    foreach ( array_reverse ( $spans ) as list ( $from, $to ) ) {
      $mark          = "\u{E0F8}" . count ( $kept ) . "\u{E0F9}";
      $kept [$mark]  = substr ( $text, $from, $to - $from );
      $text          = substr ( $text, 0, $from ) . $mark . substr ( $text, $to );
    }

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
