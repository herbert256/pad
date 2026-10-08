<?php

  $clicks = (int) padLiveValue ();

  if ( padLiveEvent () == 'add'   ) $clicks++;
  if ( padLiveEvent () == 'reset' ) $clicks = 0;

?>
