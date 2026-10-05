<?php

  // The two trees are one entry of the option form, so the second is a hit: its handler
  // does not run and never reads children=. vary= keeps a run off an earlier run's copy.

  $token = uniqid ( '', TRUE );

?>
