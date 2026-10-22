<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'rename', 'padFormAt' => 'tags/only_the_form_of_a_row_that_was_sent_refills:2', 'id' => '2', 'title' => '' ];

  padValidate ( [ 'title' => 'required' ] );

  $rows = [ [ 'id' => 1, 'title' => 'One' ], [ 'id' => 2, 'title' => 'Two' ], [ 'id' => 3, 'title' => 'Three' ] ];

?>
