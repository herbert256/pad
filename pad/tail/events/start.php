<?php

  include      pad . 'config/tail.php';
  include_once pad . 'tail/lib/tail.php';

  if ( padTailExists ( padApp . 'config/tail.php' ) )
    include padApp . 'config/tail.php';

  if     ( $padRequest ) define ( 'padTail', TRUE  );
  elseif ( $padTrace   ) define ( 'padTail', TRUE  );
  elseif ( $padTrack   ) define ( 'padTail', TRUE  );
  elseif ( $padXml     ) define ( 'padTail', TRUE  );
  elseif ( $padXref    ) define ( 'padTail', TRUE  );
  else                   define ( 'padTail', FALSE );

  if ( ! padTail )
    return;

  $padTailId    = hrtime (true);
  $padTailCnt   = 0;
  $padTraceLine = 0;
  $padXmlId     = 0;
  $padXrefId    = 0;

  if ( $padRequest ) include pad . 'tail/types/request/start.php';
  if ( $padTrace   ) include pad . 'tail/types/trace/entry/config.php';
  if ( $padTrack   ) include pad . 'tail/types/track/start.php';
  if ( $padXml     ) include pad . 'tail/types/xml/start.php';
  if ( $padXref    ) include pad . 'tail/types/xref/start.php';

  if ( padTailExists ( padApp . 'config/tail.php' ) )
    include padApp . 'config/tail.php';

?>