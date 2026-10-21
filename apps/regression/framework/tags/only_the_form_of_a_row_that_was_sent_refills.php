<?php

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'rename', 'padFormAt' => '2', 'id' => '2', 'title' => '' ];

  padValidate ( [ 'title' => 'required' ] );

  $rows = [ [ 'id' => 1, 'title' => 'One' ], [ 'id' => 2, 'title' => 'Two' ], [ 'id' => 3, 'title' => 'Three' ] ];

?>
