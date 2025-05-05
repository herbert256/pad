<?php

  $pqBuildSave = $pqBuild;
  $pqPlay      = $pqBuild;
  $pqBuild     = 'bool';

  include "sequence/plays/one.php";

  $pqBuild = $pqBuildSave;

  return $pq;

?>