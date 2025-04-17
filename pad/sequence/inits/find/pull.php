<?php

  if ( $padSeqPull )
    return;

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( $padPrmKind <> 'option' or in_array ( $padPrmName, $padSeqDone ) )
      continue;

    if ( isset ( $padSeqStore [$padPrmName] ) ) {

      $padSeqDone [] = $padPrmName;
      $padSeqPull = $padPrmName;
      
      return;
    
    }

  }

      if ( $padSeqPullName                                ) $padSeqPull = $padSeqPullName;
  elseif ( $padSeqFindType == 'continue' and $padLastPush ) $padSeqPull = $padLastPush;
  elseif ( $padSeqFindType == 'pull'     and $padLastPush ) $padSeqPull = $padLastPush;

?>