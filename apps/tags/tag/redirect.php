<?php

  $tagAbout   = 'Sends the browser to another page or address with a 302 and ends the request.';
  $tagGroup   = 'navigation';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pad:redirect 'name'/}
{pad:redirect 'orders/list', $status = 'open'/}
{pad:redirect 'https://example.com/'/}
PAD;

  $tagParms   = [
    'target' => 'A page of the application - <code>\'orders/list\'</code>, also in its <code>?page</code> form, routes resolved - or an absolute <code>http://</code> / <code>https://</code> address.' ];

  $tagOptions = [];

  $tagSee     = [ 'restart', 'page', 'exit' ];

?>
