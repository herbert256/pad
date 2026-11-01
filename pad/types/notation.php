<?php

  // Type handler for a notation: {notation:math}a/b{/notation:math}, {notation:chess 'fen'/},
  // or a bare tag naming a file of subs/notations/types/ when nothing else has the name -
  // {math}a/b{/math}, {chess 'fen'/}. The kind is the tag's name, its text the tag's first
  // parameter or else its content, and it is drawn as {notation 'math'} draws it, by
  // subs/notations/notation.php - run as a built-in tag's PHP half is run, types/_go/tag.php.

  // Whether the tag has a first parameter - $padOpt holds an empty one when it has none.

  $padNotationKind  = $padTag [$pad];
  $padNotationGiven = (bool) array_filter ( $padParms [$pad], fn ( $one ) => $one ['padPrmKind'] == 'parm' and $one ['padPrmName'] == 1 );

  $padTagGo = PAD . 'subs/notations/notation';

  return include PAD . 'types/_go/tag.php';

?>
