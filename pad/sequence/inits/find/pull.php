<?php

      if ( $padSeqPullName                                ) $padSeqPull = $padSeqPullName;
  elseif ( $padSeqFindType == 'continue' and $padLastPush ) $padSeqPull = $padLastPush;
  elseif ( $padSeqFindType == 'pull'     and $padLastPush ) $padSeqPull = $padLastPush;

?>