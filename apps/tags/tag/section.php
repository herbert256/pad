<?php

  // The sections are the files of pad/subs/sections/types/, as the engine reads them (padSectionTypes,
  // pad/subs/sections/_lib/section.php - loaded when a page uses a section).

  $tagAbout   = 'A block of a landing page - ' . count ( padKinds ( 'sections' ) ) . ' of them, from a hero, the features and the prices to the questions, a newsletter form and the footer - in pure CSS.';
  $tagGroup   = 'layout';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{section:kind 'heading', ...} ... {/section:kind}
{section:kind title='heading', ...} ... {/section:kind}
{section:hero title='heading', button='text', link='?page', image='picture.jpg', alt='text'} text {/section:hero}
{section:features title='heading', columns=3} {section:item 'name', icon='zap'} text {/section:item} ... {/section:features}
{section:pricing data='plans'/}
{section:pricing} {section:item 'name', price='€12', period='month', featured} a feature a line {/section:item} ... {/section:pricing}
{section:faq open=1, single} {section:item 'question'} answer {/section:item} ... {/section:faq}
{section:cta 'heading', button='text', link='?page'/}
{section:newsletter 'heading', button='Subscribe'/}
{section:contact 'heading', mail='...', phone='...', address='...', form/}
{section:footer brand='name', note='small print'} {section:item 'column'} Label | ?page {/section:item} ... {/section:footer}
{section:comparison plans='name, name', featured='name'} {section:item 'feature', values='yes; no'/} ... {/section:comparison}
{section:changelog} {section:item 'version', date='2026-10-30', added='a; b', fixed='c'} text {/section:item} ... {/section:changelog}
{section:downloads} {section:item 'file', link='?file', size=2457600, checksum='sha256:...'/} ... {/section:downloads}
{section:agenda timezone='CET'} {section:item 'title', from='09:00', to='10:00', track='name'/} ... {/section:agenda}
{section:blog data='posts'/}
{section:highlights} {section:item 'title', image='picture.jpg', alt='text'} text {/section:item} ... {/section:highlights}
{section:hero} {pad:slot 'eyebrow'} ... {/pad:slot} {pad:slot 'actions'} ... {/pad:slot} {/section:hero}
PAD;

  $tagParms   = [
    'kind after colon'    => 'The section, a file of <code>pad/subs/sections/types/</code>: '
               . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padKinds ( 'sections' ) ) )
               . '. The section: prefix selects it - <code>{section:hero}</code> - and that prefix is always written: a section is never reached bare, as hero, team and footer are the names of fields and pages.',
    'heading' => 'The section\'s own first value, used after <code>{section:hero}</code>: its heading, as <code>title=</code>; for <code>{section:item}</code> the item\'s name - a plan, a question, a person.' ];

  $tagOptions = [
    'title'      => 'The heading; also the first value. A <code>{pad:slot \'title\'}</code> of markup wins over it.',
    'eyebrow'    => 'The small line above the heading.',
    'text'       => 'The line under the heading - the content between the tags wins over it, and a <code>{pad:slot \'text\'}</code> over both.',
    'button'     => 'hero, cta: the text of the main button, with <code>link</code> the page it goes to; newsletter, contact: the text of the form\'s button.',
    'second'     => 'hero, cta: a quieter second button, with <code>secondLink</code>.',
    'image'      => 'hero: a picture beside the words, <code>alt</code> its text - a <code>{pad:slot \'image\'}</code> of markup in its place.',
    'note'       => 'hero, cta, pricing, newsletter: the small print; footer: the line under it all.',
    'data'       => 'A list section - features, pricing, testimonials, faq, stats, steps, team, logos, gallery, footer, comparison, changelog, downloads, agenda, blog, highlights: its items from the rows of a store, the page\'s array or a <code>_data</code> file, by their fields.',
    'columns'    => 'The cards side by side at the widest, 1 to 6 - fewer as the section narrows.',
    'open'       => 'faq: the question open at first, by its number; bare, every question. <code>single</code> keeps one open at a time.',
    'form'       => 'newsletter: the form\'s name (<code>newsletter</code>); contact: bare for a message form, or its name. <code>thanks</code> is what shows once it came back right.',
    'mail'       => 'contact: the mail address, a link; <code>phone</code>, <code>address</code> and <code>hours</code> the rest of the details.',
    'brand'      => 'footer: the name at its side.',
    'plans'      => 'comparison: the plans, a column each - else the fields of the rows of <code>data</code>; <code>featured</code> the plan in the accent.',
    'timezone'   => 'agenda: the clock the times are on, said under the heading; <code>tracks</code> the tracks in their order.',
    'level'      => 'The heading\'s level, 1 to 6 (2); the items\' headings one more.',
    'tone'       => '<code>plain</code>, <code>soft</code> (the accent\'s light shade) or <code>accent</code> (the accent itself) - the background.',
    'align'      => '<code>center</code> or <code>left</code>.',
    'id'         => 'The section\'s id, to link to it - <code>?page#pricing</code>.' ];

  $tagSee     = [ 'slot', 'widget:tabs', 'form', 'icon:star' ];

?>
