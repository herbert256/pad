// Stats Dashboard Component
function StatsDashboard() {
  const elem = document.getElementById('stats-dashboard');
  const stats = {
    users: elem.dataset.userCount,
    topics: elem.dataset.topicCount,
    posts: elem.dataset.postCount,
    news: elem.dataset.newsCount,
    tickets: elem.dataset.ticketCount,
    serverTime: elem.dataset.serverTime
  };

  const [animatedStats, setAnimatedStats] = React.useState({
    users: 0,
    topics: 0,
    posts: 0,
    news: 0,
    tickets: 0
  });

  React.useEffect(() => {
    // Animate counting up
    const duration = 1000;
    const steps = 50;
    const interval = duration / steps;

    let currentStep = 0;
    const timer = setInterval(() => {
      currentStep++;
      const progress = currentStep / steps;

      setAnimatedStats({
        users: Math.floor(stats.users * progress),
        topics: Math.floor(stats.topics * progress),
        posts: Math.floor(stats.posts * progress),
        news: Math.floor(stats.news * progress),
        tickets: Math.floor(stats.tickets * progress)
      });

      if (currentStep >= steps) {
        clearInterval(timer);
        setAnimatedStats({
          users: parseInt(stats.users),
          topics: parseInt(stats.topics),
          posts: parseInt(stats.posts),
          news: parseInt(stats.news),
          tickets: parseInt(stats.tickets)
        });
      }
    }, interval);

    return () => clearInterval(timer);
  }, []);

  return (
    <div className="stats-grid">
      <div className="stat-card">
        <div className="stat-icon">👥</div>
        <div className="stat-value">{animatedStats.users}</div>
        <div className="stat-label">Members</div>
      </div>
      <div className="stat-card">
        <div className="stat-icon">💬</div>
        <div className="stat-value">{animatedStats.topics}</div>
        <div className="stat-label">Topics</div>
      </div>
      <div className="stat-card">
        <div className="stat-icon">📝</div>
        <div className="stat-value">{animatedStats.posts}</div>
        <div className="stat-label">Posts</div>
      </div>
      <div className="stat-card">
        <div className="stat-icon">📰</div>
        <div className="stat-value">{animatedStats.news}</div>
        <div className="stat-label">News</div>
      </div>
      <div className="stat-card">
        <div className="stat-icon">🎫</div>
        <div className="stat-value">{animatedStats.tickets}</div>
        <div className="stat-label">Tickets</div>
      </div>
    </div>
  );
}

const statsRoot = ReactDOM.createRoot(document.getElementById('stats-dashboard'));
statsRoot.render(<StatsDashboard />);
