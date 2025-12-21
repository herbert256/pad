// Latest News Component (embedded in homepage)
function LatestNews() {
  const [news, setNews] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    // Simulate loading - in reality this would fetch from server
    // For now, we'll use a simple delay since data is rendered server-side
    setTimeout(() => {
      setLoading(false);
    }, 300);
  }, []);

  if (loading) {
    return <div className="loading">Loading latest news...</div>;
  }

  return (
    <div className="news-preview">
      <p className="info-message">
        📰 Check out the <a href="?news/index">News section</a> for the latest updates!
      </p>
    </div>
  );
}

const newsRoot = ReactDOM.createRoot(document.getElementById('latest-news'));
newsRoot.render(<LatestNews />);
