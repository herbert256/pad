<?php

  // Stage three of the evaluator: cuts the token stream at every 'pipe' token and returns
  // the segments in &$pipes, numbered from 0 and keeping the original token keys. The
  // caller (eval/eval.php) then runs padEvalResult over the segments in turn, feeding each
  // result in as the next segment's input value - which is what makes {echo $x | trim |
  // upper} chain. An expression without pipes simply yields one segment.

  function padEvalPipes ( $result, &$pipes ) {

    $pipe  = 0;
    $pipes = [];

    foreach ( $result as $key => $val )
      if ( $val [1] == 'pipe' )
        $pipe++;
      else
        $pipes [$pipe] [$key] = $val;

  }

?>