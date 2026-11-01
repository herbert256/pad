<?php

  // {notation 'math'}a/b{/notation} - a text written in a notation, drawn on the server by the
  // notations subsystem in subs/notations/: the kind is the first parameter, a file of
  // subs/notations/types/, the same notation as the notation: type draws, {notation:math}, and
  // the bare name, {math}. The kind's own text is the second parameter here - {notation
  // 'braille', 'Hello'/} - or, without one, the content between the tags, taken as written.

  $padNotationKind  = is_string ( $padParm ) ? strtolower ( trim ( $padParm ) ) : '';
  $padNotationGiven = (bool) array_filter ( $padParms [$pad], fn ( $one ) => $one ['padPrmKind'] == 'parm' and $one ['padPrmName'] == 2 );
  $padParm          = $padOpt [$pad] [2] ?? '';

  return include PAD . 'subs/notations/notation.php';

?>
