<?php

  // A post of form search, which holds no multiselect: the one in the form without a name and
  // the one outside any form were not sent - their padLists[] is not in the post - so they
  // show value=, as the text field beside them does; read as emptied, they lost it.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'search', 'q' => 'pad' ];

?>
