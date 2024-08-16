<?php

  $result = yaml_parse ($data);

  if ( ! is_array($result) or $result === NULL or $result === FALSE)
    return padError ( "YAML parse error" );

  return $result;

?>