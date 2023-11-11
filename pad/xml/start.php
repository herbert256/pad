<?php
  
  include_once pad . 'xml/lib.php';
  
  $padXmlFile = "xml/$padPage/$padReqID.xml";

  $padXmlTag = [];
  $padXmlOcc = [];

  $padXmlTag [$pad] = '';
  $padXmlOcc [$pad] = '';

  $padXmlTagReturn = '';

  padXmlWriteOpen ( 'pad' );

?>