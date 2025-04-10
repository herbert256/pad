<?php

  $padSeqNames [] = 'sequence';
  $padSeqNames [] = $padSeqName; 
  $padSeqNames [] = $padSeqToData;
  $padSeqNames [] = $padSeqSeq;
  $padSeqNames [] = $padName [$pad]; 
  $padSeqNames [] = $padTag [$pad];
  $padSeqNames [] = $padSeqPull;
  $padSeqNames [] = $padSeqPush;

  $padSeqNames = array_unique ( $padSeqNames );

  foreach ( $padSeqResult as $padSeqValue ) {

     $padSeqRecord = [];

     foreach ( $padSeqNames as $padSeqName )
       if ( $padSeqName and $padSeqName !== TRUE )
         $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 


?>