<?php

  // reversi - on a board of eight by eight a disc is placed so that it closes a line of the
  // other colour's discs between itself and one of its own, and every disc it closes in turns
  // over; a player who cannot move passes, and when neither can the most discs win. Against
  // the computer, which prefers the corners and the edges and keeps away from the squares
  // that give them up; opens='computer' lets it open, two is a game of two players.

  return padGame ( 'reversi', 'Reversi', [
    'opens' => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] ),
    'two'   => padGameFlag ( 'two' )
  ] );

?>
