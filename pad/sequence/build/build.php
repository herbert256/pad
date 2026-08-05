<?php

  // Runs the build phase: turns the resolved sequence into its list of terms in $pqResult.
  //
  // Called from the sequence entry points (sequence/sequence.php and friends) once the
  // parameters, plays and actions are set up. Applies a build= override, prepares the
  // per-build globals, loads the sequence type's helper files, then dispatches to
  // build/types/$pqBuild.php - one file per build strategy. The randomly/build pair
  // brackets that dispatch so a randomly= request against a computed strategy first
  // generates the terms in order and then samples them.

  include PQ . 'build/given.php';
  include PQ . 'build/vars.php';
  include PQ . "build/include.php";
  include PQ . "build/randomly/build/inits.php";
  include PQ . "build/types/$pqBuild.php";
  include PQ . "build/randomly/build/exits.php";

?>