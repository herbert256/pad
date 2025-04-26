<?php

        if ( $pqPullName                     ) $pqPull = $pqPullName;
    elseif ( $pqTag  == 'pull' and ! $pqPull ) $pqPull = $padLastPush;
    elseif ( $pqType == 'pull' and ! $pqPull ) $pqPull = $padLastPush;

?>