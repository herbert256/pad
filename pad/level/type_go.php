<?php

  $pParms [$pad] ['tag_cnt']++;

  if ( $pTrace_tag )
    include 'trace/tag/before.php';

  $pTag_content = ''; ob_start();

  $pad_walk = $pad_walks [$pad]; 

  foreach ( $pParms [$pad] as $pK => $pad_v )
    $GLOBALS['pad_'.$pK] = $pad_v;

  pTiming_start ('tag');
  $pTag_result = include PAD . "types/$pType.php";
  pTiming_end ('tag');

  foreach ( $pParms [$pad] as $pK => $pad_v )
    $pParms [$pad] [$pK] = $GLOBALS['pad_'.$pK];

  $pad_walks [$pad] = $pad_walk; 

  $pTag_content .= ob_get_clean();

  if ( $pTrace_tag )
    include 'trace/tag/after.php';

  if ( is_object   ( $pTag_result ) ) $pTag_result = pXxx_to_array ( $pTag_result );
  if ( is_resource ( $pTag_result ) ) $pTag_result = pXxx_to_array ( $pTag_result );

  if ( $pTag_result === TRUE AND $pTag_content <> '' )
    $pTag_result = $pTag_content;

  if ( is_scalar($pTag_result) and strpos($pTag_result , '@content@') !== FALSE )
    $pTag_result = str_replace('@content@', $pTrue [$pad], $pTag_result);

?>