<?php

  $tagAbout   = 'Keeps a piece of template under a name instead of printing it, for a later {content:name} to render.';
  $tagGroup   = 'values';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pad:content 'name'} ... {/pad:content}
{pad:content 'name', 'text'/}
{pad:content} ... {/pad:content 'name'}
PAD;

  $tagParms   = [
    'name' => 'The name the content is kept under; <code>{content:name}</code> or <code>{content:name}</code> renders it, <code>content=\'name\'</code> on another tag uses it as that tag\'s content. Not the name of a built-in or application tag.',
    'text' => 'Optional, when there is no content: the content as a quoted text, or a field whose value is kept as text.' ];

  $tagOptions = [];

  $tagSee     = [ 'data', 'bool', 'null', 'false' ];

?>
