<?php

  foreach ( glob ( PAD . 'seq/one/types/*.php' ) as $file ) {

    $single = str_replace ( PAD . 'seq/one/types/', '', $file   );
    $single = str_replace ( '.php',                     '', $single );

    $extra = ( $single == 'element') ? '|3' : '';

    $code = "{table}\n\n"
          . "{demo}{seq '40..60', one='$single$extra'}\n  {\$seq}\n{/seq}{/demo}\n\n"
          . "{/table}";

    file_put_contents ( APP . "seq/one/$single.pad", $code );

  }

?>