<?php

  // automaton - an elementary cellular automaton: a row of cells, each on or off, and every
  // next row worked out from the one above by a rule that looks at a cell and its two
  // neighbours. Its eight cases are numbered by the rule - Wolfram's 256 rules, 30 the chaos
  // of a cone shell, 90 a Sierpinski triangle, 110 a computer. The rows scroll up as they
  // come; click a case of the rule to flip it, click the newest row to flip a cell. rule= the
  // number (30 unless given, 0 to 255); cells= the cells of a row (240, 40 to 480); initial=
  // single for one cell on in the middle, random for a row of chance; speed= the rows a
  // second (30, 1 to 120).
  //
  //   {toy:automaton rule=110, initial='random'/}

  return padToy ( 'automaton', 'Cellular automaton', [
    'rule'    => padToyInt ( 'rule', 30, 0, 255 ),
    'cells'   => padToyInt ( 'cells', 240, 40, 480 ),
    'initial' => padToyChoice ( 'initial', 'single', [ 'single', 'random' ] ),
    'speed'   => padToyInt ( 'speed', 30, 1, 120 )
  ] );

?>
