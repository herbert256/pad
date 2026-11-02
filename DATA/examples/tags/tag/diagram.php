<?php

  // The kinds are the files of pad/subs/diagrams/types/, as the engine reads them (padKinds).

  $tagKinds   = padKinds ( 'diagrams' );

  $tagAbout   = 'Draws a diagram from Mermaid-style lines of text, laid out on the server as inline SVG - '
              . count ( $tagKinds ) . ' kinds, from flowcharts and sequence diagrams to state machines, class and entity-relationship diagrams, mind maps, trees, user journeys, commit graphs, fishbones, railroad and packet diagrams.';
  $tagGroup   = 'graphics';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{diagram:auto direction='LR', edges='curved', title='text'} A[Start] --> B{Ready?} {/diagram:auto}
{diagram:sequence title='text'} Shop ->> Bank: Charge {/diagram:sequence}
{diagram:state} [*] --> Idle {/diagram:state}
{diagram:class} Animal <|-- Duck {/diagram:class}
{diagram:er} CUSTOMER ||--o{ ORDER : places {/diagram:er}
{diagram:mindmap} Root ... {/diagram:mindmap}
{diagram:tree direction='LR'} Root ... {/diagram:tree}
{diagram:journey} section Morning ... {/diagram:journey}
{diagram:gitgraph} commit ... {/diagram:gitgraph}
{diagram:fishbone} Effect ... {/diagram:fishbone}
{diagram:railroad} rule ::= 'a' item* {/diagram:railroad}
{diagram:packet bits=32} 0-15: "Field" ... {/diagram:packet}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'bits'      => 'packet: the bits in a row, <code>32</code> unless given, 4 to 64.',
    'direction' => 'flowchart, state, class, er and tree: <code>TB</code> (default), <code>BT</code>, <code>LR</code> or <code>RL</code> - else a first line <code>graph LR</code> in a flowchart, or a line <code>direction LR</code> in a state, class or entity-relationship diagram.',
    'edges'     => 'flowchart, state, class and er: how the edges run - <code>orthogonal</code> (default, rounded turns), <code>straight</code> or <code>curved</code>.',
    'title'     => 'The accessible name, by default the kind\'s - <code>Flowchart</code>, <code>Sequence diagram</code>, <code>State diagram</code> ... - and for a journey and a packet its <code>title</code> line.' ];

  $tagSee     = [ 'chart:bar', 'timeline', 'tree' ];

?>
