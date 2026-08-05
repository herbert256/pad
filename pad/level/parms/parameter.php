<?php

  // Stores a positional parameter: the item is evaluated and appended to $padOpt [$pad],
  // whose slot 0 holds the raw option text, so the tag's first parameter lands in slot 1 -
  // what the handler sees as $padParm and what {parm:...} reads.

  $padPrmEval       = padEval ( $padPrmOne );

  $padOpt [$pad] [] = $padPrmEval;

  $padParmsSetType  = 'parm';
  $padParmsSetName  = array_key_last ( $padOpt [$pad] );
  $padParmsSetValue = $padPrmEval;

?>