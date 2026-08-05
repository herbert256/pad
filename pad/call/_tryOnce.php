<?php

  // As call/_try.php but with include_once, the body run through try/try.php by
  // call/_once.php; returns TRUE instead of the file's own value on a repeat include.

   return include_once $padCall;

 ?>