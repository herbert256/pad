<?php

  $tagAbout   = 'Sends what its content renders as a PDF instead of the page - a writer in plain PHP, the standard fonts, no library.';
  $tagGroup   = 'pictures';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{pdf file='name', size='A4', landscape, font='helvetica', title='text'} ... {/pdf}
{pdf preview, file='name'} ... {/pdf}
{pdf download}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'file'      => 'The file name of the document, default the page\'s name (<code>.pdf</code> is added).',
    'download'  => 'Bare option: sent as an attachment (<code>Content-Disposition: attachment</code>) instead of shown inline.',
    'size'      => '<code>A4</code> (default), <code>Letter</code>, <code>A5</code> or <code>Legal</code>.',
    'landscape' => 'Bare option: the page turned.',
    'font'      => '<code>helvetica</code> (default), <code>times</code> or <code>courier</code> - code is always Courier.',
    'title'     => 'The document\'s title, default its first heading.',
    'preview'   => 'Bare option: the page stays a page - the content on a sheet with links to open or download it; the same page asked with <code>&amp;padPdf</code> (<code>padPdf=download</code> for the attachment) answers the PDF.' ];

  $tagSee     = [ 'output', 'page', 'mail' ];

?>
