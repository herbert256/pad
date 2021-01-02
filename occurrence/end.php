<?php

  if ($pad_name == 'trace')
    $pad_trace = FALSE;

  foreach ($pad_parms_occur_end as $pad_v)
    if ( isset ( $pad_parms_tag [$pad_v] ) )
        include PAD_HOME . "parms/$pad_v.php" ;

  if ($pad_trace)
    pad_trace ("occur/end", "nr=$pad_occur_cnt html=" . $pad_html [$pad_lvl]);

  $pad_result [$pad_lvl] .= $pad_html[$pad_lvl];

  pad_reset ($pad_lvl, $pad_lvl);

?>