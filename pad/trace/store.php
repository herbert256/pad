<?php

  if ( $padTraceTypes ['store'] and $padTraceTypes ['tree'] )
    padFilePutContents ( "$padTraceDir/store.pad", $padStoreData );

?>