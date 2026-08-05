<?php

  // Runs a sequence action from inside an expression and returns the result as a plain array.
  //
  // Reached from start/eval/action.php when the evaluator meets a call whose name matches one
  // of actions/types/, with the action in $pqSetAction and its arguments in $pqSetParms.
  // actions/set.php makes the first argument the values to act on - a literal array becomes a
  // 'given' build, a store name a 'pull' - and folds the rest into the action's parameter.
  //
  // The steps are the same as sequence/sequence.php; the two entry points are kept as
  // separate files so $pqSetStart records which one the run came in through.

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