<?php

  $padInfXrefId++;

  $padInfXrefEvent ['event'] = $padInfXrefEventType;
  $padInfXrefEvent ['tree']  = $padInfXrefLevel [$pad];
  $padInfXrefEvent ['occur'] = $padOccur     [$pad];

  $padInfXrefEvents [] = $padInfXrefEvent;

?>