<?php

  $tagAbout   = 'Renders its content, or the value of its parameter, as Markdown and writes the HTML.';
  $tagGroup   = 'text';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{markdown $text}
{markdown} ... {/markdown}
{markdown html} ... {/markdown}
{markdown ignore} ... {/markdown}
PAD;

  $tagParms   = [
    'text' => 'In the single form: the Markdown to write, as the <code>markdown</code> pipe reads a value.' ];

  $tagOptions = [
    'html'   => 'Bare option: let the author\'s own raw HTML through - inline tags, and a block-level element or comment at the start of a line up to the next blank line. Without it raw HTML is escaped.',
    'ignore' => 'Bare option: the braces in the content are text, not tags - for code samples in the Markdown.' ];

  $tagSee     = [ 'highlight', 'collection', 'ignore', 'toc' ];

?>
