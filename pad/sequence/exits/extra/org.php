<?php

  // Extra column: gives each row a field named after the sequence type ($pqOrgName) holding
  // the term as generated, before the {make}/{keep}/{remove}/{flag} plays reshaped it.
  // Reads $pqOrgHit, filled per accepted term by build/one.php, at the term the row is
  // ($pqRowAt); a row that is no term - an action's total - keeps the value it was given
  // under that name, else 'n/a'.

  foreach ( $padData [$pad] as $padK => $padV )
    if ( $pqRowAt [$padK] !== FALSE and isset ( $pqOrgHit [ $pqRowAt [$padK] ] ) )
      $padData [$pad] [$padK] [$pqOrgName] = $pqOrgHit [ $pqRowAt [$padK] ];
    elseif ( $pqRowAt [$padK] !== FALSE or ! isset ( $padV [$pqOrgName] ) )
      $padData [$pad] [$padK] [$pqOrgName] = 'n/a';

?>
