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

    global $pad, $padName, $padTag;

    if ( $parm and is_numeric ($parm) and $parm < 0 )
      return $pad + $parm;

    if ( $parm )
      for ( $key = $pad-1; $key >=0 ; $key-- )
        if ( $padName [$key] == $parm )
          return $key;

    if ( $parm and is_numeric ($parm) )
      for ( $key = $pad-1; $key >=0 ; $key-- )
        if ( $key == $parm )
          return $key;

    for ( $key = $pad-1; $key >=0 ; $key-- )
      if ( $padTag [$key] != 'if' and $padTag [$key] != 'case' )
        return $key;

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

  function padLevel ( $value ) {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr ( $padOut [$pad], 0, $padStart [$pad] )
                   . $value
                   . substr ( $padOut [$pad], $padEnd [$pad]+1 );

  }

  function padCommentCheck () {

    global $padBetween;

    return ( str_starts_with( $padBetween, '#' ) and str_ends_with($padBetween, '#') );

  }

  function padCommentGo () {

    return padLevelNo ( '' );

  }

  function padLevelNo ( $no ) {

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

  function padLevelNoOpen () {

    global $padOut, $padStart, $padEnd, $pad;

    $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );

  }

  function padLevelStart () {

    global $padOut, $padStart, $padEnd, $pad;

    $padStart [$pad] = strrpos ( $padOut [$pad], '{', $padEnd [$pad] - strlen ( $padOut [$pad] ) );

  }

  function padLevelEnd () {

    global $padOut, $padStart, $padEnd, $pad;

    $padEnd [$pad] = strpos ( $padOut [$pad], '}' );

  }

?>