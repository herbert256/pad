<?php

  // The editors fixture: every page below this directory needs the ability edit-box, which
  // nobody has - asked for one, the visitor gets 403. Included by a {page} of a public page,
  // the guard refuses and the part renders as nothing.

  padGate ( 'edit-box', fn ( ?array $user ) => FALSE );

  padAuthorize ( 'edit-box' );

?>
