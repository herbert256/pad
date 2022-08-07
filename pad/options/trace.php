<?php
      
  $pWalk[$p]_save = $pWalk[$p];
  $pWalk[$p] = 'end';
  include PAD . 'tags/trace.php';
  $pWalk[$p] = $pWalk[$p]_save;

?>