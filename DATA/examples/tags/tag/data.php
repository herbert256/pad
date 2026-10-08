<?php

  $tagAbout   = 'Turns its content - JSON, XML, YAML, CSV, a list or a range - into a named data set for a later tag to iterate.';
  $tagGroup   = 'values';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{data 'name'} ... {/data}
{data 'name', type='csv'} ... {/data}
{data 'name', source}
{data} ... {/data 'name'}
PAD;

  $tagParms   = [
    'name'   => 'The name the data is kept under; <code>{name}</code> ... <code>{/name}</code> (or <code>{data:name}</code>) iterates it. Not the name of a built-in or application tag.',
    'source' => 'Optional, when there is no content: the data as a value - <code>\'1..5\'</code>, <code>\'["a","b"]\'</code> - or a field holding it.' ];

  $tagOptions = [
    'type' => 'The format of the content - <code>json</code>, <code>xml</code>, <code>yaml</code>, <code>csv</code>, <code>list</code>, <code>range</code> ... - when its first characters do not tell.' ];

  $tagSee     = [ 'content', 'bool', 'set', 'tree', 'count' ];

?>
