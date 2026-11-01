<?php

  // The page's own check of the post names the list as padValidate names one - interests[] -
  // and the field shows its message after its frame, worded with its label, tied to the
  // select by aria-invalid and aria-describedby under the id without the brackets.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'interests' => [ 'PHP', 'Nope' ] ];

  padValidate ( [ 'interests[]' => 'required|in:PHP,Maps' ] );

?>
