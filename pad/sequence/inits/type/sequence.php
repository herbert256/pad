<?php

   // {sequence:power 5}

   if ( isset ( $padSeqStore [$padPrm1] ) ) {
     $padSeqPull  = $padPrm1;
     $padPrmValue = $padPrm2;
     return include 'sequence/inits/type/make.php';
   }

   if ( isset ( $padSeqStore [$padPrm2] ) ) {
     $padSeqPull  = $padPrm2;
     $padPrmValue = $padPrm1;
     return include 'sequence/inits/type/make.php';
   }

  $padSeqSeq  = $padSeqTag;
  $padSeqParm = $padParm;

  include 'sequence/inits/go/sequence.php';

?>