<?php

  $pqFixed        = FALSE;
  $pqStoreUpdated = FALSE;
  $pqStored       = FALSE;

  $pqTries      = 0;
  $pqLoop       = 0;
  
  $pqSeq        = '';
  $pqBuild      = '';
  $pqParm       = '';
  $pqAction     = '';
  $pqActionParm = '';
  $pqOrgName    = '';
  $pqOrgSet     = '';

  $pqResult     = [];
  $pqDone       = [];
  $pqInfo       = [];  
  $pqNames      = [];
  $pqActions    = [];
  $pqPlays      = [];
  $pqPlaysHit   = [];
  $pqActions    = [];
  $pqActionsHit = [];
  $pqOrgHit     = [];

  include 'sequence/inits/parms.php';
  include 'sequence/inits/set.php';

?>