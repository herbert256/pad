<?php
  
  if     (   $padSeqBuildName ) $padSeqBuild = $padSeqBuildName; 
  elseif ( ! $padSeqBuild     ) $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );

?>