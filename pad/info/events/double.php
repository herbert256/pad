<?php

    global $pad, $padDouble;

    if ( $base and $new )
      if     ( strpos ( $new, '@content@'  ) !== FALSE ) $padDouble [$pad] = 'mrg-new';
      elseif ( strpos ( $base, '@content@' ) !== FALSE ) $padDouble [$pad] = 'mrg-base';
      elseif ( $merge == 'replace'                     ) $padDouble [$pad] = 'dbl-new';
      elseif ( $merge == 'bottom'                      ) $padDouble [$pad] = 'dbl-bottom';
      elseif ( $merge == 'top'                         ) $padDouble [$pad] = 'dbl-top';
      elseif ( $new                                    ) $padDouble [$pad] = 'dbl-new';
      else                                               $padDouble [$pad] = 'dbl-base';

    if ( padXref ) {
      include pad . 'info/types/xref/items/double.php';
      include pad . 'info/types/xref/items/content.php';
    }

?>