<?php

  // Build strategy 'loop' for the eval sequence: the term is the parameter evaluated as a
  // PAD expression with the loop value substituted for @, so {eval '@ * @ + 1'} gives 2, 5,
  // 10, 17, ... The general escape hatch for a formula that has no sequence type of its
  // own; as a play, {make eval='@ * 2'} rewrites another sequence's terms.

  return padEval ( $pqParm, $pqLoop );

?>