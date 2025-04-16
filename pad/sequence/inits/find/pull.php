<?php

  if ( $padSeqPull )
    return $padSeqPull;

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( $padPrmKind <> 'option' or in_array ( $padPrmName, $padSeqDone ) )
      continue;

    if ( isset ( $padSeqStore [$padPrmName] ) ) {
      $padSeqDone [] = $padPrmName;
      return $padPrmName;
    }

  }

  if ( $padSeqPullName ) 
    return $padSeqPullName;

  if ( $padSeqFindType == 'continue' and $padLastPush ) return $padLastPush;
  if ( $padSeqFindType == 'pull'     and $padLastPush ) return $padLastPush;

  return '';

?>