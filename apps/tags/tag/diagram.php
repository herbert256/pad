<?php

  // The kinds are the files of pad/subs/diagrams/types/, as the engine reads them (padKinds).

  $tagKinds   = padKinds ( 'diagrams' );

  $tagAbout   = 'Draws a diagram from Mermaid-style lines of text, laid out on the server as inline SVG - '
              . count ( $tagKinds ) . ' kinds, from flowcharts and sequence diagrams to state machines, class and entity-relationship diagrams, mind maps, trees, user journeys, commit graphs, fishbones, railroad and packet diagrams.';
  $tagGroup   = 'graphics';
  $tagForm    = 'pair';

  $tagSyntax  = <<<'PAD'
{diagram direction='LR', edges='curved', title='text'} A[Start] --> B{Ready?} {/diagram}
{diagram type='sequence', title='text'} Shop ->> Bank: Charge {/diagram}
{diagram:state} [*] --> Idle {/diagram:state}
{diagram:class} Animal <|-- Duck {/diagram:class}
{diagram:er} CUSTOMER ||--o{ ORDER : places {/diagram:er}
{mindmap} Root ... {/mindmap}
{diagram:tree direction='LR'} Root ... {/diagram:tree}
{journey} section Morning ... {/journey}
{gitgraph} commit ... {/gitgraph}
{fishbone} Effect ... {/fishbone}
{railroad} rule ::= 'a' item* {/railroad}
{packet bits=32} 0-15: "Field" ... {/packet}
PAD;

  $tagParms   = [];

  $tagOptions = [
    'type'      => 'The kind, a file of <code>pad/subs/diagrams/types/</code>: '
                 . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", $tagKinds ) )
                 . ' - <code>flowchart</code> unless it says otherwise. The diagram type names it as well, <code>{diagram:state}</code>, and so does the bare name, <code>{state}</code>, when nothing else has it; <code>sequence</code> and <code>tree</code> are tags of their own, so those are <code>{diagram:sequence}</code> and <code>{diagram:tree}</code>. A first line written the Mermaid way - <code>sequenceDiagram</code>, <code>stateDiagram-v2</code>, <code>classDiagram</code>, <code>erDiagram</code>, <code>gitGraph</code>, <code>ishikawa-beta</code>, <code>packet-beta</code> - names the kind too.',
    'bits'      => 'packet: the bits in a row, <code>32</code> unless given, 4 to 64.',
    'direction' => 'flowchart, state, class, er and tree: <code>TB</code> (default), <code>BT</code>, <code>LR</code> or <code>RL</code> - else a first line <code>graph LR</code> in a flowchart, or a line <code>direction LR</code> in a state, class or entity-relationship diagram.',
    'edges'     => 'flowchart, state, class and er: how the edges run - <code>orthogonal</code> (default, rounded turns), <code>straight</code> or <code>curved</code>.',
    'title'     => 'The accessible name, by default the kind\'s - <code>Flowchart</code>, <code>Sequence diagram</code>, <code>State diagram</code> ... - and for a journey and a packet its <code>title</code> line.' ];

  $tagSee     = [ 'chart', 'timeline', 'tree' ];

?>
