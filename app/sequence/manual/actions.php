<?php
 
  echo "{table}";
  echo "{demo}{sequence '11..20', push='mySeq1'}{/demo}";
  echo "{demo}{sequence '16..25', push='mySeq2'}{/demo}";
  echo "{demo}{sequence 'A..E',   push='mySeq3'}{/demo}";
  echo "{/table}{table}";

  foreach ( pqActions () as $action ) {

        if ( in_array ( $action, ['slice','splice','pop','shift'] ) ) continue;
    elseif ( file_exists ( "sequence/actions/double/$action" )      ) continue;
    elseif ( file_exists ( "sequence/actions/parm/$action" )        ) $extra = "=3";
    else                                                              $extra = '';

    echo str_pad ( "{demo}{mySeq1 $action$extra}", 36) . "{\$sequence} {/mySeq1} {/demo}";

  }

  echo str_pad ( "{demo}{mySeq1 slice='3|5'}", 36 )         . "{\$sequence} {/mySeq1} {/demo}";
  echo str_pad ( "{demo}{mySeq1 splice='3|5|mySeq3'}", 36 ) . "{\$sequence} {/mySeq1} {/demo}";
  echo str_pad ( "{demo}{mySeq1 shift=2}", 36 )            . "{\$sequence} {/mySeq1} {/demo}";
  echo str_pad ( "{demo}{mySeq1 shift=2}", 36 )            . "{\$sequence} {/mySeq1} {/demo}";
  echo str_pad ( "{demo}{mySeq1 pop=2}", 36   )            . "{\$sequence} {/mySeq1} {/demo}";
  echo str_pad ( "{demo}{mySeq1 pop=2}", 36   )            . "{\$sequence} {/mySeq1} {/demo}";
  
  echo "{/table}";

?>