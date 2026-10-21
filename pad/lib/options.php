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

    // A run of characters that are none of , ' " \ ( ) [ ] changes nothing but $now, and is
    // added to it at once - a loop in PHP over every character of every tag's parameters was
    // a fiftieth of a page's time. A skip (a backslash's) always stands before one of them.

    $length = strlen ( $parms );

    for ( $key = 0; $key < $length; $key++ ) {

      if ( ! $skip and ( $run = strcspn ( $parms, ",'\"\\()[]", $key ) ) ) {
        $now .= substr ( $parms, $key, $run );
        $key += $run - 1;
        continue;
      }

      $one = $input [$key];

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
    // {echo 'abc' upper} is ABC. An application option with only an end-phase file -
    // _options/end/shout.php - is an option as well: {data 'd' shout} was "a comma is
    // missing", where {data 'd' frame}, which has both files, worked. The data handlers
    // (handling/types) are left out on purpose:
    // trim, left, right and reverse are pipe functions as well, and {echo $x trim} pipes.

    $split = [];

    foreach ( $output as $item )
      if ( preg_match ( '/^\s*(\'[^\']*\'|"[^"]*"|\$[A-Za-z][\w.]*|-?\d+(?:\.\d+)?)\s+([A-Za-z][A-Za-z0-9_]*)\s*$/D', $item, $match )
           and ( padFileExists ( PAD . "options/{$match [2]}.php" ) or padOptionCheck ( $match [2] ) or padOptionEndCheck ( $match [2] ) ) ) {
        $split [] = $match [1];
        $split [] = $match [2];
      } else
        $split [] = $item;

    return $split;

  }

  // A value handed to the application's own PHP - a parameter or option of an application or
  // _common tag, as the tag's PHP reads it from $padParm, $padOpt [$pad] or $padPrm [$pad],
  // and the value of an option the application implements in _options/ or _options/end/, its
  // $padGetName - has { } and | as inert stand-ins when it came from a value, the way
  // padTagParm hands it; quotes stay real, for htmlspecialchars. What such code echoes or
  // writes into $padContent is template source: padTagParm protected only the reads through
  // it, so {echoparm $v} echoing $padParm ran a visitor's {php:getcwd}, and {xs mark=$w} on a
  // built-in tag, through the documented _options/end/wrap.php pattern, did the same. The
  // stand-ins are restored when the page is written out.
  //
  // What the template wrote out - {props '{"a":"x"}'}, mark='b' - is the author's text and
  // stays as written (padAppLiteral); a value spliced into the quotes comes with stand-ins of
  // its own. And only the PHP gets the copies: padAppHandOver swaps them in around the
  // include of the tag's PHP half (types/_go/tag.php) and padAppHandBack puts the engine's
  // values back, so the engine's own options - where= with a pipe - and a .pad component's
  // {#title} read the text they were given.

  function padAppValue ( $value ) {

    global $padProtectValues;

    if ( ! $padProtectValues or ! is_string ( $value ) )
      return $value;

    return strtr ( $value, array_intersect_key ( padProtectMap (), [ '{' => 1, '}' => 1, '|' => 1 ] ) );

  }

  function padAppLevel () {

    global $pad, $padType;

    return in_array ( $padType [$pad] ?? '', [ 'app', 'common' ], TRUE );

  }

  function padAppLiteral ( $text ) {

    $read = padMetaLiteral ( $text );

    return is_string ( $read ) or is_int ( $read ) or is_float ( $read ) or is_bool ( $read );

  }

  // The copies padAppHandOver hands the tag's PHP, with the values they stand for: the
  // parameters by position in $padOpt, the options by name in $padPrm.

  function padAppHandOver () {

    global $pad, $padOpt, $padPrm, $padParms;

    $hand = [ 'opt' => [], 'prm' => [] ];

    foreach ( $padParms [$pad] ?? [] as $one ) {

      if ( $one ['padPrmKind'] == 'parm' )
        [ $kind, $list, $text ] = [ 'opt', 'padOpt', $one ['padPrmOrg'] ];
      elseif ( $one ['padPrmKind'] == 'option' )
        [ $kind, $list, $text ] = [ 'prm', 'padPrm', explode ( '=', (string) $one ['padPrmOrg'], 2 ) [1] ?? '' ];
      else
        continue;

      $name = $one ['padPrmName'];

      if ( ! isset ( $GLOBALS [$list] [$pad] [$name] ) or ! is_string ( $GLOBALS [$list] [$pad] [$name] ) or padAppLiteral ( $text ) )
        continue;

      $copy = padAppValue ( $GLOBALS [$list] [$pad] [$name] );

      if ( $copy === $GLOBALS [$list] [$pad] [$name] )
        continue;

      $hand [$kind] [$name]            = [ $GLOBALS [$list] [$pad] [$name], $copy ];
      $GLOBALS [$list] [$pad] [$name] = $copy;

    }

    return $hand;

  }

  // A copy the tag left as it was handed is the engine's value again; one the tag set itself
  // stays what it set.

  function padAppHandBack ( $hand, $idx ) {

    global $padOpt, $padPrm;

    foreach ( $hand ['opt'] as $name => [ $value, $copy ] )
      if ( ( $padOpt [$idx] [$name] ?? NULL ) === $copy )
        $padOpt [$idx] [$name] = $value;

    foreach ( $hand ['prm'] as $name => [ $value, $copy ] )
      if ( ( $padPrm [$idx] [$name] ?? NULL ) === $copy )
        $padPrm [$idx] [$name] = $value;

  }

  // Whether a name can be an option: a name of letters, digits and _, starting with a letter.
  // An option lives in $padPrm and is no variable, so a name with the engine's prefix -
  // padding, padlock, pqr - is one too, where padValidVar refuses it as a variable.

  function padValidOption ( $name ) {

    return is_string ( $name ) and preg_match ( '/^[a-zA-Z][a-zA-Z0-9_]*$/D', $name );

  }

  // Whether an option of this level was written out in the template - glue=', ', quote="'",
  // a number - rather than taken from a value, glue=$sep. A written-out option is the
  // author's template text; a value goes in as text (padProtect), or a separator a visitor
  // sent ran as template code. The value is the item's text behind its first =, read by
  // padMetaLiteral.

  function padOptionLiteral ( $name ) {

    global $pad, $padParms;

    foreach ( $padParms [$pad] ?? [] as $one )
      if ( $one ['padPrmKind'] == 'option' and $one ['padPrmName'] == $name ) {
        $text = explode ( '=', (string) $one ['padPrmOrg'], 2 ) [1] ?? '';
        $read = padMetaLiteral ( $text );
        return is_string ( $read ) or is_int ( $read ) or is_float ( $read ) or is_bool ( $read );
      }

    return FALSE;

  }

?>
