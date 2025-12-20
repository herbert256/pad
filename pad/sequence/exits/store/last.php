<?php

  if     ( ! $pqNameGiven and ! $pqPull and ! $pqPush        ) $pqStoreName = 'default';
  elseif ( ! $pqNameGiven and ! $pqPull and $pqPush === TRUE ) $pqStoreName = 'default';
  elseif ( $pqNameGiven                                      ) $pqStoreName = $pqNameGiven;
  elseif ( $pqPush and $pqPush !== TRUE                      ) $pqStoreName = $pqPush;
  elseif ( $pqPull and $pqPull !== TRUE                      ) $pqStoreName = $pqPull;
  else                                                         $pqStoreName = $pqName;

  if ( $pqPush )
    if ( $pqPush === TRUE )
      if ( $pqPull )    $padLastPush = $pqPull;
      else              $padLastPush = $pqStoreName;
    else                $padLastPush = $pqPush;
  elseif ( $pqPull )    $padLastPush = $pqPull;
  else                  $padLastPush = $pqStoreName;

?>
