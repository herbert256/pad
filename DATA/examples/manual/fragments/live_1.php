<?php

  $count = (int) padLiveValue ();

  if ( padLiveEvent () == 'add'   ) $count++;
  if ( padLiveEvent () == 'reset' ) $count = 0;

?>
