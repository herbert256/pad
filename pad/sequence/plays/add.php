<?php

  // Registers one play on $pqPlays - a sequence used as a filter rather than as a source.
  //
  // Called from plays/inits.php and inits/find/add.php with the sequence name in
  // $padPrmName, its parameter in $padPrmValue and the kind of play ({make}, {keep},
  // {remove} or {flag}) in $pqPlay. Resolves the play's own build strategy with pqBuild(),
  // loads that type's helper files, lets the type initialise itself, and appends the
  // sequence/parm/build/play record that plays/plays.php replays per candidate. The main
  // sequence's $pqSeq and $pqBuild are saved and restored around all of that.

  $pqSave1 = $pqSeq;
  $pqSave2 = $pqBuild;

  $pqSeq   = $padPrmName;
  $pqBuild = pqBuild ( $pqSeq, $pqPlay );

  include PQ . 'build/include.php';
  include PQ . "plays/init.php";

  $pqPlays [] = [
    'pqSeq'   => $pqSeq,
    'pqParm'  => $padPrmValue,
    'pqBuild' => $pqBuild,
    'pqPlay'  => $pqPlay
  ];

  if ( ( $padK = array_search ( $pqSeq, $padDone ) ) !== false )
    unset ( $padDone [$padK] );

  $pqSeq   = $pqSave1;
  $pqBuild = $pqSave2;

?>