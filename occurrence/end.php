<?php

  if ($pad_name == 'trace')
    $pad_trace = FALSE;

  $pad_options = 'occur_end';
  include PAD_HOME . "level/options.php";

  if ( $pad_walks [$pad_lvl] == 'occurrence-end' )
    include PAD_HOME . "walk/occurrence-end.php";

  if ($pad_trace)
    pad_trace ("occur/end", "nr=$pad_occur_cnt html=" . $pad_html [$pad_lvl]);

  $pad_result [$pad_lvl] .= $pad_html [$pad_lvl];

  pad_reset ($pad_lvl, $pad_lvl);

?>