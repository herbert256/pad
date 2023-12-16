<?php

  $padXrefId++;

  $padXrefEvent ['event'] = $padXrefEventType;
  $padXrefEvent ['tree']  = $padXrefLevel [$pad];
  $padXrefEvent ['occur'] = $padOccur     [$pad];

  $padXrefEvents [] = $padXrefEvent;

?>