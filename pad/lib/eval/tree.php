<?php

  // The evaluator's parser: the tokens of an expression (lib/eval/scan.php) become a tree,
  // which lib/eval/run.php evaluates. The grammar, weakest first:
  //
  //   expression  segment ( | segment )*        a | outside brackets - each segment's answer
  //                                             is the input of the next
  //   segment     a printf format, or a range
  //   range       values ? range? : range       the inline ternary, whose condition is one
  //               values                        value; values side by side are joined as text
  //   value       operators by binding power, the ones with a value on both sides in
  //               padEval_bp, the prefix ! - + and not, the tests in, like ... (75) and a
  //               call after a value (130), which takes that value as its input
  //   primary     a literal, $field, @, ( arguments ), [ list ], a call, a lookup
  //
  // An argument of ( ) or an item of [ ] is a range of its own - a comma ends it, a => ends
  // the key of a list item - and with a | of its own it is a pipeline whose head takes the
  // value of the segment: ($n | + 1) * 2. Values left side by side in ( ) stand side by side
  // where the brackets stood; after a function they are its arguments.
  //
  // What an operator lacks the piped value stands in for: {echo 10 | - 3} is 7, {echo 5 |
  // *} is 25. One operator closing a range takes what is before it as its left side and the
  // piped value as its right one, and of two operators side by side the first takes the
  // piped value on both sides: {echo 50 | '"' . . '"'} is "5050".
  //
  // A bare word is what it names where the expression stands - a function, a lookup such as
  // a field or a data store, or else a constant - so a tree depends on that too, and is kept
  // for the request per text, per pipe body, per fallback after it and per what its words
  // name (padEvalTreeGet).
  //
  // The nodes are lists, their kind first:
  //
  //   lit  value, literal     a value written in the template; literal for a quoted string
  //   leaf token              a field, an option, a property, a constant ... read where the
  //                           walk reaches it (padEvalLeaf)
  //   me                      the value piped in, @
  //   fmt  format             a segment that is a printf format
  //   un   op, operand, as written
  //   bin  op, left, right, as written
  //   tern cond, then, else   then NULL for cond ?: else, else NULL when it is empty
  //   join nodes, text        values side by side
  //   list entries            [ 'v', node ] and [ '=>' ] as written
  //   call kind, name, input, arguments, brackets, first  - input NULL for none, first when
  //                           a built-in called with brackets reads its first argument as
  //                           its value (the head of an expression, or inside a call)
  //   look kind, name, missable                a reference that takes no arguments
  //   pipe parts              a pipeline inside brackets, NULL for an empty part
  //   seq  nodes, node        the nodes read and left out, then the node
  //   err  messages, node     reported where it is evaluated, then the node (or empty)
  //   none text               a range with no value in it
  //   more text               what is left beside a value no operator can take

  // The bare words of the tokens: per token the kind and name of a reference - a function,
  // a tag, a field ... as padTypeFunction finds it, or the type: prefix written - and FALSE
  // for an operator word or a constant. The signature says the same as text, for the cache.

  function padEvalWords ( $tokens ) {

    $words = [];
    $sig   = '';

    foreach ( $tokens as $i => $one ) {

      if ( $one [1] != 'other' )
        continue;

      $words [$i] = FALSE;

      if ( in_array ( strtoupper ( $one [0] ), padEval_precedence ) )
        continue;

      $exp = padExplode ( $one [0], ':' );

      if ( count ( $exp ) == 2 ) {
        $type = $exp [0];
        $name = $exp [1];
      } else {
        $type = padTypeFunction ( $one [0] );
        $name = $one [0];
      }

      if ( padValid ( $type ) and padValid ( $name ) ) {
        $words [$i] = [ $type, $name ];
        $sig       .= "$i:$type,";
      }

    }

    return [ $words, $sig ];

  }

  // The tree of an expression and the tokens read before it, kept for the request - not for a
  // text of more than 1 KB, a value spliced in that never comes back (padEvalTokens).

  function padEvalTreeGet ( $tokens, $eval, $pipe, $fallback = FALSE ) {

    static $trees = [];

    [ $words, $sig ] = padEvalWords ( $tokens );

    $key = ( $pipe ? 'p' : 'e' ) . ( $fallback ? 'f' : '' ) . $sig . "\x1f" . $eval;

    if ( isset ( $trees [$key] ) )
      return $trees [$key];

    if ( strlen ( $eval ) > 1024 )
      return padEvalTree ( $tokens, $words, $eval, $pipe, $fallback );

    if ( count ( $trees ) >= 4000 )
      $trees = [];

    return $trees [$key] = padEvalTree ( $tokens, $words, $eval, $pipe, $fallback );

  }

  // The segments of the expression, and its leaves: every field, option, property, hex value
  // and constant, by the position of its token, each read where the walk reaches it
  // (padEvalLeaf). With $fallback the answer goes into a fallback the caller applies
  // (padEvalTreeMissing).

  function padEvalTree ( $tokens, $words, $eval, $pipe, $fallback = FALSE ) {

    $p = [ 'tok' => $tokens, 'word' => $words, 'match' => [] ];

    // Each opening bracket and its closing one. The validation saw them balanced, but the
    // scanner drops a backslash outside a string with the character after it - (\) holds no
    // ) - and what cannot be reduced answers empty, as an unclosed bracket does; the
    // scanner reported the backslash. A bracket with no end walked the expression again
    // from its start, without end.

    $open   = [];
    $broken = FALSE;

    foreach ( $tokens as $i => $one )
      if ( $one [1] == 'open' or $one [1] == 'a-open' )
        $open [] = $i;
      elseif ( ( $one [1] == 'close' or $one [1] == 'a-close' ) and $open )
        $p ['match'] [ array_pop ( $open ) ] = $i;
      elseif ( $one [1] == 'close' or $one [1] == 'a-close' )
        $broken = TRUE;

    if ( $open or $broken )
      return [ 'segments' => [ [ 'err', [], NULL ] ], 'leaves' => [] ];

    $leaves = [];

    foreach ( $tokens as $i => $one )
      if ( in_array ( $one [1], [ '$', '$$n', 'prop', '&', '#', 'hex' ], TRUE ) )
        $leaves [$i] = [ [ $one [0], $one [1] ], FALSE ];
      elseif ( $one [1] == 'other' and ! $words [$i] and ! in_array ( strtoupper ( $one [0] ), padEval_txt ) )
        $leaves [$i] = [ [ $one [0], 'other' ], FALSE ];

    // An expression that is no pipe body may open with a | - its value goes straight in.

    $segments = [];
    $from     = 0;
    $count    = count ( $tokens );

    foreach ( padEvalTreeCuts ( $p, 0, $count, 'pipe' ) as $n => $cut ) {

      if ( $cut == $from + 1 and $tokens [$from] [1] == '%' )
        $segments [] = [ 'fmt', $tokens [$from] [0] ];
      elseif ( $cut > $from or $n or $pipe )
        $segments [] = padEvalTreeRange ( $p, $from, $cut, [ 'head' => ( ! $n and ! $pipe ), 'call' => FALSE, 'text' => $eval ] );

      $from = $cut + 1;

    }

    $miss     = [];
    $segments = padEvalTreeMissing ( $segments, $fallback, $miss );

    foreach ( $miss as $i => $true )
      if ( isset ( $leaves [$i] ) )
        $leaves [$i] [1] = TRUE;

    return [ 'segments' => $segments, 'leaves' => $leaves ];

  }

  // The lookups whose value goes straight into a fallback may be missing - that is what the
  // fallback is for: the left side of ??, the value piped into default or optional, with a
  // prefix or without - and with $fallback the answer of the whole expression, which {echo}
  // hands to the fallback of its tag's pipe. Brackets around one do not matter: ($x) ??
  // 'none' as $x ?? 'none', where the ) after the field hid the ?? from the token after it. A
  // lookup that is part of more - $x + 1 ?? 0, 'a' . $x ?? 'b' - is no fallback's, and is
  // named when it is not there. The fields of $miss are marked so for padEvalLeaf, a
  // reference without arguments on its own node.

  function padEvalTreeMissing ( $segments, $fallback, &$miss ) {

    $segments = padEvalTreeMissingParts ( $segments, $miss );

    $last = array_key_last ( $segments );

    if ( $fallback and $last !== NULL )
      $segments [$last] = padEvalTreeMay ( $segments [$last], $miss );

    return $segments;

  }

  // The parts of a pipeline: one with a fallback after it may be missing.

  function padEvalTreeMissingParts ( $parts, &$miss ) {

    foreach ( $parts as $n => $part )
      if ( $n and $part and $parts [$n-1] and padEvalTreeFallback ( $part ) )
        $parts [$n-1] = padEvalTreeMay ( $parts [$n-1], $miss );

    foreach ( $parts as $n => $part )
      if ( $part )
        $parts [$n] = padEvalTreeMissingNode ( $part, $miss );

    return $parts;

  }

  // The nodes below $node, the left side of each ?? and the parts of each pipeline in them.

  function padEvalTreeMissingNode ( $node, &$miss ) {

    switch ( $node [0] ) {

      case 'bin':

        if ( $node [1] == '??' )
          $node [2] = padEvalTreeMay ( $node [2], $miss );

        $node [2] = padEvalTreeMissingNode ( $node [2], $miss );
        $node [3] = padEvalTreeMissingNode ( $node [3], $miss );

        break;

      case 'un':

        $node [2] = padEvalTreeMissingNode ( $node [2], $miss );

        break;

      case 'call':

        if ( $node [3] )
          $node [3] = padEvalTreeMissingNode ( $node [3], $miss );

        foreach ( $node [4] as $n => $arg )
          $node [4] [$n] = padEvalTreeMissingNode ( $arg, $miss );

        break;

      case 'tern':

        foreach ( [ 1, 2, 3 ] as $n )
          if ( $node [$n] )
            $node [$n] = padEvalTreeMissingNode ( $node [$n], $miss );

        break;

      case 'join':
      case 'seq':

        foreach ( $node [1] as $n => $one )
          $node [1] [$n] = padEvalTreeMissingNode ( $one, $miss );

        if ( $node [0] == 'seq' )
          $node [2] = padEvalTreeMissingNode ( $node [2], $miss );

        break;

      case 'list':

        foreach ( $node [1] as $n => $entry )
          if ( $entry [0] == 'v' )
            $node [1] [$n] [1] = padEvalTreeMissingNode ( $entry [1], $miss );

        break;

      case 'pipe':

        $node [1] = padEvalTreeMissingParts ( $node [1], $miss );

        break;

      case 'err':

        if ( $node [2] )
          $node [2] = padEvalTreeMissingNode ( $node [2], $miss );

        break;

    }

    return $node;

  }

  // A lookup that may be missing: a field, or a reference without arguments - field:x.

  function padEvalTreeMay ( $node, &$miss ) {

    if ( $node [0] == 'leaf' )
      $miss [ $node [1] ] = TRUE;
    elseif ( $node [0] == 'look' )
      $node [3] = TRUE;

    return $node;

  }

  // Whether a part of a pipeline is a fallback for the value piped into it: default or
  // optional - the built-in, the application's or by function: - or a ?? with nothing on its
  // left.

  function padEvalTreeFallback ( $node ) {

    if ( $node [0] == 'bin' )
      return $node [1] == '??' and $node [2] === [ 'me' ];

    return $node [0] == 'call' and $node [3] === NULL
       and in_array ( $node [2], [ 'default', 'optional' ], TRUE )
       and in_array ( $node [1], [ 'pad', 'app', 'function' ], TRUE );

  }

  // Whether the text of a pipe begins with a fallback, by the same rule - what {echo} and
  // {$x} read of the pipe a tag carries, before any of it is evaluated: | default('-'),
  // | pad:default('-'), | ?? '-', | optional.

  function padEvalFallbackPipe ( $text ) {

    return (bool) preg_match ( '/^\s*(\?\?|((pad|app|function):)?(default|optional)\b)/', (string) $text );

  }

  // The positions in $a..$b where a range of the level is cut - at a | ('pipe') or where an
  // argument starts ('arg': the token after a comma, or after a => when $arrow) - with $b
  // last. The brackets inside are stepped over.

  function padEvalTreeCuts ( $p, $a, $b, $at, $arrow = TRUE ) {

    $cuts = [];

    for ( $i = $a; $i < $b; $i++ ) {

      $one = $p ['tok'] [$i];

      if ( $at == 'pipe' and $one [1] == 'pipe' )
        $cuts [] = $i;
      elseif ( $at == 'arg' and $i > $a and ! empty ( $one ['arg'] ) and ( $arrow or $p ['tok'] [$i-1] [1] != 'arrow' ) )
        $cuts [] = $i;

      if ( isset ( $p ['match'] [$i] ) )
        $i = $p ['match'] [$i];

    }

    $cuts [] = $b;

    return $cuts;

  }

  // The first token of the level in $a..$b of kind $kind (and text $text), or FALSE.

  function padEvalTreeFind ( $p, $a, $b, $kind, $text = NULL ) {

    for ( $i = $a; $i < $b; $i++ ) {

      $one = $p ['tok'] [$i];

      if ( $one [1] == $kind and ( $text === NULL or $one [0] === $text ) )
        return $i;

      if ( isset ( $p ['match'] [$i] ) )
        $i = $p ['match'] [$i];

    }

    return FALSE;

  }

  // A range as one node: its values joined when more than one is left side by side.

  function padEvalTreeRange ( $p, $a, $b, $ctx ) {

    $nodes = padEvalTreeNodes ( $p, $a, $b, $ctx );

    if ( count ( $nodes ) == 1 )
      return $nodes [0];

    if ( ! $nodes )
      return [ 'none', $ctx ['text'] ];

    return [ 'join', $nodes, $ctx ['text'] ];

  }

  // The values of a range, the inline ternary first: the first ? splits it at its own : - a
  // ? in between opens a nested one, which takes the next : - so a ? b : c ? d : e is
  // a ? b : (c ? d : e). With nothing before the ? the condition is the piped value.

  function padEvalTreeNodes ( $p, $a, $b, $ctx ) {

    $q = padEvalTreeFind ( $p, $a, $b, 'OPR', '?' );

    if ( $q === FALSE )
      return padEvalTreeValues ( padEvalTreeItems ( $p, $a, $b, $ctx ), $ctx );

    $c     = FALSE;
    $depth = 0;

    for ( $i = $q + 1; $i < $b; $i++ ) {

      $one = $p ['tok'] [$i];

      if ( $one [1] == 'OPR' and $one [0] === '?' )
        $depth++;
      elseif ( $one [1] == 'OPR' and $one [0] === ':' and $depth )
        $depth--;
      elseif ( $one [1] == 'OPR' and $one [0] === ':' ) {
        $c = $i;
        break;
      }

      if ( isset ( $p ['match'] [$i] ) )
        $i = $p ['match'] [$i];

    }

    // Without its : the ? is reported and left out; the lenient walk keeps the rest:
    // 1 ? 'a' is 1a.

    if ( $c === FALSE ) {

      $nodes = padEvalTreeValues ( padEvalTreeItems ( $p, $a, $b, $ctx, $q ), $ctx );

      $nodes [0] = [ 'err', [ "the ? of an inline ternary has no : - write cond ? then : else" ], $nodes [0] ?? [ 'lit', '', FALSE ] ];

      return $nodes;

    }

    if ( $q == $a )

      $cond = [ 'me' ];

    else {

      $cond = padEvalTreeValues ( padEvalTreeItems ( $p, $a, $q, $ctx ), $ctx );
      $cond = ( count ( $cond ) == 1 ) ? $cond [0] : [ 'err', [ "the condition of an inline ternary is not one value" ], [ 'lit', '', FALSE ] ];

    }

    $then = ( $c == $q + 1 ) ? NULL : padEvalTreeRange ( $p, $q + 1, $c, $ctx );
    $else = ( $c + 1 >= $b ) ? NULL : padEvalTreeRange ( $p, $c + 1, $b, $ctx );

    return [ [ 'tern', $cond, $then, $else ] ];

  }

  // The items of a range for padEvalTreeValues: a value, a bracketed group or list already a
  // node ('a'), an operator ('o': the operator and how it was written), a word that calls
  // ('c': kind, name, its arguments when brackets follow it - NULL when none do - and
  // whether it is a test), or what stands where no value can ('x'). The token $skip is left
  // out.

  function padEvalTreeItems ( $p, $a, $b, $ctx, $skip = -1 ) {

    static $single = [];

    $items = [];

    for ( $i = $a; $i < $b; $i++ ) {

      if ( $i == $skip )
        continue;

      $one  = $p ['tok'] [$i];
      $kind = $one [1];

      if ( $kind == 'open' ) {

        $j    = $p ['match'] [$i];
        $last = count ( $items ) - 1;

        if ( $last >= 0 and $items [$last] [0] == 'c' and $items [$last] [3] === NULL )
          $items [$last] [3] = padEvalTreeArgs ( $p, $i + 1, $j, [ 'call' => TRUE ] + $ctx );
        else
          foreach ( padEvalTreeArgs ( $p, $i + 1, $j, $ctx ) as $node )
            $items [] = [ 'a', $node ];

        $i = $j;

      } elseif ( $kind == 'a-open' ) {

        $j = $p ['match'] [$i];

        $items [] = [ 'a', padEvalTreeList ( $p, $i + 1, $j, $ctx ) ];

        $i = $j;

      } elseif ( $kind == 'VAL' )

        $items [] = [ 'a', [ 'lit', $one [0], ! empty ( $one ['lit'] ) ] ];

      elseif ( $kind == '$$' )

        $items [] = [ 'a', [ 'me' ] ];

      elseif ( in_array ( $kind, [ '$', '$$n', 'prop', '&', '#', 'hex' ], TRUE ) )

        $items [] = [ 'a', [ 'leaf', $i ] ];

      elseif ( $kind == 'OPR' and in_array ( $one [0], [ '?', ':' ], TRUE ) )

        $items [] = [ 'x' ];

      elseif ( $kind == 'OPR' )

        $items [] = [ 'o', $one [0], $one [2] ?? $one [0] ];

      elseif ( $kind == 'other' and ! $p ['word'] [$i] and in_array ( strtoupper ( $one [0] ), padEval_txt ) )

        $items [] = [ 'o', strtoupper ( $one [0] ), $one [0] ];

      elseif ( $kind == 'other' and ! $p ['word'] [$i] )

        $items [] = [ 'a', [ 'leaf', $i ] ];

      elseif ( $kind == 'other' ) {

        [ $type, $name ] = $p ['word'] [$i];

        $single [$type] ??= file_exists ( PAD . "eval/single/$type.php" );

        if ( $single [$type] )
          $items [] = [ 'a', [ 'look', $type, $name, FALSE ] ];
        else
          $items [] = [ 'c', $type, $name, NULL, $type == 'pad' and in_array ( $name, padEval_tests ) ];

      } else

        $items [] = [ 'x' ];

    }

    return $items;

  }

  // The arguments inside ( ): each a range, which may hold pipes of its own.

  function padEvalTreeArgs ( $p, $a, $b, $ctx ) {

    $nodes = [];
    $from  = $a;

    foreach ( padEvalTreeCuts ( $p, $a, $b, 'arg' ) as $cut ) {

      if ( $cut > $from )
        foreach ( padEvalTreePiped ( $p, $from, $cut, $ctx ) as $node )
          $nodes [] = $node;

      $from = $cut;

    }

    return $nodes;

  }

  // One argument or item: a pipeline when a | of its own stands in it - the part before the
  // first | takes the segment's value, as the head of an expression does, each part after it
  // the answer of the one before - and else its values.

  function padEvalTreePiped ( $p, $a, $b, $ctx ) {

    $cuts = padEvalTreeCuts ( $p, $a, $b, 'pipe' );

    if ( count ( $cuts ) == 1 )
      return padEvalTreeNodes ( $p, $a, $b, $ctx );

    $parts = [];
    $from  = $a;

    // A part after a | has its value piped in, as a segment of the expression has: a
    // built-in called there takes it - ucfirst(($s | left(2))). A message names the whole
    // expression, where it quoted nothing: "there is no value in ''".

    // A part that is a printf format (padEvalFormatLength) formats the value piped into it.

    foreach ( $cuts as $n => $cut ) {
      if ( $n and $cut == $from + 1 and $p ['tok'] [$from] [1] == '%' )
        $parts [] = [ 'fmt', $p ['tok'] [$from] [0] ];
      else
        $parts [] = ( $cut > $from ) ? padEvalTreeRange ( $p, $from, $cut, ( $n ? [ 'head' => FALSE, 'call' => FALSE ] : [] ) + $ctx ) : NULL;
      $from = $cut + 1;
    }

    return [ [ 'pipe', $parts ] ];

  }

  // The items of a [ ] list: a comma ends one, a => pairs a key with its value. The pairs are
  // made from the values when the list is built (padEvalArrayItems), as written.

  function padEvalTreeList ( $p, $a, $b, $ctx ) {

    $entries = [];
    $from    = $a;

    foreach ( padEvalTreeCuts ( $p, $a, $b, 'arg', FALSE ) as $cut ) {

      $part = $from;

      for ( $i = $from; $i <= $cut; $i++ ) {

        if ( $i < $cut and $p ['tok'] [$i] [1] != 'arrow' ) {
          if ( isset ( $p ['match'] [$i] ) )
            $i = $p ['match'] [$i];
          continue;
        }

        if ( $i > $part )
          foreach ( padEvalTreePiped ( $p, $part, $i, $ctx ) as $node )
            $entries [] = [ 'v', $node ];

        if ( $i < $cut )
          $entries [] = [ '=>' ];

        $part = $i + 1;

      }

      $from = $cut;

    }

    return [ 'list', $entries ];

  }

  // The values of a range of items. Of two operators side by side - the second one with a
  // value on both sides - the first takes the piped value on both sides and stands as a
  // value: {echo 50 | '"' . . '"'} is "5050". A binary operator closing the range takes
  // everything before it as its left side and the piped value as its right one - {echo 5 |
  // 2 + 3 *} is 25 - and a prefix one closing it works on the piped value alone, what
  // stands before it read and left out; after a lone binary operator, + !, it is named.

  function padEvalTreeValues ( $items, $ctx ) {

    $count = count ( $items );

    for ( $n = 0; $n < $count - 1; $n++ )
      if ( $items [$n] [0] == 'o' and padEvalTreeBinary ( $items, $n + 1 ) )
        $items [$n] = [ 'a', padEvalTreeAlone ( $items [$n] ) ];

    $last = $items [ $count - 1 ] ?? [ '' ];
    $end  = ( $count > 1 and $last [0] == 'o' ) ? $count - 1 : $count;

    $nodes = [];
    $pos   = 0;

    while ( $pos < $end )
      $nodes [] = padEvalTreeExpr ( $items, $pos, 0, $end, $ctx );

    if ( $end == $count )
      return $nodes;

    if ( count ( $nodes ) != 1 or ( $end == 1 and padEvalTreeBinary ( $items, 0 ) ) )
      return [ [ 'more', $ctx ['text'] ] ];

    if ( isset ( padEval_bp [ $last [1] ] ) )
      return [ [ 'bin', $last [1], $nodes [0], [ 'me' ], $last [2] ] ];

    return [ [ 'seq', $nodes, [ 'un', $last [1], [ 'me' ], $last [2] ] ] ];

  }

  // An operator with nothing to work on but the piped value.

  function padEvalTreeAlone ( $item ) {

    return isset ( padEval_bp [ $item [1] ] )
      ? [ 'bin', $item [1], [ 'me' ], [ 'me' ], $item [2] ]
      : [ 'un',  $item [1], [ 'me' ], $item [2] ];

  }

  // Whether an item is an operator with a value on both sides.

  function padEvalTreeBinary ( $items, $pos ) {

    return isset ( $items [$pos] ) and $items [$pos] [0] == 'o' and isset ( padEval_bp [ $items [$pos] [1] ] );

  }

  // One value from the items at $pos, operators binding stronger than $min.

  function padEvalTreeExpr ( $items, &$pos, $min, $end, $ctx ) {

    $item = $items [$pos++];

    if ( $item [0] == 'a' )

      $left = $item [1];

    elseif ( $item [0] == 'c' )

      $left = padEvalTreeCall ( $item, NULL, $items, $pos, $end, $ctx );

    elseif ( $item [0] == 'o' and $pos >= $end )

      $left = padEvalTreeAlone ( $item );

    elseif ( $item [0] == 'o' and isset ( padEval_bp [ $item [1] ] ) )

      // A binary operator where a value should be: its left side is the piped value -
      // {echo 10 | - 3} is 7.

      $left = [ 'bin', $item [1], [ 'me' ], padEvalTreeExpr ( $items, $pos, padEval_bp [ $item [1] ] - ( $item [1] == '**' ), $end, $ctx ), $item [2] ];

    elseif ( $item [0] == 'o' )

      $left = [ 'un', $item [1], padEvalTreeExpr ( $items, $pos, $item [1] == 'NOT' ? 50 : 110, $end, $ctx ), $item [2] ];

    else

      $left = [ 'more', $ctx ['text'] ];

    while ( $pos < $end ) {

      $item = $items [$pos];

      if ( $item [0] == 'o' and isset ( padEval_bp [ $item [1] ] ) ) {

        $bp = padEval_bp [ $item [1] ];

        if ( $bp <= $min )
          break;

        $pos++;

        // ** binds to the right: 2 ** 3 ** 2 is 2 ** 9.

        $right = ( $pos < $end ) ? padEvalTreeExpr ( $items, $pos, ( $item [1] == '**' ) ? $bp - 1 : $bp, $end, $ctx ) : [ 'me' ];
        $left  = [ 'bin', $item [1], $left, $right, $item [2] ];

      } elseif ( $item [0] == 'o' and $item [1] == 'NOT' and ( $items [$pos+1] [0] ?? '' ) == 'c' and $items [$pos+1] [4] and $pos + 1 < $end ) {

        // A not between a value and a test denies the test: $s not in ('a', 'b').

        if ( 75 <= $min )
          break;

        $pos += 2;

        $left = [ 'un', 'NOT', padEvalTreeCall ( $items [$pos-1], $left, $items, $pos, $end, $ctx ), $item [2] ];

      } elseif ( $item [0] == 'c' ) {

        // A test takes the arithmetic and the concatenation on its left as its input -
        // $n + 1 range (9, 9) - any other call the value right before it.

        if ( ( $item [4] ? 75 : 130 ) <= $min )
          break;

        $pos++;

        $left = padEvalTreeCall ( $item, $left, $items, $pos, $end, $ctx );

      } else

        break;

    }

    return $left;

  }

  // A call: its arguments in brackets, or else the one value right after it, when one does.
  // A test after it tests its answer - {$s | lower in ('a', 'b')} - where it was the
  // argument the function left unread, tested on the piped value.

  function padEvalTreeCall ( $item, $input, $items, &$pos, $end, $ctx ) {

    $args = $item [3];

    if ( $args === NULL ) {
      $args = [];
      if ( $pos < $end and ( $items [$pos] [0] == 'a' or ( $items [$pos] [0] == 'c' and ! $items [$pos] [4] ) ) )
        $args [] = padEvalTreeExpr ( $items, $pos, 129, $end, $ctx );
    }

    return [ 'call', $item [1], $item [2], $input, $args, $item [3] !== NULL, $ctx ['head'] or $ctx ['call'] ];

  }

?>
