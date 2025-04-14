<?php

  if ( $padSeqContinue )
    return;
  
  if     ( ! $padSeqNameGiven and ! $padSeqPull and ! $padSeqPush        ) $padSeqStoreName = 'default';
  elseif ( ! $padSeqNameGiven and ! $padSeqPull and $padSeqPush === TRUE ) $padSeqStoreName = 'default';
  elseif ( $padSeqNameGiven                                              ) $padSeqStoreName = $padSeqNameGiven;
  elseif ( $padSeqPush and $padSeqPush !== TRUE                          ) $padSeqStoreName = $padSeqPush; 
  elseif ( $padSeqPull and $padSeqPull !== TRUE                          ) $padSeqStoreName = $padSeqPull;
  else                                                                     $padSeqStoreName = $padSeqName;

  if ( $padSeqPush )
    if ( $padSeqPush === TRUE )
      if ( $padSeqPull )        $padLastPush = $padSeqPull;
      else                      $padLastPush = $padSeqStoreName;
    else                        $padLastPush = $padSeqPush;
  elseif ( $padSeqPull )        $padLastPush = $padSeqPull;
  else                          $padLastPush = $padSeqStoreName;

?>