<?php

  // Tag entry for {resume}: transform the last pushed sequence in place. Reached from
  // tags/resume.php; notes the entry point for {info} in $pqSetStart and, unlike its
  // siblings, runs sequence/resume.php, which writes back to the store instead of iterating.

  $pqSetStart = __FILE__;

  return include PQ . "sequence/resume.php";

?>