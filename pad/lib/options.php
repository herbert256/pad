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
      // 'it\'s' is one string, not one that ends at the t and opens another at the s.

      if ( $one == '\\' and in_array ( $input [$key+1] ?? '', [ "'", '"' ] ) ) {
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

    return $output;

  }

?>