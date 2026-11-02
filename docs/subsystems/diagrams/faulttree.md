# faulttree

A drawing-only rooted tree: `top|event|and|or ID "Label"` and `PARENT -> CHILD`. Exactly one top event has one child; AND/OR gates require at least two children; basic events are leaves. Rejects duplicate/missing nodes, multiple parents, cycles and disconnected components. Maximum 40 nodes, 39 edges, 20 KB source. AND means all inputs required; OR means any input sufficient, explicitly in accessible text. No probabilities or independence assumptions are inferred.

The shared SVG chart shell supplies responsive sizing, theme colours, escaped title/description and CSP handling. Full labels remain in accessible descriptions when visual labels are shortened.

```pad
{diagram:faulttree}
top failure "Service unavailable"
or gate "Failure causes"
event power "Power lost"
event network "Network lost"
failure -> gate
gate -> power
gate -> network
{/diagram:faulttree}
```
