<?php

  // A post of form f: its multiselect shows the list posted - interests[] is $_POST
  // ['interests'] - in place of value=; the field of the same name in form g, which did not
  // come back, shows value=.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'interests' => [ 'Music', 'PHP' ] ];

?>
