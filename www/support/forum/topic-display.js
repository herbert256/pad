// Topic Display Component using {reactData} pattern
function TopicDisplay() {
  // Get topic data from {reactData} tag
  const topicElem = document.getElementById('topic-header');
  const topic = JSON.parse(topicElem.dataset.data);

  // Get posts data from {reactData} tag
  const postsElem = document.getElementById('topic-posts');
  const posts = JSON.parse(postsElem.dataset.data);

  // Get user context
  const displayElem = document.getElementById('topic-display');
  const userId = displayElem.dataset.userId;
  const loggedIn = displayElem.dataset.loggedIn === 'true';

  return (
    <div className="topic-container">
      {/* Topic Header */}
      <div className="card">
        <div className="topic-meta">
          <span className="badge">Topic #{topic.id}</span>
          <span className="meta">Views: {topic.views}</span>
          <span className="meta">
            Created: {topic.created_at.substring(0, 10)}
          </span>
          {topic.is_pinned === 1 && (
            <span className="badge" style={{backgroundColor: '#f39c12'}}>
              📌 Pinned
            </span>
          )}
          {topic.is_locked === 1 && (
            <span className="badge" style={{backgroundColor: '#95a5a6'}}>
              🔒 Locked
            </span>
          )}
        </div>
      </div>

      {/* Posts List */}
      <div className="posts-list">
        {posts.length === 0 ? (
          <div className="empty-state card">
            <p>No posts yet in this topic.</p>
          </div>
        ) : (
          posts.map((post, index) => (
            <div key={post.id} className="post-item card">
              <div className="post-header">
                <strong>{post.username}</strong>
                {post.user_id === parseInt(userId) && (
                  <span className="badge">You</span>
                )}
                <span className="meta">
                  {post.created_at.substring(0, 16)}
                </span>
              </div>
              <div className="post-content">{post.content}</div>
            </div>
          ))
        )}
      </div>

      {/* Reply Form */}
      {loggedIn && topic.is_locked !== 1 ? (
        <div className="card">
          <h3>Reply to this topic</h3>
          <form method="POST" action={`?forum/new&topic_id=${topic.id}`}>
            <textarea
              name="content"
              placeholder="Write your reply..."
              rows="6"
              required
            ></textarea>
            <button type="submit" className="btn btn-primary">
              Post Reply
            </button>
          </form>
        </div>
      ) : topic.is_locked === 1 ? (
        <div className="info-message">
          🔒 This topic is locked. No new replies can be posted.
        </div>
      ) : (
        <div className="info-message">
          Please <a href="?auth/login">login</a> to reply to this topic.
        </div>
      )}
    </div>
  );
}

const displayRoot = ReactDOM.createRoot(document.getElementById('topic-display'));
displayRoot.render(<TopicDisplay />);
