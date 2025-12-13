<?php

  if ( ! $padInfo ) 
    return;

  include PQ . 'exits/info/start.php';
  include PQ . 'exits/info/actions.php';
  include PQ . 'exits/info/options.php';
  include PQ . 'exits/info/plays.php';

  include PAD . 'events/sequence.php';

?>