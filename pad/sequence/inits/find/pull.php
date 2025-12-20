<?php

    if     ( $pqTag  == 'pull' and ! $pqPull ) $pqPull = $padLastPush;
    elseif ( $pqType == 'pull' and ! $pqPull ) $pqPull = $padLastPush;

?>
