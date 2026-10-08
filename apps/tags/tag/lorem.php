<?php

  $tagAbout   = 'Placeholder text - Lorem ipsum and Latin words - in words, sentences or paragraphs, the same text for the same seed.';
  $tagGroup   = 'text';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{lorem}
{lorem words=4}
{lorem sentences=2, seed=7}
{lorem paragraphs=3, sentences=4, varied}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'words'      => 'So many words exactly, as sentences; fewer than five are a title, without a full stop. 50 when no length is given.',
    'sentences'  => 'So many sentences of six to fourteen words; with <code>paragraphs</code>, the sentences of each paragraph.',
    'paragraphs' => 'So many <code>&lt;p&gt;</code> paragraphs, of four to seven sentences unless <code>sentences</code> says.',
    'seed'       => 'Another text, the same again for the same seed - default 0.',
    'varied'     => 'Bare option: leave the classic opening "Lorem ipsum dolor sit amet" out - for the second text on a page.' ];

  $tagSee     = [ 'placeholder', 'excerpt', 'sequence' ];

?>
