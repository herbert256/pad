<?php

  $tagAbout   = 'Renders a template, or its own content, to an e-mail with an HTML and a text part, and sends it.';
  $tagGroup   = 'network';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{mail to=$email, template='order', subject='Order confirmation'}
{mail to=$email, subject='Welcome', from='shop@example.com', cc='a@example.com', bcc='b@example.com', replyTo='help@example.com'} ... {/mail}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'to'       => 'The address or addresses - <code>ann@example.com</code>, <code>Ann &lt;ann@example.com&gt;</code>, several separated by commas.',
    'subject'  => 'The subject - required.',
    'template' => 'A template in <code>_mail/</code>: <code>order.pad</code> the HTML part, <code>order.txt</code> the text part, <code>order.php</code> run first. Without it the pair\'s content is the HTML part.',
    'from'     => 'The sender, <code>$padMailFrom</code> by default.',
    'cc'       => 'Copy addresses.',
    'bcc'      => 'Blind copy addresses.',
    'replyTo'  => 'The Reply-To address.' ];

  $tagSee     = [ 'curl', 'page', 'form' ];

?>
