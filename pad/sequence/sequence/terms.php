<?php

  // Runs a sequence type directly and hands back every term it produced, for the membership
  // checks that have to generate a sequence to look in it - build/check.php and
  // plays/play/order.php, through pqTerms() in lib/sequence.php.
  //
  // Those used pqArray(), which writes a {sequence} tag pair and renders it through padCode:
  // every term cost a rendered occurrence, about forty times what generating it costs, so a
  // flag over values past a type's table spent seconds on each candidate and a range of them
  // ran into the time limit; and the parameter was pasted into the tag as template text, so
  // eval='@*2' arrived as an expression of its own - its membership read six zeros - and a
  // parameter with a comma in it split into two options.
  //
  // The steps are sequence/sequence.php's, with the type and its parameter handed over as
  // data and the run limited by the named parameters in $pqSetParms (stop, to or sole). The
  // run is direct, so it lives in the caller's function scope and the tag run that asked
  // keeps its own state. Nothing is pulled and nothing plays: a pull or a play the level
  // around it carries is not this run's.

  include PQ . 'inits/direct.php';
  include PQ . 'inits/clear.php';
  include PQ . 'inits/vars.php';

  $pqSeq   = $pqSetAction;
  $pqParm  = $pqSetParm;
  $pqPull  = '';
  $pqBuild = '';

  include PQ . 'build/inits.php';
  include PQ . 'inits/init.php';
  include PQ . 'inits/limits.php';
  include PQ . 'build/build.php';

  return array_values ( $pqResult );

?>
