<?php

  // Declares and zeroes everything one sequence run accumulates into, then reads its
  // parameters.
  //
  // $pqResult is the values being collected and $pqTries the attempts spent on them;
  // $pqSeq / $pqBuild / $pqParm say what is being generated and by which strategy, with
  // $pqCheckPlay carrying which of keep, remove and flag a 'check' build stands for; $pqPlays
  // holds the filters and $pqActions the transformations, each with a matching *Hit array
  // recording what actually fired; $pqDone lists option names already claimed so later passes
  // skip them; $pqInfo collects the {info} report.
  //
  // inits/parms.php then reads the run's parameters and inits/set.php derives the rest.

  $pqFixed        = FALSE;
  $pqStoreUpdated = FALSE;
  $pqStored       = FALSE;

  $pqTries      = 0;
  $pqLoop       = 0;

  $pqSeq        = '';
  $pqBuild      = '';
  $pqCheckPlay  = 'keep';
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
  $pqActionsHit = [];
  $pqOrgHit     = [];

  include PQ . 'inits/parms.php';
  include PQ . 'inits/set.php';

?>