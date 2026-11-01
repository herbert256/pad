<?php

  // tictactoe - three in a row on a board of three by three, against the computer, which
  // looks every game through to its end and so never loses: a game played well is a draw.
  // opens='computer' lets it open; two is a game of two players at one screen.
  //
  //   {tictactoe/}
  //   {game 'tictactoe', opens='computer'/}

  return padGame ( 'tictactoe', 'Tic-tac-toe', [
    'opens' => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] ),
    'two'   => padGameFlag ( 'two' )
  ] );

?>
