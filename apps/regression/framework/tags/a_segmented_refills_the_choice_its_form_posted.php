<?php

  // A post of form f: its size shows the choice posted in place of value=, the size of form g,
  // which did not come back, shows value=; tone came back without a choice - a radio group
  // with none is left out of the post, the padLists[] beside it says it was there - so none
  // is chosen, not value= again; and view was posted a value that is no choice of it, which
  // is not shown.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'size' => 'L', 'view' => 'Map', 'padLists' => [ 'size', 'tone', 'view' ] ];

?>
