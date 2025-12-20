<?php

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    if ( file_exists ( PQ . "actions/single/$pqAction" ) )
      $pqInfo ['actions/single'] [] = $pqAction;
    elseif ( file_exists ( PQ . "actions/double/$pqAction" ) )
      $pqInfo ['actions/double'] [] = $pqAction;

  }

?>