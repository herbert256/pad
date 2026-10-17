<?php

  // A {poll} inside a {nocache} part renders on every request, the hit included, so the
  // section round it is kept: the second section of the name is a hit - the stored text,
  // the poll made again from its source.

  padFragmentForget ( 'fwPollNocache' );

?>
