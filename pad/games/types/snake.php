<?php

  // snake - steer a snake round a field with the arrow keys, by swiping or with the buttons,
  // to eat the apples; each one makes it longer and a little faster, and it ends where the
  // snake runs into a wall or itself. size= the field, 18 squares a side unless given (10 to
  // 30); speed= the steps a second it starts with, 6 unless given (2 to 15); wrap lets it run
  // off one side and on at the other instead of into the wall.

  return padGame ( 'snake', 'Snake', [
    'size'  => padGameInt ( 'size', 18, 10, 30 ),
    'speed' => padGameInt ( 'speed', 6, 2, 15 ),
    'wrap'  => padGameFlag ( 'wrap' )
  ] );

?>
