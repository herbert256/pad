<?php

  // morris - nine men's morris, the mill game played since Roman times: on three squares
  // joined at their middles each player first places nine pieces on the points, one a turn,
  // then moves one a turn along a line to a free point next to it, and with three left may
  // jump to any free point. Three in a row along a line make a mill, and a mill takes a piece
  // of the other player - one in a mill only when all of them are. Two pieces left, or no
  // move, loses; fifty moves without a piece taken, or a position seen a third time, is a
  // draw. Against the computer, which looks ahead as far as level= says - easy, normal (the
  // default) or hard; opens='computer' lets it begin, two is a game of two players at one
  // screen. Click a point, or move along the lines with the arrow keys and choose with Enter.

  return padGame ( 'morris', 'Nine men’s morris', [
    'level' => padGameChoice ( 'level', 'normal', [ 'easy', 'normal', 'hard' ] ),
    'opens' => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] ),
    'two'   => padGameFlag ( 'two' )
  ] );

?>
