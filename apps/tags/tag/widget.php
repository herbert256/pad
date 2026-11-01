<?php

  // The widgets are the files of pad/subs/widgets/types/, as the engine reads them (padWidgetTypes).

  $tagAbout   = 'An interactive part of the page that works without JavaScript - ' . count ( padWidgetTypes () ) . ' of them, from tabs, a modal and a carousel to a toast, a dropdown and a wizard.';
  $tagGroup   = 'widgets';
  $tagForm    = 'both';

  $tagSyntax  = <<<'PAD'
{widget 'name', ...} ... {/widget}
{widget:name ...} ... {/widget:name}
{tabs active='label'} {tab 'label'} ... {/tab} ... {/tabs}
{accordion single, open=1} {tab 'label'} ... {/tab} ... {/accordion}
{carousel label='name'} {tab 'caption'} ... {/tab} ... {/carousel}
{wizard active=1, next='Next', back='Back'} {tab 'step'} ... {/tab} ... {/wizard}
{lightbox label='name'} {tab 'caption'} <img ...> {/tab} ... {/lightbox}
{splitpane size=30, min=20, max=60, stacked, collapse, height='16rem'} {tab 'label'} ... {/tab} {tab 'label'} ... {/tab} {/splitpane}
{modal 'name', title='heading', button='text'} ... {/modal}
{drawer 'name', title='heading', button='text', side='left'} ... {/drawer}
{dropdown 'text', align='end'} <a href="..."> ... </a> ... {/dropdown}
{contextmenu 'name', area='tr'} <a href="..."> ... </a> ... {/contextmenu}
{commandpalette 'text', data='rows', key='k', placeholder='hint', empty='text'} <a href="..."> ... </a> ... {/commandpalette}
{tooltip 'tip'} ... {/tooltip}
{toast 'title', tone='success', place='top', timeout=5} ... {/toast}
{copy 'text', label='Copy'/}
{poll 'name', options='Yes, No', title='question', button='Vote', results/}
{breadcrumb home='Home', label='Breadcrumb'/}
PAD;

  $tagParms   = [
    'name' => 'The widget, a file of <code>pad/subs/widgets/types/</code>: '
            . implode ( ', ', array_map ( fn ( $widget ) => "<code>$widget</code>", padWidgetTypes () ) )
            . '. The widget: type names it as well - <code>{widget:modal \'terms\'}</code> - and so does the bare name, <code>{modal \'terms\'}</code>, after every other kind of tag: a store, a field or an array of the name wins.',
    'first'  => 'The widget\'s own first parameter, the second of <code>{widget}</code>: the name of a modal, a drawer, a poll or a context menu, the text of a copy box, the title of a toast, the button of a dropdown, the tip of a tooltip, the button of a command palette.' ];

  $tagOptions = [
    'active'  => 'tabs, wizard: the tab or step shown first, by its number from 1 or its label.',
    'single'  => 'Bare option, accordion: one item open at a time.',
    'open'    => 'accordion: the item open at first, by its number or its label; bare, every item.',
    'label'   => 'carousel, lightbox, wizard, breadcrumb: the name for a screen reader; copy: the button\'s text.',
    'title'   => 'modal, drawer: the heading that names the dialog; poll: the question.',
    'button'  => 'modal, drawer: the text of the button that opens it (<code>Open</code>); poll: the vote button.',
    'side'    => 'drawer: <code>right</code> (the default), <code>left</code> or <code>bottom</code>.',
    'align'   => 'dropdown: <code>start</code> (the default) or <code>end</code> - the menu\'s edge under the button\'s.',
    'place'   => 'tooltip: <code>top</code> or <code>bottom</code>; toast: <code>inline</code>, <code>top</code> or <code>bottom</code> - in the flow, or in that corner of the window.',
    'tone'    => 'toast: <code>info</code>, <code>success</code>, <code>warning</code> or <code>error</code>.',
    'timeout' => 'toast: seconds, 1 to 600, after which it closes by itself, which its script does.',
    'next'    => 'wizard: the text of the Next button (<code>Next</code>).',
    'back'    => 'wizard: the text of the Back button (<code>Back</code>).',
    'options' => 'poll: the answers, separated by commas - two or more.',
    'results' => 'Bare option, poll: the results to everyone, also before a vote.',
    'home'    => 'breadcrumb: the first step\'s text when the trail is the page\'s path (<code>Home</code>).',
    'data'    => 'breadcrumb: the steps from rows - their label and link fields - of a store, the page\'s array or a <code>_data</code> file; commandpalette: the commands from rows - label, link, group and keywords.',
    'size'    => 'splitpane: the first pane\'s share in percent, a whole number (<code>50</code>).',
    'min'     => 'splitpane: the smallest share of the first pane, in percent (<code>10</code>).',
    'max'     => 'splitpane: the largest share of the first pane, in percent (<code>90</code>).',
    'stacked' => 'Bare option, splitpane: the panes one above the other, the bar between them across.',
    'collapse' => 'Bare option, splitpane: Enter on the bar or a double click folds the first pane away and back.',
    'height'  => 'splitpane: the height of the panes, a CSS length - <code>300px</code>, <code>24rem</code>, <code>60vh</code>; stacked <code>20rem</code> when not given.',
    'area'    => 'contextmenu: the part a right click opens the menu on - the nearest element around it of a tag name or a class, <code>tr</code>, <code>.card</code>; the element it stands in when not given.',
    'key'     => 'commandpalette: the letter of the shortcut with Ctrl or &#8984; (<code>k</code>).',
    'placeholder' => 'commandpalette: the hint in the search box (<code>Type a command</code>).',
    'empty'   => 'commandpalette: what shows when nothing is found (<code>No command found</code>).' ];

  $tagSee     = [ 'tab', 'copy', 'live', 'form' ];

?>
