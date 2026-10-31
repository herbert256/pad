<?php

  // gomoku - five in a row: black and white take turns on a square board, and the first to
  // have five of their stones in a line - across, down or diagonal - wins. Against the
  // computer, which weighs every free point by the lines it could make or block there;
  // size= the board (15, from 9 to 19), opens='computer' lets it open, two is a game of two
  // players at one screen.

  return padGame ( 'gomoku', 'Five in a row', [
    'size'  => padGameInt ( 'size', 15, 9, 19 ),
    'opens' => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] ),
    'two'   => padGameFlag ( 'two' )
  ] );

?>
