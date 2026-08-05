<?php

  // {exception 'message'} throws a real PHP exception from inside a template, which PAD's
  // error handling then reports - try/try.php catches it when $padErrorTry is on, the
  // global handler otherwise. Mainly there to exercise that path.

  throw new Exception ($padParm);

?>