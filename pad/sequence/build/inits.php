<?php
  
      if ( $pqPull    ) $pqBuild = 'pull';
  elseif ( ! $pqBuild ) $pqBuild = pqBuild ( $pqSeq, 'loop' );

?>