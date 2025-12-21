// Topic Posts Component
function TopicPosts() {
  const elem = document.getElementById('topic-posts');
  const topicId = elem.dataset.topicId;
  const userId = elem.dataset.userId;
  const loggedIn = elem.dataset.loggedIn === 'true';

  const [posts, setPosts] = React.useState([]);
  const [loading, setLoading] = React.useState(true);
  const [newPost, setNewPost] = React.useState('');

  React.useEffect(() => {
    // In production, fetch posts for this topic
    setTimeout(() => {
      setPosts([]);
      setLoading(false);
    }, 300);
  }, [topicId]);

  if (loading) {
    return <div className="loading">Loading posts...</div>;
  }

  return (
    <div className="topic-posts">
      {posts.length === 0 ? (
        <div className="empty-state card">
          <p>No posts yet.</p>
        </div>
      ) : (
        posts.map((post, index) => (
          <div key={post.id} className="post-item card">
            <div className="post-header">
              <strong>{post.username}</strong>
              <span className="meta">{post.created_at}</span>
            </div>
            <div className="post-content">{post.content}</div>
          </div>
        ))
      )}

      {loggedIn ? (
        <div className="card">
          <h3>Reply to this topic</h3>
          <form method="POST" action={`?forum/new&topic_id=${topicId}`}>
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
      ) : (
        <div className="info-message">
          Please <a href="?auth/login">login</a> to reply to this topic.
        </div>
      )}
    </div>
  );
}

const postsRoot = ReactDOM.createRoot(document.getElementById('topic-posts'));
postsRoot.render(<TopicPosts />);
