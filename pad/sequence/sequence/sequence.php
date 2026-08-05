<?php

  // Runs a sequence from inside an expression and returns its values as a plain array.
  //
  // Reached from start/eval/sequence.php when the evaluator meets a call whose name matches
  // a directory in types/; eval/parms/sequence.php has put the name in $pqSetAction and the
  // call's arguments in $pqSetParms. inits/direct.php imports the engine globals (the whole
  // chain runs inside a function), actions/set.php turns the first argument into the values
  // to work on - a literal array becomes a 'given' build, a store name a 'pull'.
  //
  // Unlike the tag path there is no level to publish to and no store to update: the caller
  // simply gets array_values ( $pqResult ).

  include PQ . 'inits/direct.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'actions/set.php';
  include PQ . 'plays/inits.php';
  include PQ . 'actions/inits.php';
  include PQ . 'inits/limits.php';
  include PQ . 'build/build.php';
  include PQ . 'exits/actions.php';
  include PQ . 'exits/done.php';
  include PQ . 'exits/info.php';

  return array_values ( $pqResult );

?>