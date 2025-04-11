<?php

  include 'sequence/inits/check/tag.php';

  $padSeqCheck = 'keep';   include "sequence/inits/check/go.php";
  $padSeqCheck = 'remove'; include "sequence/inits/check/go.php";
  $padSeqCheck = 'flag';   include "sequence/inits/check/go.php";

?>