<?php

  // Stage one of the evaluator: the tokeniser. padEvalParse walks the expression text one
  // character at a time and fills &$result with tokens, keyed 100 apart so later stages
  // can splice values in and drop tokens without renumbering. Each token is
  // [0] => text, [1] => kind, and later stages add [2]/[3] for typed references.
  //
  // Kinds produced here: VAL (quoted string, number, hex payload), OPR (operator), '$'
  // (field), '&' (tag), '#' (option), '$$' (the @ placeholder holding the piped value),
  // pipe, open/close for ( ), a-open/a-close for [ ], hex, and 'other' for a bare word
  // that only padEvalAfter can classify. An expression starting with % is not tokenised
  // at all - it is kept whole as a printf format for padEvalValue.
  //
  // The two predicates decide where a $name ends: padEvalParseStart says whether the
  // character after $ can open a name, padEvalParseValid whether a character continues
  // one. Both are deliberately generous so name@tag, dotted paths, prefixes with : and
  // the <, > and * wildcards of the at-syntax stay inside a single token instead of being
  // torn apart as operators.
  //
  // Numbers accept a leading sign, decimals, exponents and 0x hex; strings accept both
  // quote styles with \n \r \t \\ \' \" escapes. Whitespace and commas end the current
  // token and are otherwise ignored, so parameter lists need no special handling.

  function padEvalParseStart ( $next, $next2 ) {

    if ( $next == '-' and ctype_xdigit($next2) )         return TRUE;
    if ( preg_match('/^[a-zA-Z0-9_]/', $next))           return TRUE;
    if ( $next == '*' and in_array($next2, ['.','@'] ) ) return TRUE;
    if ( $next == '<' and in_array($next2, ['.','@'] ) ) return TRUE;
    if ( $next == '>' and in_array($next2, ['.','@'] ) ) return TRUE;
    if ( $next == '<' and ctype_digit($next2)  )         return TRUE;
    if ( $next == '>' and ctype_digit($next2)  )         return TRUE;

    return FALSE;

  }

  function padEvalParseValid ( $one, $next, $next2, $prev ) {

    if ( ctype_alpha($one) or ctype_digit($one) )                  return TRUE;
    if ( in_array($one, ['_',':'] ) )                              return TRUE;
    if ( $one == '.' and preg_match('/^[a-zA-Z0-9_]/', $next ) )   return TRUE;
    if ( $one == '.' and in_array($next, ['<','>','*'] ) )         return TRUE;
    if ( $one == '*' and in_array($next, ['.','@'] ) )             return TRUE;
    if ( $one == '<' and in_array($next, ['.','@'] ) )             return TRUE;
    if ( $one == '>' and in_array($next, ['.','@'] ) )             return TRUE;
    if ( $one == '@' and ctype_alpha($next) )                      return TRUE;
    if ( $one == '<' and ctype_digit($next) )                      return TRUE;
    if ( $one == '>' and ctype_digit($next) )                      return TRUE;
    if ( $one == '-' and in_array($prev, ['.','@'] ) )             return TRUE;

    return FALSE;

  }

  function padEvalParse (&$result, $eval ) {

    $result = [];

    $input = trim ($eval);

    if ( str_starts_with ( $input, '%' ) ) {
      $result [100] [0] = $input;
      $result [100] [1] = '%';
      return;
    }

    $input  = str_split ( padUnescape($eval) );
    $is_hex = $is_var = $is_prm = $is_tag = $is_str = $is_quote = $is_num = $is_other = FALSE;
    $skip   = $i = 0;

    foreach ( $input as $key => $one ) {

      if ($skip) {
        $skip--;
        continue;
      }

      if ( $is_other and $result[$i][0] and preg_match('/^[a-zA-Z0-9_]/', $one) ) {
        $result[$i][0] .= $one;
        continue;
      }

      $next  = (isset($input[$key+1])) ? $input[$key+1] : '';
      $next2 = (isset($input[$key+2])) ? $input[$key+2] : '';
      $prev  = (isset($result [$i] [0]) and $result [$i] [0]) ? substr($result [$i] [0],0,1) : '';

      if ( $is_var )
        if ( padEvalParseValid ( $one, $next, $next2, $prev )  ) {
          $result[$i][0] .= $one;
          continue;
        } else {
          $is_var = FALSE;
        }

      if ($one=="\\") {

        if ($next == 'n')
          $next = "\n";
        elseif ($next == 'r')
          $next = "\r";
        elseif ($next == 't')
          $next = "\t";
        elseif ( ! in_array ($next, ["'", '"', "\\"])) {

          // Reported under the strict syntax check; the lenient walk keeps the
          // character the backslash stood in front of.

          global $padCheckSyntax;

          if ( $padCheckSyntax )
            padError ( "Unsupported \\ char" );

        }

        if ($is_str or $is_quote) {
          $result [$i] [0] .= $next;
          $skip=1;
        } else {

          global $padCheckSyntax;

          if ( $padCheckSyntax )
            padError ( "Escape \\ char only allowed inside a string" );

          $skip=1;

        }

        continue;

      }

      if ($one=="'" and ! $is_quote) {

        if (!$is_str) {

          $is_str = TRUE;
          $is_other = FALSE;

          $i += 100;
          $result [$i] [0] = '';
          $result [$i] [1] = 'VAL';

        } else

          $is_str = FALSE;

        continue;

      }

      if ($one=='"' and ! $is_str) {

        if (!$is_quote) {

          $is_quote = TRUE;
          $is_other = FALSE;

          $i += 100;
          $result [$i] [0] = '';
          $result [$i] [1] = 'VAL';

        } else

          $is_quote = FALSE;

        continue;

      }

      if ($is_str or $is_quote) {

        $result [$i] [0] .= $one;

        continue;

      }

      if ($one == '|') {

        $i += 100;
        $result [$i] [0] = '|';
        $result [$i] [1] = 'pipe';
        $is_other = FALSE;

        continue;

      }

      if ($one == ')') {

        $i += 100;
        $result [$i] [0] = ')';
        $result [$i] [1] = 'close';
        $is_other = FALSE;

        continue;

      }

      if ($one == '(') {

        $i += 100;
        $result [$i] [0] = '(';
        $result [$i] [1] = 'open';
        $is_other = FALSE;

        continue;

      }

      if ($one == ']') {

        $i += 100;
        $result [$i] [0] = ']';
        $result [$i] [1] = 'a-close';
        $is_other = FALSE;

        continue;

      }

      if ($one == '[' ) {

        $i += 100;
        $result [$i] [0] = '[';
        $result [$i] [1] = 'a-open';
        $is_other = FALSE;

        continue;

      }

      if ($one == '@') {

        // A property name followed by @ and a target is one reference, not the placeholder:
        // {if first@items} asks for the iteration property. Only the names with a file in
        // pad/properties/ read this way - a closed set, so any other word@word keeps the
        // tokenisation it always had - and the target is a level name or a relative -N.
        // The bare spelling means the property and nothing else - a row field of the same
        // name shadows only the $-spelling - so the token gets its own kind, which
        // padEvalAfter resolves through padPropertyValue.

        if ( $is_other and ! empty ( $result[$i][0] )
             and file_exists ( PAD . 'properties/' . $result[$i][0] . '.php' ) ) {

          $padEvalRest = implode ( '', array_slice ( $input, $key + 1 ) );

          if ( preg_match ( '/^([a-zA-Z_][a-zA-Z0-9_]*|-\d+)/', $padEvalRest, $padEvalTarget ) ) {

            $result[$i][0] .= '@' . $padEvalTarget[1];
            $result[$i][1]  = 'prop';

            $is_other = FALSE;
            $skip     = strlen ( $padEvalTarget[1] );

            continue;

          }

        }

        $i += 100;
        $result[$i][1] = '$$';

        $is_other = FALSE;

        continue;

      }

      if ($one == '$' and $next == '$') {

        $i += 100;
        $result[$i][1] = '$$';

        $is_other = FALSE;
        $skip = 1;

        continue;

      }

      if ( $one == '#' and preg_match('/^[a-zA-Z0-9_-]/', $next) and ! $is_var and ! $is_tag ) {

        $is_prm   = TRUE;
        $is_other = FALSE;

        $i += 100;
        $result[$i][1] = '#';
        $result[$i][0] = $next;
        $skip = 1;
        continue;

      }

      if ( $is_prm ) {

        if ( preg_match('/^[a-zA-Z0-9_:]/', $one) ) {
          $result[$i][0] .= $one;
          continue;
        }

        $is_prm = FALSE;

      }

      if ( $one == '&' and preg_match('/^[a-zA-Z0-9_-]/', $next) and ! $is_var ) {

        $is_tag   = TRUE;
        $is_other = FALSE;

        $i += 100;
        $result[$i][1] = '&';
        $result[$i][0] = $next;
        $skip = 1;
        continue;

      }

      if ($is_tag) {

        if ( preg_match('/^[a-zA-Z0-9_:]/', $one) ) {
          $result[$i][0] .= $one;
          continue;
        }

        $is_tag = FALSE;

      }

      if ($one == '$' and padEvalParseStart ( $next, $next2 ) ) {

        $is_var   = TRUE;
        $is_other = FALSE;
        $skip = 1;

        $i += 100;
        $result[$i][1] = '$';
        $result[$i][0] = $next;

        continue;

      }

      if ($one == '0' and strtoupper($next) == 'X' and ctype_xdigit($next2) ) {

        $is_hex   = TRUE;
        $is_other = FALSE;

        $i += 100;
        $result[$i][1] = 'hex';
        $result[$i][0] = $next2;

        $skip = 2;

        continue;

      }

      if ( $is_hex ) {

        if ( ctype_xdigit($one) ) {
          $result[$i][0] .= $one;
          continue;
        } else
          $is_hex = FALSE;

      }

      if ( ! $is_num  and
           (      ctype_digit($one)
             or ( $one == '.' and ctype_digit($next) )
             or ( $one == '-' and ctype_digit($next) )
             or ( $one == '-' and $next == '.' and ctype_digit($next2) )
             or ( $one == '+' and ctype_digit($next) )
             or ( $one == '+' and $next == '.' and ctype_digit($next2) )
           )
         ) {

        $is_num = TRUE;
        $i += 100;
        $result[$i][0] = $one;
        $result[$i][1] = 'VAL';
        $is_other = FALSE;
        continue;

      }

      if ( $is_num ) {

        if ( ctype_digit($one) ) {
          $result[$i][0] .= $one;
          continue;
        }

        if ( $one == '.' and ctype_digit($next) ) {
          $result[$i][0] .= $one;
          continue;
        }

        if ( strtoupper($one) == 'E'
             and ( ctype_digit($next)
                   or ( $next == '-' and ctype_digit($next2) )
                   or ( $next == '+' and ctype_digit($next2) ) ) )  {
          $result[$i][0] .= $one . $next;
          $skip=1;
          continue;
        }

        $is_num = FALSE;

      }

      if ( $one == '!' and $next == '=' ) {

        $i += 100;
        $result [$i] [0] = 'NE';
        $result [$i] [1] = 'OPR';

        $is_other = FALSE;
        $skip = 1;

        continue;

      }

      if ( in_array($one.$next, padEval_2) ) {

        $i += 100;
        $result [$i] [0] = $one.$next;
        $result [$i] [1] = 'OPR';

        $is_other = FALSE;
        $skip = 1;

        continue;

      } elseif ( in_array($one, padEval_1) ) {

        $i += 100;
        $result [$i] [0] = $one;
        $result [$i] [1] = 'OPR';

        $is_other = FALSE;

        continue;

      }

      if (ctype_space($one)) {
        $is_other = FALSE;
        continue;
      }

      if ($one == ',') {
        $is_other = FALSE;
        continue;
      }

      if (!$is_other) {
        $is_other = TRUE;
        $i += 100;
        $result[$i][0] = '';
        $result[$i][1] = 'other';
      }

      $result[$i][0] .= $one;

    }

  }

?>