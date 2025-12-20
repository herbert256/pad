<?php

  if ( ! $pqToData )
    return;

  if ( $pqPull and ( isset ($pqPop) or isset ($pqShift) ) )
    $padDataStore [$pqToData] = padData ( $pqStore [$pqPull], '', $pqToData );
  else
    $padDataStore [$pqToData] = padData ( $pqResult,              '', $pqToData );

?>
