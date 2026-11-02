<?php

  $tagAbout   = 'A form field with its label that refills from what its form sent and shows the error validation found for it.';
  $tagGroup   = 'forms';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{input:field 'name'/}
{input:field 'email', type='email', label='E-mail', value='', id='mail', placeholder='you@example.org', required/}
{input:field 'terms', type='checkbox', label='I agree', value='yes', checked/}
{input:field 'email', type='email', label='E-mail', rules='required|email'/}
{input:slider 'volume', label='Volume', min=0, max=100/}
PAD;

  $tagParms   = [
    'name' => 'The field name - required. <code>user[email]</code> and <code>tags[]</code> are found where PHP files them; the id is the name with what an id cannot hold made a dash.' ];

  $tagOptions = [
    'type'    => 'The input type, <code>text</code> by default: <code>email</code>, <code>number</code>, <code>checkbox</code>, <code>radio</code>, <code>hidden</code>, <code>password</code>, <code>file</code>, <code>submit</code> ...',
    'label'   => 'A <code>&lt;label for&gt;</code> before the field - after a checkbox or radio - and the word its error message uses.',
    'value'   => 'The value before anything was sent; for a checkbox or radio its own value (<code>on</code> by default).',
    'id'      => 'The id, by default the name.',
    'checked' => 'Bare option: a checkbox or radio is checked before a post.',
    'rules'   => 'The field\'s rules - <code>required|email|max:80</code> - read from the page\'s own template and checked before its PHP runs. Only in a named <code>{input:form}</code> of the page itself.' ];

  $tagSee     = [ 'form', 'textarea', 'attrs', 'validator' ];

?>
