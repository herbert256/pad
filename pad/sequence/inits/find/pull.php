<?php

      if ( $pqPullName                                ) $pqPull = $pqPullName;
  elseif ( $pqFindType == 'continue' and $padLastPush ) $pqPull = $padLastPush;
  elseif ( $pqFindType == 'pull'     and $padLastPush ) $pqPull = $padLastPush;

?>