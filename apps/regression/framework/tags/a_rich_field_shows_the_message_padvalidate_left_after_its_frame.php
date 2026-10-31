<?php

  // The page's own check of the post: the field shows its message after its frame, worded
  // with its label, tied to the number by aria-invalid and aria-describedby.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'guests' => '' ];

  padValidate ( [ 'guests' => 'required|integer' ] );

?>
