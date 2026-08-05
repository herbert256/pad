<?php

  // Extra column: gives each row a field named after the sequence type ($pqOrgName) holding
  // the term as generated, before the {make}/{keep}/{remove}/{flag} plays reshaped it.
  // Reads $pqOrgHit, filled per accepted term by build/one.php; 'n/a' where it has no entry.

  foreach ( $padData [$pad] as $padK => $padV )
    if ( isset ( $pqOrgHit [$padK] ) )
      $padData [$pad] [$padK] [$pqOrgName] = $pqOrgHit [$padK];
    else
      $padData [$pad] [$padK] [$pqOrgName] = 'n/a';

?>