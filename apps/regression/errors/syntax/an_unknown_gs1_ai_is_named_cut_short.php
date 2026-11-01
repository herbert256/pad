<?php

  // An AI of three thousand characters, a control character and a line break among them, is
  // no GS1 application identifier: the message names it as the other messages of the GS1
  // element strings name a text - its control characters spaces, cut to ten - where it carried
  // the AI whole, 3035 characters, the control character and the line break with it.

  $elements = "(ab\x01\n" . str_repeat ( 'x', 3000 ) . ')1';

?>
