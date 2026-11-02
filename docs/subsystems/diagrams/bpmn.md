# bpmn

A drawing-only subset: `lane ID "Label"`; `start|end|task|exclusive|parallel ID LANE "Label"`; `ID -> ID "optional branch label"`. Lanes are rows; declaration order determines left-to-right placement. Supports start/end events, tasks and exclusive/parallel gateways, and sequence flows. It rejects other BPMN constructs, invalid lanes, unknown nodes, end outgoing/start incoming flows and self-loops. Maximum 8 lanes, 40 nodes, 100 edges, 20 KB source. No BPMN XML interchange, message semantics, tokens or workflow execution.

The shared SVG chart shell supplies responsive sizing, theme colours, escaped title/description and CSP handling. Full labels remain in accessible descriptions when visual labels are shortened.

```pad
{diagram:bpmn}
lane sales "Sales"
lane ops "Operations"
start begin sales "Order received"
task check sales "Check order"
exclusive paid sales "Paid?"
task pack ops "Pack goods"
end done ops "Dispatched"
begin -> check
check -> paid
paid -> pack "yes"
pack -> done
{/diagram:bpmn}
```
