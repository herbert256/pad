<?php

  // The DBGp pieces on their own: packets cut from a stream, a command line with a quoted
  // value and base64 data, file URIs both ways, and a property - an array with a string in
  // it - as the browser gets it.

  $buffer = "12\0<a>first</a>\0" . "13\0<b>second</b>\0" . "20\0<c>not yet";
  $frames = implode ( ' | ', dbgpFrames ( $buffer ) );
  $rest   = strlen ( $buffer );

  $command = rtrim ( dbgpCommand ( 'property_get', 7, [ 'n' => "\$row['first name']", 'd' => 0 ] ), "\0" );
  $evalCmd = rtrim ( dbgpCommand ( 'eval', 8, [], '$a + 1' ), "\0" );

  $uri  = dbgpUri ( '/tmp/a dir/x.php' );
  $back = dbgpPath ( $uri );

  $xml = '<?xml version="1.0" encoding="iso-8859-1"?>'
       . '<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" command="property_get" transaction_id="7">'
       . '<property name="$order" fullname="$order" type="array" children="1" numchildren="2" page="0" pagesize="100">'
       . '<property name="id" fullname="$order[&quot;id&quot;]" type="int"><![CDATA[42]]></property>'
       . '<property name="name" fullname="$order[&quot;name&quot;]" type="string" size="5" encoding="base64"><![CDATA[' . base64_encode ( 'caf' . "\u{e9}" ) . ']]></property>'
       . '</property></response>';

  $property = dbgpProperty ( dbgpParse ( $xml )->property );
  $shape    = $property ['type'] . ' ' . $property ['numchildren'] . ': '
            . implode ( ', ', array_map ( fn ( $p ) => $p ['fullname'] . ' (' . $p ['type'] . ') ' . $p ['value'], $property ['items'] ) );

  $broke = dbgpLocation ( dbgpParse ( '<response xmlns="urn:debugger_protocol_v1" xmlns:xdebug="https://xdebug.org/dbgp/xdebug" status="break">'
                                    . '<xdebug:message filename="file:///tmp/a%20dir/x.php" lineno="12"></xdebug:message></response>' ) );
  $where = $broke ['file'] . ':' . $broke ['line'];

  $error = dbgpError ( dbgpParse ( '<response xmlns="urn:debugger_protocol_v1"><error code="5"><message><![CDATA[command is not available]]></message></error></response>' ) );

?>
