<?php

  $pqNames [] = 'sequence';

  $pqNames [] = $pqName; 
  $pqNames [] = $pqToData;
  $pqNames [] = $pqSeq;
  $pqNames [] = $pqPull;
  $pqNames [] = $pqPush;

  $pqNames [] = $padName [$pad]; 
  $pqNames [] = $padTag [$pad];

  $pqNames = array_unique ( $pqNames );

  foreach ( $pqResult as $pqValue ) {

     $pqRecord = [];

     foreach ( $pqNames as $pqName )
       if ( $pqName and $pqName !== TRUE )
         $pqRecord [$pqName] = $pqValue;

     $padData [$pad] [] = $pqRecord; 

  } 

?>