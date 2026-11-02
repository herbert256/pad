# Graph algorithms

`padGraph(kind, nodes, edges, options = [])` accepts at most 1000 nodes and 10000
edges. Nodes are unique nonempty string IDs (max 256 bytes), or records containing
`id` and optional finite nonnegative `duration`. Edges are `[from,to,weight?]` or
`{from,to,weight?}` records. Endpoints must exist; duplicate edges fail. Node input
order breaks ties. Options and record fields are checked, never silently ignored.

`directed=true` by default. `toposort` and `criticalpath` require directed graphs.
Only `shortestpath` accepts weights other than one. Invalid input calls `padError`.

- `toposort`: stable Kahn ordering. `status='cycle'` includes a closed cycle witness
  of real edges and the successfully ordered prefix; this is not a valid full order.
- `components`: weakly connected components for directed graphs, connected components
  otherwise. Members and component order follow node order.
- `shortestpath`: Dijkstra with finite nonnegative native-number weights. `source`
  is required; optional `target` returns one path, or `status='unreachable'`.
  `distances` includes all nodes with null distance/parent for unreachable nodes.
- `criticalpath`: DAG durations from nodes, default zero. Returns total duration,
  one duration-achieving path, and earliest start/finish, latest start, slack and
  critical flags per task. Critical tolerance is returned explicitly as
  `max(1,totalDuration)*1e-10`. Cycle results contain no scheduling numbers.

Work is bounded by O(V²+E) for order/shortest paths and O(V+E) for components;
critical path adds two DAG passes. Overflow fails. This does not schedule calendars,
resources or dates. Drawing stays in charts/diagrams.

`/showcase/?analysis/dependencies` computes a task DAG and colours critical tasks
in the existing Gantt chart.
