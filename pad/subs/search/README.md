# Search subsystem

The existing BM25F index, stemming, query phrases and prefix search remain shared across language kinds.

## Exact filters and facets

`padSearch($rows, $query, ['fields'=>'title,body', 'filters'=>['category'=>'guide', 'status'=>['open','closed']], 'facets'=>'category,status'])` filters the corpus before indexing and ranking, so excluded documents do not affect BM25 statistics. A filter map can also be a JSON object string, such as `filters => '{"category":"guide","status":["open","closed"]}'`, for template options. Filters use exact typed equality: `1` differs from `'1'`. Fields are combined with AND, allowed values within a field with OR. A list-valued document field matches any item; a missing field does not equal explicit null. An empty allowed-value list matches nothing. Dotted field paths work as with normal search fields.

`facets` is a comma-separated string or array of field names. After the query, `padSearchInfo()` returns `['total'=>N, 'facets'=>['category'=>[['value'=>'guide','count'=>3], ...]]]`. Counts cover all matching documents after exact filtering and before `first` or template paging. Repeated list values count once per document. Typed values remain distinct; buckets are sorted deterministically by their textual value then type. Missing and non-scalar values are omitted. Every search resets metadata, including searches with no hits.

Current filter metadata is loaded before searching even when the searchable text is unchanged. Filter-specific indexes use separate cache identities. These filters do not substitute for application authorization; choose the source rows appropriate to the caller before searching.
