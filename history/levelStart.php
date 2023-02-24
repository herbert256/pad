<?php

  $padHistoryCount++;
  
  $padHistoryLevel [$pad] = $padHistoryCount;

  $padHistoryResult [$padHistoryLevel [$pad]] ['level'] = padTraceGetLevel ($pad);

?>