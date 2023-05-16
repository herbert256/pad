<?php

  $padIncludeSet     = $padInclude;
  $padBuildModeSet   = $padBuildMode;
  $padBuildMergeSet  = $padBuildMerge; 

  include pad . 'build/build.php'; 

  $padData [$pad]     = [];
  $padData [$pad] [1] = [];

  foreach ( get_defined_vars() as $padK => $padV )
    if ( padValidStore($padK) )
      $padData [$pad] [1] [$padK] = $padV;

  if ( count ( $padData [$pad] [1] ) ) {
    $padKey     [$pad] = 1;
    $padCurrent [$pad] = $padData [$pad] [1];
  } else {
    $padData    [$pad] = padDefaultData ();
    $padKey     [$pad] = 999;
    $padCurrent [$pad] = $padData [$pad] [999];
  }

  unset ( $padIncludeSet    );
  unset ( $padBuildModeSet  );
  unset ( $padBuildMergeSet ); 

?>