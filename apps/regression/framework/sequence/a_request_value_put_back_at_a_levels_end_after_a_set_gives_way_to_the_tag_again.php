<?php

  // The visitor sends ?prime=<b>evil</b>. A level variable of the same name puts the
  // request's value aside and back at the level's end, and a {set} inside the level changes
  // the name for that level only: the value put back is the request's again, so {prime} is
  // the sequence type, never the visitor's text written raw in its place.

  padGetParms ( 'GET', [ 'prime' => '<b>evil</b>' ] );

?>
