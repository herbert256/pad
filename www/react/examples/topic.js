// The forum topic: four mount points written by {reactData} - topic, board, user, posts -
// each a <div id="..." data="JSON">. The attribute is a plain data, not a data-* one, so it
// is read with getAttribute('data'): dataset.data is undefined.

function readMount(id) {
  return JSON.parse(document.getElementById(id).getAttribute('data'));
}

function formatWhen(text) {
  return new Date(text.replace(' ', 'T')).toLocaleString('en', { dateStyle: 'medium', timeStyle: 'short' });
}

function TopicDisplay() {
  const topic = readMount('topic');
  const board = readMount('board');
  const user = readMount('user');
  const posts = readMount('posts');

  const [open, setOpen] = React.useState(null);
  const [liked, setLiked] = React.useState({});

  return (
    <div>
      <div className="crumbs" style={{ marginBottom: 10 }}>
        <span>📚 Forum</span><span>/</span><span>{board.name}</span>
      </div>

      <div className="card" style={{ borderTop: `4px solid ${topic.is_locked ? 'var(--bad)' : topic.is_pinned ? 'var(--warn)' : 'var(--pad)'}` }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', gap: 12, flexWrap: 'wrap' }}>
          <h2 style={{ margin: 0 }}>{topic.title}</h2>
          <div className="chips">
            {topic.is_pinned === 1 && <span className="badge">📌 pinned</span>}
            {topic.is_locked === 1 && <span className="badge is-bad">🔒 locked</span>}
          </div>
        </div>
        <div className="chips" style={{ margin: '12px 0' }}>
          <span className="badge is-pad">👤 {user.username}{user.role === 'admin' ? ' · admin' : ''}</span>
          <span className="badge">📅 {formatWhen(topic.created_at)}</span>
          <span className="badge">👁 {topic.views} views</span>
        </div>
        <div className="panel">
          <div className="panel-title">Board</div>
          <strong>{board.name}</strong>
          <div className="muted small">{board.description}</div>
        </div>
      </div>

      <h3 style={{ margin: '24px 0 12px' }}>💬 {posts.length} {posts.length === 1 ? 'post' : 'posts'}</h3>

      {posts.length === 0 && <div className="empty-state"><span className="big">💭</span>No posts yet.</div>}

      <div style={{ display: 'grid', gap: 12 }}>
        {posts.map((post, index) => (
          <div key={post.id} className="card rise" style={{ animationDelay: `${index * 80}ms`, cursor: 'pointer' }}
               onClick={() => setOpen(open === post.id ? null : post.id)}>
            <div style={{ display: 'flex', gap: 14 }}>
              <Avatar name={post.username} hue={(post.user_id * 67) % 360} />
              <div style={{ flex: 1, minWidth: 0 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', gap: 10, flexWrap: 'wrap' }}>
                  <div>
                    <strong>{post.username}</strong>{' '}
                    {/* user is the topic's author (the user_from_topic provider), not whoever
                        is looking - the page has no notion of a viewer - so the badge marks
                        the author's posts */}
                    {post.user_id === user.id && <span className="badge is-react">author</span>}
                  </div>
                  <span className="muted small">#{index + 1} &middot; {formatWhen(post.created_at)}</span>
                </div>
                <p style={{ margin: '8px 0 0' }}>{post.content}</p>
                {open === post.id && (
                  <div className="panel rise small" style={{ marginTop: 10 }}>
                    post {post.id} &middot; user {post.user_id} &middot; updated {formatWhen(post.updated_at)}
                  </div>
                )}
              </div>
              <button className="btn-sm btn-ghost" style={{ alignSelf: 'start' }}
                      onClick={event => { event.stopPropagation(); setLiked({ ...liked, [post.id]: !liked[post.id] }); }}>
                {liked[post.id] ? '❤️' : '🤍'}
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('topic-display')).render(<TopicDisplay />);
