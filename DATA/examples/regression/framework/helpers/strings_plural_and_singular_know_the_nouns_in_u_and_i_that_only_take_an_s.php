<?php

  // A word ending in -us or -is was taken for a singular - status, analysis - so the plural
  // of a noun in -u or -i that only takes an s was never recognised: padStrSingular kept
  // 'menus' and 'skis' as they were, and padStrPlural made 'menuses' and 'skises' of them.

  $words = [ 'menu', 'mainMenu', 'MENU', 'guru', 'emu', 'haiku', 'bureau', 'ski', 'taxi', 'kiwi', 'emoji', 'wiki', 'status', 'analysis' ];

  $r = json_encode ( array_map ( fn ( $word ) => [
    padStrPlural ( $word ),
    padStrSingular ( padStrPlural ( $word ) ),
    padStrPlural ( padStrPlural ( $word ) )
  ], $words ) );

  unset ( $words );

?>
