// News Article Header Component
function NewsArticleHeader() {
  const elem = document.getElementById('news-article');
  const article = {
    id: elem.dataset.id,
    title: elem.dataset.title,
    date: elem.dataset.date,
    userId: elem.dataset.userId
  };

  return (
    <div className="article-header card">
      <div className="article-meta">
        <span className="badge">📰 News Article #{article.id}</span>
        <span className="meta">Published: {article.date}</span>
      </div>
    </div>
  );
}

const articleRoot = ReactDOM.createRoot(document.getElementById('news-article'));
articleRoot.render(<NewsArticleHeader />);
