<?php

  // dotsandboxes - dots and boxes, the pencil-and-paper game Édouard Lucas described in 1889:
  // two players take turns drawing a line between two neighbouring dots, and who draws the
  // fourth side of a box takes it and draws again; when every box is taken the most boxes win.
  // Against the computer, which takes the boxes it is given, gives none away while it can,
  // then hands over the shortest chain, and at the end of a long chain leaves the last two
  // boxes to keep control. Click a line, or move along the lines with the arrow keys and draw
  // one with Enter. height= and width= the boxes, 4 by 4 unless given (2 to 9);
  // opens='computer' lets it begin, two is a game of two players at one screen.

  return padGame ( 'dotsandboxes', 'Dots and boxes', [
    'height' => padGameInt ( 'height', 4, 2, 9 ),
    'width'  => padGameInt ( 'width', 4, 2, 9 ),
    'opens'  => padGameChoice ( 'opens', 'you', [ 'you', 'computer' ] ),
    'two'    => padGameFlag ( 'two' )
  ] );

?>
