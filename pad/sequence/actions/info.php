<?php

  if ( file_exists ( "sequence/actions/single/$pqAction" ) )
    $pqInfo ['actions/single'] [] = $pqAction;
  elseif ( file_exists ( "sequence/actions/double/$pqAction" ) )
    $pqInfo ['actions/double'] [] = $pqAction;

?>