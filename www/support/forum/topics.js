// Topics List Component
function TopicsList() {
  const elem = document.getElementById('topics-list');
  const boardId = elem.dataset.boardId;
  const loggedIn = elem.dataset.loggedIn === 'true';

  const [topics, setTopics] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    // In production, fetch topics for this board
    setTimeout(() => {
      setTopics([]);
      setLoading(false);
    }, 300);
  }, [boardId]);

  if (loading) {
    return <div className="loading">Loading topics...</div>;
  }

  if (topics.length === 0) {
    return (
      <div className="empty-state">
        <p>No topics yet in this board.</p>
        {loggedIn && <p>Be the first to start a discussion!</p>}
      </div>
    );
  }

  return (
    <div className="topics-list">
      {topics.map(topic => (
        <div key={topic.id} className="topic-item card">
          <h3>
            <a href={`?forum/topic&id=${topic.id}`}>{topic.title}</a>
          </h3>
          <p className="meta">
            Posted by {topic.username} • {topic.created_at} • {topic.views} views
          </p>
        </div>
      ))}
    </div>
  );
}

const topicsRoot = ReactDOM.createRoot(document.getElementById('topics-list'));
topicsRoot.render(<TopicsList />);
