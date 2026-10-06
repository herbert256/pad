<?php

  // An error hook that fails itself, the way one that reports to a tracker fails when the
  // tracker is down: what it raises is its own failure, logged and set aside.

  padError ( 'the error hook could not reach its tracker' );

?>
