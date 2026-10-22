<?php

  // The rescued fixture: a guard that asks padAuthRequire inside padRescue and goes on to
  // let the visitor through by its own test. Asked directly, a guest is sent to the login
  // page, here the vars fixture; included by a {page} of a public page, the refusal holds
  // though the guard swallowed it, and the part renders as nothing.

  padRescue ( fn () => padAuthRequire ( 'request/vars' ), FALSE, FALSE );

  return TRUE;

?>
