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

  include PQ . 'inits/parms.php';
  include PQ . 'inits/set.php';

?>
