<?php

  if (! $pad )
    return;
  
  $padHistoryCount++;
  $padHistoryLevel [$pad] = $padHistoryCount;

  $padHistoryResult [$padHistoryCount] ['pad'] = $pad;

?>