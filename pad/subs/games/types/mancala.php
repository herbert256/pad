<?php

  // mancala - oware, the mancala of West Africa, by the abapa rules: two rows of six houses
  // with four seeds each. A turn takes every seed of one of your houses and sows them one by
  // one into the houses after it, counter-clockwise, passing over the house they came from;
  // when the last seed makes a house of the other side two or three, those seeds are
  // captured, and so are the houses before it on that side that hold two or three. A move
  // that would capture every seed the other side has captures nothing, and a side left
  // without seeds must be given some when a move can. More than half the seeds wins; when
  // the seeds only go round, each side takes what is on its own. Against the computer, which
  // looks ahead as far as level= says - easy, normal (the default) or hard; seeds= the seeds
  // a house starts with, 4 unless given (3 to 6); opens='computer' lets it begin, two is a
  // game of two players at one screen. Click a house of yours, or move between them with the
  // arrow keys and sow with Enter.

  return padGame ( 'mancala', 'Mancala', [
    'seeds' => padGameInt ( 'seeds', 4, 3, 6 ),
    'level' => padGameChoice ( 'level', 'normal', [ 'easy', 'normal', 'hard' ] ),
    'opens' => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] ),
    'two'   => padGameFlag ( 'two' )
  ] );

?>
