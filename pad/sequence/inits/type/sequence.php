<?php

   // {sequence:power 5}

   if ( isset ( $padSeqStore [$padSeqPrm1] ) ) {
     $padSeqPull  = $padSeqPrm1;
     $padPrmValue = $padSeqPrm2;
     return include 'sequence/inits/type/make.php';
   }

   if ( isset ( $padSeqStore [$padSeqPrm2] ) ) {
     $padSeqPull  = $padSeqPrm2;
     $padPrmValue = $padSeqPrm1;
     return include 'sequence/inits/type/make.php';
   }

  $padSeqSeq  = $padSeqTag;
  $padSeqParm = $padParm;

  include 'sequence/inits/go/sequence.php';

?>