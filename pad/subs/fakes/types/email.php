<?php

  // email - An address at example.com, .org or .net, the domains kept for examples, so a
  // message sent to one reaches nobody: emma.walker@example.org. name= makes it from that
  // name - {fake 'email', name=$name/} - in a row with a name before it it is that name's,
  // else it is made from a name of its own (padFakeEmail).

  return padFakeEmail ( padFakeOpt ( 'name' ) ?? padFakeInRow ( 'name' ) );

?>
