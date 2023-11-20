<?php
 
  padXml     ();
  padXmlTidy ();

  padFilePutContents ( $GLOBALS ['padXmlDir'] . '/tree.json' , $GLOBALS ['padXmlTree'] );

?>