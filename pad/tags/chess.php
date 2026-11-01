<?php

  // {chess 'fen', flip, highlight='e2, e4', arrow='e2-e4', size=360, title='name'/} - a chess
  // position drawn as a board: the chess notation, subs/notations/types/chess.php, the same
  // drawing as {notation:chess 'fen'/} and {notation 'chess', 'fen'/}. A built-in tag, as it
  // was before the notations: {chess} comes before a field, a data store, a snippet or a _data
  // file named chess, where a notation's bare name comes after them all - a chess club's page
  // with its games in $chess printed them where the board stood. The FEN is the first
  // parameter, or the content between the tags.

  $padNotationKind  = 'chess';
  $padNotationGiven = (bool) array_filter ( $padParms [$pad], fn ( $one ) => $one ['padPrmKind'] == 'parm' and $one ['padPrmName'] == 1 );

  return include PAD . 'subs/notations/notation.php';

?>
