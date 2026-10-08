<?php

  // Fails until its third attempt.

  if ( $padJob ['attempts'] < 3 )
    throw new RuntimeException ( "not yet, attempt {$padJob ['attempts']}" );

  $GLOBALS ['queueHeard'] [] = "flaky made it on attempt {$padJob ['attempts']}";

?>
