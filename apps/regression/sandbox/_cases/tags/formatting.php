<?php

  // {tidy} rewrites what it wraps as tidied HTML. {trace} has no case here: switching the
  // execution trace on inside a sandboxed pass trips the stats timing a real request
  // carries, so the trace tag is a pages test in regression2 instead.

  return [

    [ 'tidy normalises the html it wraps',
      <<<'PAD'
      {tidy}<p ><b >x</b ></p >{/tidy}
      PAD,
      '<p><b>x</b></p>' ],

  ];

?>
