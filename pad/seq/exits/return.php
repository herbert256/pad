<?php

  $padSeqReturn = [];

  $padSeqNames [] = 'sequence';
  $padSeqNames [] = $padSeqName; 
  $padSeqNames [] = $padSeqToData;
  $padSeqNames [] = $padSeqSeq;
  $padSeqNames [] = $padSeqName; 
  $padSeqNames [] = $padName [$pad]; 
  $padSeqNames [] = $padTag [$pad];
  $padSeqNames [] = $padSeqPull;

  $padSeqNames = array_unique ( $padSeqNames );

  foreach ($padSeqResult as $padSeqValue  ) {

     $padSeqRecord = [];

     foreach ( $padSeqNames as $padSeqName )
       if ( $padSeqName )
         $padSeqRecord [$padSeqName] = $padSeqValue;

     $padSeqReturn [] = $padSeqRecord; 

  } 


?>