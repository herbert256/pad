<?php

  $padHistoryFile = "history/$app/$page/" . padFileTimestamp () . ".json";

  padFilePutContents ( $padHistoryFile, $padHistoryResult );

?>