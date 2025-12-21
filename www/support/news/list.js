// News List Component
function NewsList() {
  const elem = document.getElementById('news-list');
  const isAdmin = elem.dataset.isAdmin === 'true';

  const [news, setNews] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    // In production, fetch news articles
    setTimeout(() => {
      setNews([]);
      setLoading(false);
    }, 300);
  }, []);

  if (loading) {
    return <div className="loading">Loading news...</div>;
  }

  if (news.length === 0) {
    return (
      <div className="empty-state">
        <p>No news articles yet.</p>
        {isAdmin && <p>Create the first article!</p>}
      </div>
    );
  }

  return (
    <div className="news-list">
      {news.map(article => (
        <div key={article.id} className="news-item card">
          <h3>
            <a href={`?news/view&id=${article.id}`}>{article.title}</a>
          </h3>
          <p className="meta">
            {article.created_at} • by {article.username}
          </p>
          <p>{article.content.substring(0, 200)}...</p>
          <a href={`?news/view&id=${article.id}`} className="btn btn-secondary">
            Read More
          </a>
        </div>
      ))}
    </div>
  );
}

const newsRoot = ReactDOM.createRoot(document.getElementById('news-list'));
newsRoot.render(<NewsList />);
