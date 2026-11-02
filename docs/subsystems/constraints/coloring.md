# constraints: coloring

Fixed color budget 1..32; ≤256 nodes and graph validation reused with undirected unit edges. Colors are zero-based. Degree-first deterministic backtracking returns solved/infeasible/limit/cancelled, and never seeks a minimum chromatic number. Self-loops are infeasible. maxSteps and cancellation charge each search node and neighbor constraint.

Runnable example: `apps/tags/examples/solve/coloring.pad`. Independent fixture: `php tests/subsystems/expansion-constraints-coloring.php`.
