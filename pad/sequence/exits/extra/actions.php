<?php

  // Extra columns: gives each row one field per applied action, holding what $pqResult looked
  // like at that position right after that action ran - so a chain of actions can be followed
  // term by term. Reads $pqActionsHit, snapshotted per action by actions/actions.php; 'n/a'
  // where an action returned fewer terms than the final result has rows.

  if ( ! count ( $pqActionsHit ) )
    return;

  foreach ( $padData [$pad] as $padK => $padV )
    foreach ( $pqActionsHit as $pqAction => $pqActionResult )
      if ( isset ( $pqActionResult [$padK] ) )
        $padData [$pad] [$padK] [$pqAction] = $pqActionResult [$padK];
      else
        $padData [$pad] [$padK] [$pqAction] = 'n/a';

?>