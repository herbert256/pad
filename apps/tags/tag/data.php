<?php

  $tagAbout   = 'Reads a supported data format, a list or a range into a named data set for a later tag to iterate.';
  $tagGroup   = 'values';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:data 'name'} ... {/pad:data}
{pad:data 'name', type='csv'} ... {/pad:data}
{pad:data 'name', format='toml'} ... {/pad:data}
{pad:data 'name', source/}
{pad:data} ... {/pad:data 'name'}
PAD;

  $tagParms   = [
    'name'   => 'The name the data is kept under; <code>{data:name}</code> ... <code>{/data:name}</code> (or <code>{data:name}</code>) iterates it. Not the name of a built-in or application tag.',
    'source' => 'Optional, when there is no content: the data as a value - <code>\'1..5\'</code>, <code>\'["a","b"]\'</code> - or a field holding it.' ];

  $tagOptions = [
    'type' => 'The reader to use explicitly: a data format, <code>list</code> or <code>range</code>.',
    'format' => 'The format to read, also accepted as <code>type=</code>: ' . appSubsystemLinks ( 'format' ) . '.' ];

  $tagSee     = [ 'content', 'bool', 'set', 'tree', 'count' ];

  $dataFormatRows = [];
  foreach ( padKinds ( 'formats' ) as $kind ) {
    $definition = padFormatDef ( $kind );
    $dataFormatRows [] = [ 'name' => $kind, 'link' => "kind/format/$kind",
                           'extensions' => implode ( ', ', $definition ['extension'] ?? [] ),
                           'reads' => isset ( $definition ['read'] ) ? 'yes' : 'no',
                           'writes' => isset ( $definition ['write'] ) ? 'yes' : 'no',
                           'about' => appSubsystemCatalog () ["format:$kind"] ['about'] ];
  }

?>
