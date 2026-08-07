<?php

  // Runs a sequence type from inside an expression and returns one term of it.
  //
  // Reached from start/eval/sequence.php when the evaluator meets sequence:<name>(n) and the name
  // matches a directory in types/. inits/direct.php imports the engine globals (the whole chain
  // runs inside a function) and inits/eval.php reads the call: the name is the type, the argument
  // is which term is wanted.
  //
  // The steps are the tag path's, minus the ones that only make sense for a tag. There is no
  // level to publish to, no store to update and no data to shape, so exits/exits.php is not used
  // and only the type's own exit hook and the actions run.
  //
  // What comes back is the term itself, not a list of one: sequence:fibonacci(8) is a way of
  // asking for the eighth Fibonacci number, and 13 is the answer to that. The action half of the
  // prefix - sequence:sum([1,2,3]), which sequence/action.php serves - answers with an array
  // instead, because what an action produces is a list.

  include PQ . 'inits/direct.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';
  include PQ . 'inits/eval.php';
  include PQ . 'build/inits.php';
  include PQ . 'inits/init.php';
  include PQ . 'plays/inits.php';
  include PQ . 'inits/limits.php';
  include PQ . 'actions/inits.php';
  include PQ . 'build/build.php';
  include PQ . 'exits/exit.php';
  include PQ . 'exits/actions.php';
  include PQ . 'exits/done.php';
  include PQ . 'exits/info.php';

  $pqEvalResult = array_values ( $pqResult );

  return end ( $pqEvalResult );

?>