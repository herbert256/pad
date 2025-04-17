<?php

  $pqNames [] = 'sequence';
  $pqNames [] = $pqName; 
  $pqNames [] = $pqToData;
  $pqNames [] = $pqSeq;
  $pqNames [] = $padName [$pad]; 
  $pqNames [] = $padTag [$pad];
  $pqNames [] = $pqPull;
  $pqNames [] = $pqPush;

  $pqNames = array_unique ( $pqNames );

  foreach ( $pqResult as $pqValue ) {

     $pqRecord = [];

     foreach ( $pqNames as $pqName )
       if ( $pqName and $pqName !== TRUE )
         $pqRecord [$pqName] = $pqValue;

     $pqReturn [] = $pqRecord; 

  } 


?>