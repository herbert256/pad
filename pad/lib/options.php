<?php

  // padParseOptions splits a tag's parameter text on commas, character by character, so
  // that commas inside 'single quotes', "double quotes", (parentheses) or [brackets] are
  // kept as part of one option. This is what lets an option carry a function call or a
  // list, as in {echo $d | date('D, d M')}. Unbalanced ) or ] raises a padError and the
  // whole option list is discarded.

  function padParseOptions ( $parms ) {

    $input  = str_split ( $parms );
    $output = [];

    $in_str   = FALSE;
    $in_quote = FALSE;
    $pair     = 0;

    $now  = '';
    $skip = FALSE;

    foreach ( $input as $key => $one ) {

      if ( $skip ) {
        $now .= $one;
        $skip = FALSE;
        continue;
      }

      if ( $one==',' and !$in_str and !$in_quote and !$pair ) {
        $output [] = $now;
        $now = '';
        continue;
      }

      $now .= $one;

      // A backslash before a quote escapes it, as padPipeSplit and the evaluator read it:
      // 'it\'s' is one string, not one that ends at the t and opens another at the s. And a
      // backslash escapes a backslash, as the evaluator reads 'a\\' as a\ - its second one
      // was taken to escape the closing quote, and the string ran on over the comma behind
      // it: {tag 'a\\', name='x'} lost its name= to the parameter.

      if ( $one == '\\' and in_array ( $input [$key+1] ?? '', [ "'", '"', '\\' ] ) ) {
        $skip = TRUE;
        continue;
      }

      if ( $one=="'" and $in_quote )
        continue;

      if ( $one=='"' and $in_str )
        continue;

      if ( $one==',' and ($in_str or $in_quote or $pair) )
        continue;

      if ( $one=='(' and ($in_str or $in_quote) )
        continue;

      if ( $one==')' and ($in_str or $in_quote) )
        continue;

     if ( $one=='[' and ($in_str or $in_quote) )
        continue;

      if ( $one==']' and ($in_str or $in_quote) )
        continue;

      // An orphan close bracket is reported under the strict syntax check; the lenient
      // walk keeps it as the ordinary character it already appended to $now.

      if ( $one==')' and !$pair ) {
        global $padCheckSyntax;
        if ( $padCheckSyntax ) {
          padError ("Closing ) without an opening (");
          return [];
        }
        continue;
      }

      if ( $one==']' and !$pair ) {
        global $padCheckSyntax;
        if ( $padCheckSyntax ) {
          padError ("Closing ] without an opening [");
          return [];
        }
        continue;
      }

      if ( $one=='(') {
        $pair++;
        continue;
      }

      if ( $one==')') {
        $pair--;
        continue;
      }

      if ( $one=='[') {
        $pair++;
        continue;
      }

      if ( $one==']') {
        $pair--;
        continue;
      }

      if ( $one=="'" and ! $in_str ) {
        $in_str = TRUE;
        continue;
      }

      if ( $one=='"' and ! $in_quote ) {
        $in_quote = TRUE;
        continue;
      }

      if ( $one=="'" and $in_str ) {
        $in_str = FALSE;
        continue;
      }

      if ( $one=='"' and $in_quote ) {
        $in_quote = FALSE;
        continue;
      }

    }

    if ($now !== '')
      $output [] = $now;

    // An option written after a parameter without its comma - {data 'rawJson' ignore}, the
    // form CLAUDE.md and the manual's ignore page show - is the option it names: a single
    // value, a space, and the name of an engine or application option. The evaluator read
    // the two as a value piped into the function of that name, so ignore('rawJson') named
    // the store and the option never applied. A word that names no option stays that pipe:
    // {echo 'abc' upper} is ABC. The data handlers (handling/types) are left out on purpose:
    // trim, left, right and reverse are pipe functions as well, and {echo $x trim} pipes.

    $split = [];

    foreach ( $output as $item )
      if ( preg_match ( '/^\s*(\'[^\']*\'|"[^"]*"|\$[A-Za-z][\w.]*|-?\d+(?:\.\d+)?)\s+([A-Za-z][A-Za-z0-9_]*)\s*$/D', $item, $match )
           and ( file_exists ( PAD . "options/{$match [2]}.php" ) or padOptionCheck ( $match [2] ) ) ) {
        $split [] = $match [1];
        $split [] = $match [2];
      } else
        $split [] = $item;

    return $split;

  }

?>