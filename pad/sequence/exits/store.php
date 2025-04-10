<?php

  if ( $padPair [$pad] and ! $padSeqPush )
    return; 

  if     ( ! $padSeqNameGiven and ! $padSeqPull and ! $padSeqPush ) $padSeqStoreName = 'sequence';
  elseif ( $padSeqNameGiven                                       ) $padSeqStoreName = $padSeqNameGiven;
  elseif ( $padSeqPush and $padSeqPush !== TRUE                   ) $padSeqStoreName = $padSeqPush; 
  elseif ( $padSeqPull and $padSeqPull !== TRUE                   ) $padSeqStoreName = $padSeqPull;
  else                                                              $padSeqStoreName = $padSeqName;

  if ( $padSeqPush )
    if ( $padSeqPush === TRUE )
      if ( $padSeqPull )        $padLastPush = $padSeqPull;
      else                      $padLastPush = $padSeqStoreName;
    else                        $padLastPush = $padSeqPush;
  elseif ( $padSeqPull )        $padLastPush = $padSeqPull;
  else                          $padLastPush = $padSeqStoreName;

  if ( file_exists ( "sequence/types/$padLastPush") )
    padError ( "Store name '$padLastPush' can not be equal to a Sequence name" );

  if ( file_exists ( "sequence/options/types/$padLastPush.php") )
    padError ( "Store name '$padLastPush' can not be equal to a Sequence option name" );

  if ( file_exists ( "sequence/actions/types/$padLastPush.php") )
    padError ( "Store name '$padLastPush' can not be equal to an Action name" );
  
  if ( file_exists ( "options/$padLastPush.php") )
    padError ( "Store name '$padLastPush' can not be equal to a PAD option name" );

  if ( $padSeqStoreUpdated ) $padSeqStore [$padLastPush] = $padSeqStore [$padSeqPull];
  else                       $padSeqStore [$padLastPush] = array_values ( $padSeqResult );

?>
