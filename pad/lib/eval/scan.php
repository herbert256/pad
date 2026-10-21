<?php

  // Stage one of the evaluator: the scanner. The text of an expression becomes a list of
  // tokens, which the parser (lib/eval/tree.php) makes a tree of. A token is [0] => its text
  // or value, [1] => its kind:
  //
  //   VAL       a quoted string - 'lit' => TRUE: a string the template wrote - or a number
  //   OPR       an operator, [2] => as written when it is a symbol: >= for GE, && for AND
  //   $  $$n    a field, the field another field names ($$x)
  //   prop      a property with its target, first@items, current@
  //   &  #      a property, an option of the tag
  //   $$        the @ placeholder - the value piped in - without a [0]
  //   hex       the digits of a 0x number
  //   other     a bare word: an operator word, a function, a reference, a constant - the
  //             parser asks what it names where the expression stands
  //   pipe  open  close  a-open  a-close  arrow    | ( ) [ ] =>
  //   %         a whole pipe segment that is a printf format (padEvalScan)
  //
  // Whitespace and commas end a token and are dropped; the token after a comma in brackets,
  // and after a =>, carries 'arg' => TRUE: it starts an argument, or the value of a list item.
  //
  // A field's name runs on as far as padEvalScanValid lets it: name@tag, dotted paths,
  // prefixes with : and the <, > and * of the at-syntax stay inside one token instead of being
  // torn apart as operators. A byte from 0x80 up is a letter of a name, as PHP has it.
  //
  // A - or + is the sign of a number only where no value stands before it: 5 -3 is a
  // subtraction, 5 * -3 and $x eq -3 keep their sign. Before anything that is no digit it is
  // the prefix NEG or POS - 5 * -$b, mid(2, -$n) - where an operator, an opening bracket or a
  // comma stands before it; at the head of an expression or of a pipe segment it stays the
  // operator whose missing left side is the piped value: {echo 10 | - 3} is 7.

  // The tokens of an expression, kept for the request: a loop evaluates the same text once
  // per row, and the text alone decides what the validation and the scanner make of it.
  // Only what came through clean is kept - an expression the validation refused answers
  // FALSE and is checked again next time, so it is reported as often as it is written; and
  // the scanner's own complaints are about a backslash, so an expression holding one is
  // never kept. Nor is a text of more than 1 KB, which no hand-written expression reaches: a
  // value spliced into a loop's expression - {echo {$body} | php:strlen} - makes a text of its
  // own per row, which never comes back, and the memos of the scanner, the tree and the pipe
  // check held 2000 rows of 20 KB five times over, 284 MB at the peak.

  function padEvalTokens ( $eval ) {

    static $memo = [];

    if ( isset ( $memo [$eval] ) )
      return $memo [$eval];

    if ( ! padEvalValidate ( $eval ) )
      return FALSE;

    $tokens = padEvalScan ( $eval );

    if ( ! str_contains ( $eval, '\\' ) and count ( $memo ) < 4000 and strlen ( $eval ) <= 1024 )
      $memo [$eval] = $tokens;

    return $tokens;

  }

  // A pipe segment that opens with a printf conversion is a format: one '%' token, which
  // the evaluator fills with the segment's input. The other segments are scanned as text:
  // {echo $p | * 1.21 | %.2f}, {echo $n | %05.2f | trim}.

  function padEvalScan ( $eval ) {

    $segments = padEvalFormatSegments ( padUnescape ( $eval ) );

    if ( $segments === FALSE )
      return padEvalScanText ( $eval );

    $tokens = [];

    foreach ( $segments as $n => [ $text, $format ] ) {

      if ( $n )
        $tokens [] = [ '|', 'pipe' ];

      if ( $format )
        $tokens [] = [ trim ( $text ), '%' ];
      else
        foreach ( padEvalScanText ( $text ) as $token )
          $tokens [] = $token;

    }

    return $tokens;

  }

  // Whether an expression is a printf format: a % that opens a conversion sprintf knows -
  // %d, %05.1f, %'*10s, %1$s, %% - whatever text follows it. Any other % is the modulo
  // operator, the pipe value on its left: {$x | % 3}, and {$x | % $n}, which went to sprintf
  // whole as the format '% $n' and ended the request on an unknown format specifier, where
  // {$x | * $n} multiplied. A % followed by a space and a word stays a format, as % s is.

  function padEvalFormat ( $eval ) {

    return (bool) preg_match ( "/^%(?:\\d+\\$)?(?:[-+ 0]|'.)*\\d*(?:\\.\\d+)?[bcdeEfFgGhHosuxX%]/", trim ( $eval ) );

  }

  // The length of a format that opens at $at, a pipe part inside brackets - ($p | %.2f),
  // php:strlen($n | %05d), [$a | %02d, $b | %02d] - or 0 when none does. It runs to the |,
  // the comma or the bracket that ends its part; brackets inside it are its text. Read as an
  // expression the % was the modulo operator: (1.25 | %.1f) divided by zero.

  function padEvalFormatLength ( $text, $at ) {

    if ( ! preg_match ( "/^%(?:\\d+\\$)?(?:[-+ 0]|'.)*\\d*(?:\\.\\d+)?[bcdeEfFgGhHosuxX%]/", substr ( $text, $at ), $match ) )
      return 0;

    $len   = strlen ( $text );
    $depth = 0;

    for ( $end = $at + strlen ( $match [0] ); $end < $len; $end++ ) {

      $one = $text [$end];

      if     ( $one == '(' or $one == '[' )                $depth++;
      elseif ( ( $one == ')' or $one == ']' ) and $depth ) $depth--;
      elseif ( in_array ( $one, [ ')', ']', ',', '|' ] ) ) break;

    }

    return $end - $at;

  }

  // The pipe segments of an expression, each [ text, whether it is a format ], when one of
  // them is a printf format (padEvalFormat) - FALSE when none is. A segment ends at a | that
  // stands outside a string and outside brackets, where padEvalPipes cuts - not at a || -
  // and a format runs to the next | whatever it holds, %'*10s pads with a quote.

  function padEvalFormatSegments ( $text ) {

    $segments = [];
    $formats  = FALSE;
    $len      = strlen ( $text );
    $from     = 0;

    while ( TRUE ) {

      if ( padEvalFormat ( substr ( $text, $from ) ) ) {

        $end     = strpos ( $text, '|', $from );
        $end     = ( $end === FALSE ) ? $len : $end;
        $formats = TRUE;

        $segments [] = [ substr ( $text, $from, $end - $from ), TRUE ];

      } else {

        $quote = '';
        $depth = 0;

        for ( $end = $from; $end < $len; $end++ ) {

          $one = $text [$end];

          if     ( $one == '\\' )                  $end++;
          elseif ( $quote and $one == $quote )     $quote = '';
          elseif ( $quote )                        continue;
          elseif ( $one == "'" or $one == '"' )    $quote = $one;
          elseif ( $one == '(' or $one == '[' )    $depth++;
          elseif ( $one == ')' or $one == ']' )    $depth--;
          elseif ( substr ( $text, $end, 2 ) == '||' ) $end++;      // the or operator
          elseif ( $one == '|' and $depth <= 0 )   break;

        }

        $end = min ( $end, $len );

        $segments [] = [ substr ( $text, $from, $end - $from ), FALSE ];

      }

      if ( $end >= $len )
        break;

      $from = $end + 1;

    }

    return $formats ? $segments : FALSE;

  }

  function padEvalScanText ( $eval ) {

    $in    = padUnescape ( $eval );
    $len   = strlen ( $in );
    $tok   = [];
    $word  = FALSE;   // the last token is a word, which letters and digits go on with
    $depth = 0;       // the brackets open
    $args  = [];      // the tokens a comma or a => stands after
    $signs = [];      // the numbers that took their sign along
    $at    = 0;

    while ( $at < $len ) {

      $one   = $in [$at];
      $next  = $in [$at+1] ?? '';
      $next2 = $in [$at+2] ?? '';
      $last  = count ( $tok ) - 1;

      if ( $word and preg_match ( '/^[a-zA-Z0-9_]/', $one ) ) {
        $tok [$last] [0] .= $one;
        $at++;
        continue;
      }

      // The properties that name what they read - option.sort@items, parameter.1@items,
      // variable.total@items - keep their dot: read as the . operator it tore the reference
      // apart. Only where the whole name@target follows.

      if ( $word and $one == '.' and in_array ( $tok [$last] [0], [ 'option', 'parameter', 'variable' ] )
           and preg_match ( '/^\.([a-zA-Z0-9_]+)@([a-zA-Z_]|-\d)/', substr ( $in, $at ), $dotted ) ) {
        $tok [$last] [0] .= '.' . $dotted [1];
        $at += 1 + strlen ( $dotted [1] );
        continue;
      }

      if ( $one == '\\' ) {
        padEvalScanEscape ( $next, FALSE );
        $at += 2;
        continue;
      }

      $was  = $word;
      $word = FALSE;

      if ( $one == "'" or $one == '"' ) {

        $tok [] = [ padEvalScanString ( $in, $at ), 'VAL', 'lit' => TRUE ];

      } elseif ( ( $one == '&' and $next == '&' ) or ( $one == '|' and $next == '|' ) ) {

        // && and || are and and or, as PHP and JavaScript write them.

        $tok [] = [ ( $one == '&' ) ? 'AND' : 'OR', 'OPR', $one . $next ];
        $at    += 2;

      } elseif ( $one == '|' ) {

        $tok [] = [ '|', 'pipe' ];
        $at++;

        // A part inside brackets that is a printf format is one token, as a segment is.

        if ( $depth > 0 ) {

          for ( $from = $at; $from < $len and ctype_space ( $in [$from] ); $from++ ) ;

          $flen = padEvalFormatLength ( $in, $from );

          if ( $flen ) {
            $tok [] = [ rtrim ( substr ( $in, $from, $flen ) ), '%' ];
            $at     = $from + $flen;
          }

        }

      } elseif ( $one == '(' or $one == '[' ) {

        $tok [] = [ $one, ( $one == '(' ) ? 'open' : 'a-open' ];
        $depth++;
        $at++;

      } elseif ( $one == ')' or $one == ']' ) {

        $tok [] = [ $one, ( $one == ')' ) ? 'close' : 'a-close' ];
        $depth--;
        $at++;

      } elseif ( $one == '@' ) {

        $at = padEvalScanAt ( $in, $at, $tok, $was );

      } elseif ( $one == '$' and $next == '$' and preg_match ( '/^[a-zA-Z_\x80-\xff]/', $next2 ) ) {

        // $$ before a name is one round of indirection, as {$$x} has it: the field whose
        // name the field x holds.

        $at    += 3;
        $tok [] = [ padEvalScanField ( $in, $at, $next2 ), '$$n' ];

      } elseif ( $one == '$' and $next == '$' ) {

        $tok [] = [ 1 => '$$' ];
        $at    += 2;

      } elseif ( ( $one == '#' or $one == '&' ) and preg_match ( '/^[a-zA-Z0-9_-]/', $next ) ) {

        $at    += 2;
        $tok [] = [ padEvalScanName ( $in, $at, $next ), $one ];

      } elseif ( $one == '$' and padEvalScanStart ( $next, $next2 ) ) {

        $at    += 2;
        $tok [] = [ padEvalScanField ( $in, $at, $next ), '$' ];

      } elseif ( $one == '0' and strtoupper ( $next ) == 'X' and ctype_xdigit ( $next2 ) ) {

        $at    += 3;
        $tok [] = [ $next2 . padEvalScanRun ( $in, $at, 'ctype_xdigit' ), 'hex' ];

      } elseif ( padEvalScanNumbered ( $in, $at, $tok ) ) {

        if ( $one == '-' or $one == '+' )
          $signs [] = count ( $tok );

        $tok [] = [ padEvalScanNumber ( $in, $at ), 'VAL' ];

      } elseif ( ( $one == '-' or $one == '+' ) and padEvalScanSign ( $in, $at, $tok )
                 and ( padEvalScanComma ( $in, $at ) or ( $last >= 0 and $tok [$last] [1] != 'pipe' ) ) ) {

        $tok [] = [ ( $one == '-' ) ? 'NEG' : 'POS', 'OPR', $one ];
        $at++;

      } elseif ( $one == '?' ) {

        // ?? is the empty-coalescing operator and ? : the inline ternary. A : glued to a
        // word went with it, as in php:strlen; only a : standing apart is the ternary's.

        $tok [] = [ ( $next == '?' ) ? '??' : '?', 'OPR' ];
        $at    += ( $next == '?' ) ? 2 : 1;

      } elseif ( $one == ':' and ! $was ) {

        $tok [] = [ ':', 'OPR' ];
        $at++;

      } elseif ( $one == '=' and $next == '>' ) {

        // => pairs a key with its value in a [ ] list; the token after it starts the value.

        $tok  [] = [ '=>', 'arrow' ];
        $args [] = count ( $tok );
        $at     += 2;

      } elseif ( in_array ( $one, [ '<', '>', '=' ] ) or ( $one == '!' and $next == '=' ) ) {

        // < > and = are operators wherever they stand: $x<=3, $x<3. [2] keeps the spelling
        // the template wrote, for the strict check to name.

        $pair   = isset ( padEval_alt [ $one . $next ] );
        $tok [] = [ padEval_alt [ $pair ? $one . $next : $one ], 'OPR', $pair ? $one . $next : $one ];
        $at    += $pair ? 2 : 1;

      } elseif ( $one == '*' and $next == '*' ) {

        $tok [] = [ '**', 'OPR' ];
        $at    += 2;

      } elseif ( in_array ( $one, padEval_1 ) ) {

        $tok [] = [ $one, 'OPR' ];
        $at++;

      } elseif ( ctype_space ( $one ) ) {

        $at++;

      } elseif ( $one == ',' ) {

        if ( $depth > 0 )
          $args [] = $last + 1;

        $at++;

      } else {

        // Anything else is a letter of a word - a function, a reference, a constant, or an
        // operator word - which goes on as long as no token claims what follows.

        if ( $was )
          $tok [$last] [0] .= $one;
        else
          $tok [] = [ $one, 'other' ];

        $word = TRUE;
        $at++;

      }

    }

    return padEvalScanMarks ( $tok, $signs, $args );

  }

  // A sign before a number that stands before ** is the prefix it is elsewhere: -3 ** 2 is
  // -9, as in PHP and as -$b ** 2 is. Then the token after each comma in brackets and after
  // each => - a sign split off its number included - is marked as the start of an argument.

  function padEvalScanMarks ( $tok, $signs, $args ) {

    $out   = [];
    $first = [];

    foreach ( $tok as $n => $token ) {

      $first [$n] = count ( $out );

      if ( in_array ( $n, $signs ) and ( $tok [$n+1] [1] ?? '' ) == 'OPR' and $tok [$n+1] [0] === '**' ) {
        $out []     = [ $token [0] [0] == '-' ? 'NEG' : 'POS', 'OPR', $token [0] [0] ];
        $token [0]  = substr ( $token [0], 1 );
      }

      $out [] = $token;

    }

    foreach ( $args as $n )
      if ( isset ( $first [$n] ) )
        $out [ $first [$n] ] ['arg'] = TRUE;

    return $out;

  }

  // A backslash escapes the next character inside a string: \n \r \t, and \' \" \\ as
  // themselves. Any other character is reported under the strict syntax check and kept, and
  // outside a string the backslash and its character are reported and dropped.

  function padEvalScanEscape ( $next, $inside ) {

    global $padCheckSyntax;

    $map = [ 'n' => "\n", 'r' => "\r", 't' => "\t" ];

    if ( $padCheckSyntax and ! isset ( $map [$next] ) and ! in_array ( $next, [ "'", '"', "\\" ] ) )
      padError ( "Unsupported \\ char" );

    if ( $padCheckSyntax and ! $inside )
      padError ( "Escape \\ char only allowed inside a string" );

    return $map [$next] ?? $next;

  }

  // A string in ' or ", from its opening quote at $at to its closing one; the other quote is
  // an ordinary character inside it.

  function padEvalScanString ( $in, &$at ) {

    $quote = $in [$at++];
    $text  = '';
    $len   = strlen ( $in );

    while ( $at < $len ) {

      $one = $in [$at];

      if ( $one == $quote ) {
        $at++;
        break;
      }

      if ( $one == '\\' ) {
        $text .= padEvalScanEscape ( $in [$at+1] ?? '', TRUE );
        $at   += 2;
        continue;
      }

      $text .= $one;
      $at++;

    }

    return $text;

  }

  // The characters from $at on that pass the test $test.

  function padEvalScanRun ( $in, &$at, $test ) {

    $text = '';

    while ( isset ( $in [$at] ) and $test ( $in [$at] ) ) {
      $text .= $in [$at];
      $at++;
    }

    return $text;

  }

  // The name of an option (#) or a property (&) after its first character.

  function padEvalScanName ( $in, &$at, $text ) {

    return $text . padEvalScanRun ( $in, $at, fn ( $one ) => (bool) preg_match ( '/^[a-zA-Z0-9_:]/', $one ) );

  }

  // The name of a field after its first character: on as far as padEvalScanValid lets it,
  // or a positional step ends the path (padEvalScanStep).

  function padEvalScanField ( $in, &$at, $text ) {

    while ( isset ( $in [$at] ) ) {

      $one   = $in [$at];
      $next  = $in [$at+1] ?? '';
      $next2 = $in [$at+2] ?? '';

      if ( ! padEvalScanValid ( $one, $next, $next2, substr ( $text, -1 ) )
           and ! padEvalScanStep ( $one, $text, substr ( $in, $at + 1, 40 ) ) )
        break;

      $text .= $one;
      $at++;

    }

    return $text;

  }

  // Whether a number starts at $at: a digit, a . before a digit after a comma or where no
  // value stands before it - after one it is the concatenation, 'a'.1 is a1, and [1, .5]
  // was [1 . 5] - or a sign before either.

  function padEvalScanNumbered ( $in, $at, $tok ) {

    $one   = $in [$at];
    $next  = $in [$at+1] ?? '';
    $next2 = $in [$at+2] ?? '';

    if ( ctype_digit ( $one ) )
      return TRUE;

    if ( $one == '.' )
      return ctype_digit ( $next ) and padEvalScanSign ( $in, $at, $tok );

    return ( $one == '-' or $one == '+' )
       and ( ctype_digit ( $next ) or ( $next == '.' and ctype_digit ( $next2 ) ) )
       and padEvalScanSign ( $in, $at, $tok );

  }

  // Whether the - + or . at $at opens a number: after a comma - mid(2, -1) - or where no
  // value stands before it.

  function padEvalScanSign ( $in, $at, $tok ) {

    return padEvalScanComma ( $in, $at ) or ! padEvalAfterValue ( $tok, count ( $tok ) - 1 );

  }

  // Whether a comma is the last thing before $at, whitespace aside.

  function padEvalScanComma ( $in, $at ) {

    for ( $back = $at - 1; $back >= 0 and ctype_space ( $in [$back] ); $back-- ) ;

    return $back >= 0 and $in [$back] == ',';

  }

  // A number: digits, a fraction, an exponent - 1.5E-10 - and two dots between digits as a
  // range kept whole, the text 4..10 a sequence reads. A 0x before a hex digit is a hex value
  // of its own.

  function padEvalScanNumber ( $in, &$at ) {

    $text = $in [$at++];

    while ( isset ( $in [$at] ) ) {

      $one   = $in [$at];
      $next  = $in [$at+1] ?? '';
      $next2 = $in [$at+2] ?? '';

      if ( $one == '0' and strtoupper ( $next ) == 'X' and ctype_xdigit ( $next2 ) )
        break;
      elseif ( ctype_digit ( $one ) ) {
        $text .= $one;
        $at++;
      } elseif ( $one == '.' and $next == '.' and ctype_digit ( $next2 ) and ! str_contains ( $text, '.' ) ) {
        $text .= '..';
        $at   += 2;
      } elseif ( $one == '.' and ctype_digit ( $next ) ) {
        $text .= '.';
        $at++;
      } elseif ( strtoupper ( $one ) == 'E' and ( ctype_digit ( $next ) or ( ( $next == '-' or $next == '+' ) and ctype_digit ( $next2 ) ) ) ) {
        $text .= $one . $next;
        $at   += 2;
      } else
        break;

    }

    return $text;

  }

  // An @ after a property name - one with a file in pad/properties/ - and a target, a level
  // name or a relative -N, is one reference: {if first@items} asks for the iteration
  // property. With no target - {if current@ eq 2} - it is the property of the loop the
  // expression stands in. Any other @ is the placeholder for the value piped in.

  function padEvalScanAt ( $in, $at, &$tok, $word ) {

    $last = count ( $tok ) - 1;
    $rest = substr ( $in, $at + 1 );

    if ( $word and file_exists ( PAD . 'properties/' . strtok ( $tok [$last] [0], '.' ) . '.php' ) ) {

      if ( preg_match ( '/^([a-zA-Z_][a-zA-Z0-9_]*|-\d+)/', $rest, $target ) ) {
        $tok [$last] = [ $tok [$last] [0] . '@' . $target [1], 'prop' ];
        return $at + 1 + strlen ( $target [1] );
      }

      if ( ! preg_match ( '/^[a-zA-Z0-9_$@.\'"]/', $rest ) ) {
        $tok [$last] = [ $tok [$last] [0] . '@', 'prop' ];
        return $at + 1;
      }

    }

    $tok [] = [ 1 => '$$' ];

    return $at + 1;

  }

  // Whether the character after a $ can open a name.

  function padEvalScanStart ( $next, $next2 ) {

    if ( $next == '-' and ctype_xdigit ( $next2 ) )         return TRUE;
    if ( preg_match ( '/^[a-zA-Z0-9_\x80-\xff]/', $next ) ) return TRUE;
    if ( $next == '*' and in_array ( $next2, ['.','@'] ) )  return TRUE;
    if ( $next == '<' and in_array ( $next2, ['.','@'] ) )  return TRUE;
    if ( $next == '>' and in_array ( $next2, ['.','@'] ) )  return TRUE;
    if ( $next == '<' and ctype_digit ( $next2 ) )          return TRUE;
    if ( $next == '>' and ctype_digit ( $next2 ) )          return TRUE;

    return FALSE;

  }

  // Whether a character goes on with a field's name, $prev the one before it. A : goes on
  // only before the name it prefixes - $items:name - and before anything else it is the
  // ternary's: $a ? $b:$c. A level number is a target - $name@1 - as {$name@1} reads it.

  function padEvalScanValid ( $one, $next, $next2, $prev ) {

    if ( preg_match ( '/^[a-zA-Z0-9\x80-\xff]$/D', $one ) )                   return TRUE;
    if ( $one == '_' )                                                       return TRUE;
    if ( $one == ':' and preg_match ( '/^[a-zA-Z_\x80-\xff]/', $next ) )     return TRUE;
    if ( $one == '.' and preg_match ( '/^[a-zA-Z0-9_\x80-\xff]/', $next ) )  return TRUE;
    if ( $one == '.' and in_array ( $next, ['<','>','*'] ) )                 return TRUE;
    if ( $one == '*' and in_array ( $next, ['.','@'] ) )                     return TRUE;
    if ( $one == '<' and in_array ( $next, ['.','@'] ) )                     return TRUE;
    if ( $one == '>' and in_array ( $next, ['.','@'] ) )                     return TRUE;
    if ( $one == '@' and ctype_alpha ( $next ) )                             return TRUE;
    if ( $one == '@' and $next == '-' and ctype_digit ( $next2 ) )           return TRUE;
    if ( $one == '@' and ctype_digit ( $next ) )                             return TRUE;
    if ( $one == '<' and ctype_digit ( $next ) and $prev == '.' )            return TRUE;
    if ( $one == '>' and ctype_digit ( $next ) and $prev == '.' )            return TRUE;
    if ( $one == '-' and in_array ( $prev, ['.','@'] ) )                     return TRUE;

    return FALSE;

  }

  // A positional step at the end of a path - $u.tags.1> is the first from the end, .3< the
  // third from the start - as the {$u.tags.1>} form reads it: a < or > after a dot and digits
  // where the path ends - the end of the text, a | ) ] , or a space with no value after it.
  // Followed by a value it stays the comparison: $a.1>3, $a.1 > $b.

  function padEvalScanStep ( $one, $name, $rest ) {

    if ( ( $one != '<' and $one != '>' ) or ! preg_match ( '/\.\d+$/', $name ) )
      return FALSE;

    if ( $rest === '' or preg_match ( '/^[|)\],.]/', $rest ) )
      return TRUE;

    if ( ! ctype_space ( $rest [0] ) )
      return FALSE;

    return ! preg_match ( '/^\s*[0-9$\'"(+\-.@!]/', $rest );

  }

  // Whether the token at $n is a value - so that a - or + after it is an operator, not a
  // sign: a literal, a field, a tag or option reference, the @ placeholder, a closing
  // bracket, a property, or a bare word that is not itself an operator.

  function padEvalAfterValue ( $tokens, $n ) {

    $kind = $tokens [$n] [1] ?? '';

    if ( in_array ( $kind, [ 'VAL', '$', '$$n', '&', '#', '$$', 'close', 'a-close', 'hex', 'prop' ] ) )
      return TRUE;

    if ( $kind == 'other' )
      return ! in_array ( strtoupper ( $tokens [$n] [0] ), padEval_txt ) and ! isset ( padEval_alt [ $tokens [$n] [0] ] );

    return FALSE;

  }

?>
