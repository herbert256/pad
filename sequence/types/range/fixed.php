<?php

  $padSeqRangeParms = padExplode ( $padSeqParm, '..' );

  return range ( $padSeqRangeParms[0], $padSeqRangeParms[1], $padSeqInc );

?>