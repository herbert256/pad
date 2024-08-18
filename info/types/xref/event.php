<?php

  $padInfoXrefId++;

  $padInfoXrefEvent ['event'] = $padInfoXrefEventType;
  $padInfoXrefEvent ['tree']  = $padInfoXrefLevel       [$pad];
  $padInfoXrefEvent ['occur'] = $padOccur               [$pad];

  $padInfoXrefEvents [] = $padInfoXrefEvent;

?>