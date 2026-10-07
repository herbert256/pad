<?php

  // A section kept per vary= value is forgotten by that value as code has it at hand: the
  // section rendered with vary=$userId, the integer 42, and padFragmentForget ( 'name', '42' )
  // - the same user, as the request or a database row spells it - missed, because the key
  // was made of the value's serialized type as well, and the old copy stayed.

  padFragmentForget ( 'fwVaryType', 42 );

  $userId = 42;

?>
