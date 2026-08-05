<?php

  // Type handler for a sequence action ({action:sum}, {mySeq:sum}): delegates to the sequence
  // subsystem, which runs the action found in PQ/actions/types/.

  return include PQ . 'start/types/action.php';

?>