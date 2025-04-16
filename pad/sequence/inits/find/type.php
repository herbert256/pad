<?php

      if ( padSeqPlay ( $padSeqTag ) or padSeqPlay ( $padSeqType ) ) $padSeqFindType = 'play';
  elseif ( $padSeqTag == 'sequence'  or $padSeqType == 'sequence'  ) $padSeqFindType = 'sequence';
  elseif ( $padSeqTag == 'action'    or $padSeqType == 'action'    ) $padSeqFindType = 'action';
  elseif ( $padSeqTag == 'pull'      or $padSeqType == 'pull'      ) $padSeqFindType = 'pull';
  elseif ( $padSeqTag == 'continue'  or $padSeqType == 'continue'  ) $padSeqFindType = 'continue';
  else                                                               $padSeqFindType = '???';

?>