<?php
  
  if ( ! $padTrace )
    return;

  padFilePutContents ( $padLevelDir [$pad] . "/level.json", padTraceGetLevel($pad) ); 
  padFilePutContents ( $padLevelDir [$pad] . "/base.html",  $padBase [$pad]     );
  padFilePutContents ( $padLevelDir [$pad] . "/data.json",  $padData [$pad]     );
  
?>