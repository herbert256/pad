<?php

  if ( $padXml [$padXmlLvl] ['source'] )
    padXref ( 'status', $padXml [$padXmlLvl] ['source'] );
  else
    padXref ( 'status', 'NONE' );

?>