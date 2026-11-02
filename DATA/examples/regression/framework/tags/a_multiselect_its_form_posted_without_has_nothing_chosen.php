<?php

  // The browser leaves a select with nothing chosen out of the post: form f came back without
  // the field, its padLists[] naming it as the browser posts it, so nothing is chosen in it -
  // value= is what it shows before a post, and shown again it undid the emptying.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'padLists' => [ 'interests[]' ], 'name' => 'Ann' ];

?>
