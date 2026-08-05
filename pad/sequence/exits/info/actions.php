<?php

  // Info block: lists the actions this run applied, split by kind - actions/single/ ones work
  // on the sequence alone, actions/double/ ones combine it with a second sequence or store.
  // Reads $pqActions, appends to $pqInfo['actions/single'] and $pqInfo['actions/double'].

  foreach ( $pqActions as $pqAction => $pqActionParm ) {

    if ( file_exists ( PQ . "actions/single/$pqAction" ) )
      $pqInfo ['actions/single'] [] = $pqAction;
    elseif ( file_exists ( PQ . "actions/double/$pqAction" ) )
      $pqInfo ['actions/double'] [] = $pqAction;

  }

?>