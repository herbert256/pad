<?php

  // A section holding a {poll} is rendered every time and never kept: the poll is the
  // visitor's own - its form, or the results once voted - and on a hit its handler did not
  // run, so the session was not marked as shown the form and the vote from it was refused.
  // The second section of the name finds no stored copy and renders its own content.

  padFragmentForget ( 'fwPollSection' );

?>
