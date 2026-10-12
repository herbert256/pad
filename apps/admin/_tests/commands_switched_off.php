<?php

  // With $adminCommands off no page starts the pad command - the queues' retry, flush and
  // work, the schedule's run and its listing as well as the console.

  $commandsWere              = $GLOBALS ['adminCommands'] ?? TRUE;
  $GLOBALS ['adminCommands'] = FALSE;

  [ $commandCode, $commandSaid ] = adminRun ( [ 'help' ] );

  $GLOBALS ['adminCommands'] = $commandsWere;

?>
