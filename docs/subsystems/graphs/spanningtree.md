# graphs: spanningtree

Minimum spanning forest using Kruskal. Defaults to undirected; directed=true is rejected. Finite nonnegative weights are supported, self-loops ignored, parallel edges rejected by normal graph validation. Equal weights use original node-order endpoint ties. Disconnected components and connected flag are explicit. Weight overflow fails.

Runnable example: `apps/tags/examples/graph/spanningtree.pad`. Independent fixture: `php tests/subsystems/expansion-graphs-spanningtree.php`.
