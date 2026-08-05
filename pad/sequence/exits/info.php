<?php

  // Builds the sequence info block shown in debug output. Gathers where the run was entered
  // from, which actions, options and plays it used into $pqInfo, then hands that to
  // events/sequence.php, which feeds the xref and the trace.
  //
  // Does nothing unless $padInfo is on. Also included directly by the non-tag entry points
  // in sequence/ (sequence.php, action.php, pull.php, resume.php).

  if ( ! $padInfo )
    return;

  include PQ . 'exits/info/start.php';
  include PQ . 'exits/info/actions.php';
  include PQ . 'exits/info/options.php';
  include PQ . 'exits/info/plays.php';

  include PAD . 'events/sequence.php';

?>