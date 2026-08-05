<?php

  // Makes a {pull} with no name of its own - as tag or as type - resume the store that was
  // pushed last, $padLastPush.

    if     ( $pqTag  == 'pull' and ! $pqPull ) $pqPull = $padLastPush;
    elseif ( $pqType == 'pull' and ! $pqPull ) $pqPull = $padLastPush;

?>