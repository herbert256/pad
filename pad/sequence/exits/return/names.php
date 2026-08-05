<?php

  // Default return shape, used when no name= was given: every term becomes a row carrying
  // the same value under each name the sequence answers to - the generic 'sequence', the
  // resolved $pqName, the toData name and the push name - so {$sequence}, {$fibonacci} and
  // {$myStore} all reach it. Reads $pqResult, fills $padData[$pad].
  //
  // The inner loop reuses $pqName as its variable, leaving it holding the last name; nothing
  // downstream of the return stage reads it again.

  $pqNames [] = 'sequence';
  $pqNames [] = $pqName;
  $pqNames [] = $pqToData;
  $pqNames [] = $pqPush;

  $pqNames = array_unique ( $pqNames );

  foreach ( $pqResult as $pqValue ) {

     $pqRecord = [];

     foreach ( $pqNames as $pqName )
       if ( $pqName and $pqName !== TRUE )
         $pqRecord [$pqName] = $pqValue;

     $padData [$pad] [] = $pqRecord;

  }

?>