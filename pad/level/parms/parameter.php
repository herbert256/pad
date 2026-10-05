<?php

  // Stores a positional parameter: the item is evaluated and appended to $padOpt [$pad],
  // whose slot 0 holds the raw option text, so the tag's first parameter lands in slot 1 -
  // what the handler sees as $padParm and what {parm:...} reads.

  // A quoted parameter with an unclaimed bare word behind it is almost always a missing
  // comma - {data 'v' ignor} names the store "'v' ignor" and everything downstream blames
  // the wrong thing. A word that IS a known option is the documented space form - {data
  // 'rawJson' ignore} - and passes; the condition tags stay out entirely, their one
  // parameter being a whole expression where quotes and words mix legitimately.

  if ( $padCheckSyntax
       and ! in_array ( $padTag [$pad], [ 'if', 'elseif', 'while', 'until', 'case', 'when' ] )
       and preg_match ( "/^\s*('[^']*'|\"[^\"]*\")\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*$/", $padPrmOne, $padPrmShape )
       and ! file_exists ( PAD . "options/" . $padPrmShape [2] . ".php" )
       and ! file_exists ( PAD . "handling/types/" . $padPrmShape [2] . ".php" )
       and ! file_exists ( PAD . "functions/" . $padPrmShape [2] . ".php" )
       and ! padOptionCheck ( $padPrmShape [2] ) )
    padError ( "a comma is missing between parameters: " . trim ( $padPrmOne ) );

  // {increment}, {decrement} and {set} address their words by name and never read them
  // as values - the handler takes the raw text. Evaluating them anyway made the strict
  // evaluator report a counter that is allowed not to exist yet.

  //
  // The {tag ? true : false} option form is level/ternary.php's to read: its sides are not
  // an expression of their own, and evaluated as one they now made an inline ternary.

  if ( in_array ( $padTag [$pad], [ 'increment', 'decrement', 'set' ] )
       or str_starts_with ( ltrim ( $padOpt [$pad] [0] ?? '' ), '?' ) )
    $padPrmEval     = $padPrmOne;
  else
    $padPrmEval     = padEval ( $padPrmOne );

  $padOpt [$pad] [] = $padPrmEval;

  $padParmsSetType  = 'parm';
  $padParmsSetName  = array_key_last ( $padOpt [$pad] );
  $padParmsSetValue = $padPrmEval;

?>