<?php

  // Prefixed forms such as {make:fibonacci} or {pull:mySeq} run exactly like the plain tag
  // forms - only the name resolution differs - so this just hands over to sequence/tag.php.

  return include PQ . 'sequence/tag.php';

?>