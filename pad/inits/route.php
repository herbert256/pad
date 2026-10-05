<?php

  // The path below the application's entry point, for a clean URL (lib/route.php): on
  // /shop/products/42 it is products/42, on /shop/?products it is empty. Read before
  // inits/vars.php, because a link written {$padGo}page&padInclude in the clean form carries
  // its request values in the path, and vars.php reads padInclude among them; inits/page.php
  // then decides whether the path or the query string names the page.

  $padRoutePath = padRequestPath ();

?>