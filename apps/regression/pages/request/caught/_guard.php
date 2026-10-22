<?php

  // The caught fixture: a guard that asks padAuthorize inside a try/catch of its own and
  // goes on to let the visitor through by its own test. Asked directly, the visitor gets
  // 403; included by a {page} of a public page, the refusal holds though the guard caught
  // it, and the part renders as nothing.

  padGate ( 'edit-caught', fn ( ?array $user ) => FALSE );

  try {
    padAuthorize ( 'edit-caught' );
  } catch ( Throwable $caughtRefusal ) {
  }

  return TRUE;

?>
