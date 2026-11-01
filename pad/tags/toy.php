<?php

  // {toy 'life', pattern='glider'/} - a small live simulation or visual toy played in the
  // browser, of the kind its parameter names: a kind is a file of subs/toys/types/, set up by the
  // toys subsystem in subs/toys/, the same toy as the toy: type writes it, {toy:life/}.

  $padToyKind = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'subs/toys/toy.php';

?>
