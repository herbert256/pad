<?php

  $one = "{table}\n\n"
       . "{demo}{sequence loop, from=1, to=10}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence loop, from=1, to=10, make, $type}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( "sequence/types/$type/flags/make", 1 );
  file_put_contents ( APP . "sequence/make/{$type}.pad", $one );

?>