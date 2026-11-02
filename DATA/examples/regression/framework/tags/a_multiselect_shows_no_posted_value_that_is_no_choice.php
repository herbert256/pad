<?php

  // A posted value that is no choice of the field - a hand-made post - is neither chosen nor
  // added as an option: the select posts only its own options, and in: refuses the rest. An
  // item that is a list is left out too, and the choice posted beside them is chosen.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'interests' => [ 'Nope', [ 'PHP' ], 'Maps', '<b>' ] ];

?>
