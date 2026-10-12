<?php

  // A country's code and own name come before another's alias: "Congo" is the Congo (CG),
  // whose name it is, where the Democratic Republic of the Congo listed it as an alias and,
  // read first, took it - a map of Congo's figures coloured its neighbour.

  $codes = implode ( ' ', array_map ( 'padMapCode', [ 'Congo', 'DR Congo', 'Congo-Brazzaville', 'COD' ] ) );

?>
