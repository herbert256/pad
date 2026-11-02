# graphs: strongcomponents

Directed strong components via two graph traversals. Groups and their members follow original node order. Condensation uses zero-based group IDs and sorted unique edges, producing a DAG. Requires directed=true; normal graph limits (1000 nodes, 10000 edges) apply. The fixture checks all 64 directed three-node graphs against independent transitive closure.

Runnable example: `apps/tags/examples/graph/strongcomponents.pad`. Independent fixture: `php tests/subsystems/expansion-graphs-strongcomponents.php`.
