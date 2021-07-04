<?php

  $pad_trace_dir_occ = "$pad_trace_dir_lvl/occ-" . $pad_occur [$pad_lvl]++;
  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  $pad_json = [ $pad_key [$pad_lvl] => $pad_current [$pad_lvl] ] ;

  pad_file_put_contents ("$pad_trace_dir_occ/_base.html", $pad_base[$pad_lvl]                 );
  pad_file_put_contents ("$pad_trace_dir_occ/_data.json", pad_json ($pad_current [$pad_lvl] ) );

  pad_trace ("occur/start", "nr=$pad_occur_cnt key=" . $pad_key [$pad_lvl] . ' html=' . $pad_html[$pad_lvl]);

?>