<?php

  // The post the live script sends for a change of a multiselect of which a and c are chosen:
  // every choice as the form posts it, padLists[] naming the list, padValue the choices
  // joined by ', '. The region comes back with both chosen, and padLiveValue () is a, c.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'tags' => [ 'a', 'c' ], 'padLists' => [ 'tags[]' ], 'padEvent' => 'pick', 'padValue' => 'a, c', 'padLive' => 'box' ];

  $picked = padLiveEvent () . ': ' . padLiveValue ();

?>
