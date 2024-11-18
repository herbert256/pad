<?php

  $one = "{table}\n\n"
       . "{demo}{seq loop, from=1, to=10}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq loop, from=1, to=10, make, $type}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( PAD . "seq/types/$type/flags/make", 1 );
  file_put_contents ( APP . "seq/make/{$type}.pad", $one );

?>