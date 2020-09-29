<?php

  if ($pad_name == 'trace')  {
    $pad_trace = FALSE;
  }

  if ($pad_trace)
    pad_trace ("occur/end", "nr=$pad_occur_cnt html=" . $pad_html [$pad_lvl]);

  $pad_result [$pad_lvl] .= $pad_html[$pad_lvl];

  if ( isset($pad_parms_tag ['callback']) and count($pad_data [$pad_lvl]) ) {
    $pad_callback = "exit_occurrence";
    include PAD_HOME . 'level/callback.php';
  }

  pad_reset ($pad_lvl, $pad_lvl);

  $pad_save_vars [$pad_lvl] = $pad_delete_vars [$pad_lvl] = [];

?>