<?php

  $tagAbout   = 'Colours source code on the server - PAD, PHP, HTML, CSS, JavaScript, JSON, YAML, SQL or shell - with optional line numbers and marked lines.';
  $tagGroup   = 'text';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{highlight 'lang'} ... {/highlight}
{highlight 'lang', lines, mark='3, 5-7'} ... {/highlight}
{highlight file='_data/products.json'}
PAD;

  $tagParms   = [
    'lang' => 'The language: <code>pad</code>, <code>php</code>, <code>html</code> (<code>xml</code>, <code>svg</code>), <code>css</code>, <code>js</code> (<code>ts</code>, <code>json5</code>), <code>json</code>, <code>yaml</code>, <code>sql</code>, <code>bash</code> (<code>sh</code>) or <code>text</code>. With <code>file=</code> it defaults to the file\'s extension, otherwise to <code>text</code>.' ];

  $tagOptions = [
    'file'  => 'A file of the application to show instead of the content - never one outside it, under <code>_config/</code> or a dotfile.',
    'lines' => 'Bare option: number the lines.',
    'mark'  => 'Lines to set apart: <code>\'4\'</code>, <code>\'3, 5-7\'</code>.' ];

  $tagSee     = [ 'source', 'markdown', 'ignore' ];

?>
