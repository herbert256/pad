<?php

  // The thing a sandbox case cannot be: a page whose .php half leaves variables for its .pad
  // half. A nested pass has a scope of its own, so a case has to state its data inside the
  // template; here the two files pair the way every PAD page does.

  $greeting = 'Hello';
  $items    = [ 'a', 'b', 'c' ];

?>
