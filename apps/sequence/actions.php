<?php

  echo "{common:table}";
  echo "{common:demo}{sequence:range '11..20', push='mySeq1'/}{/common:demo}";
  echo str_pad ( "{common:demo}{pull:mySeq1}", 36) . "{\$sequence} {/pull:mySeq1} {/common:demo}";

  foreach ( actions () as $action ) {

        if ( in_array ( $action, ['slice','splice','pop','shift'] )  ) continue;
    elseif ( file_exists ( PAD . "sequence/actions/double/$action" ) ) continue;
    elseif ( file_exists ( PAD . "sequence/actions/parm/$action" )   ) $extra = "=3";
    else                                                      $extra = '';

    echo str_pad ( "{common:demo}{pull:mySeq1 $action$extra}", 36) . "{\$sequence} {/pull:mySeq1} {/common:demo}";

  }

  echo "{/common:table}{common:table}";

  echo "{common:demo}{sequence:range '11..20', push='mySeq1'/}{/common:demo}";
  echo str_pad ( "{common:demo}{pull:mySeq1}", 36) . "{\$sequence} {/pull:mySeq1} {/common:demo}";

  echo str_pad ( "{common:demo}{pull:mySeq1 slice='3|5'}", 36 )         . "{\$sequence} {/pull:mySeq1} {/common:demo}";
  echo str_pad ( "{common:demo}{pull:mySeq1 shift=2}", 36 )            . "{\$sequence} {/pull:mySeq1} {/common:demo}";
  echo str_pad ( "{common:demo}{pull:mySeq1 shift=2}", 36 )            . "{\$sequence} {/pull:mySeq1} {/common:demo}";
  echo str_pad ( "{common:demo}{pull:mySeq1 pop=2}", 36   )            . "{\$sequence} {/pull:mySeq1} {/common:demo}";
  echo str_pad ( "{common:demo}{pull:mySeq1 pop=2}", 36   )            . "{\$sequence} {/pull:mySeq1} {/common:demo}";

  echo "{/common:table}{common:table}";

  echo "{common:demo}{sequence:range '11..20', push='mySeq1'/}{/common:demo}";
  echo "{common:demo}{sequence:range 'A..E',   push='mySeq2'/}{/common:demo}";
  echo "{common:demo}{pull:mySeq1 splice='3|5|mySeq2'} {\$sequence} {/pull:mySeq1} {/common:demo}";
  echo "{/common:table}";

?>
