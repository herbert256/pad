<?php

  // record writes what the page answers into a covering suite's store, and the comparator
  // reads the run's own host as localhost only in what comes back - so the store must hold
  // it as localhost too, or an answer recorded on http://127.0.0.1:8080/pad/ never matches
  // again, on that host or any other.

  $keep    = $padHost;
  $padHost = 'http://127.0.0.1:8080/pad/';
  $stored  = getSuiteRecordBody ( "  see http://127.0.0.1:8080/pad/demo/?x\n" );
  $padHost = $keep;

?>
