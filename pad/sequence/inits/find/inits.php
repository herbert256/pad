<?php

  $pqFindParm = $padOpt [$pad] [1];

      if ( pqPlay ( $pqTag ) or pqPlay ( $pqType ) ) $pqFindType = 'play';
  elseif ( $pqTag == 'sequence'  or $pqType == 'sequence'  ) $pqFindType = 'sequence';
  elseif ( $pqTag == 'action'    or $pqType == 'action'    ) $pqFindType = 'action';
  elseif ( $pqTag == 'pull'      or $pqType == 'pull'      ) $pqFindType = 'pull';
  elseif ( $pqTag == 'continue'  or $pqType == 'continue'  ) $pqFindType = 'continue';

?>