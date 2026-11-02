<?php

  // A click in the live region posts its event alone: the multiselect in the region was not
  // sent, so it shows value= - a and c - as the text field beside it shows kept; read as
  // emptied, every click took its choices away.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padLive' => 'box', 'padEvent' => 'add', 'padValue' => '' ];

?>
