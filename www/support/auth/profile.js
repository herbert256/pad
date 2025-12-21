// Profile Component
function Profile() {
  const elem = document.getElementById('profile-info');
  const profile = {
    username: elem.dataset.username,
    email: elem.dataset.email,
    role: elem.dataset.role,
    created: elem.dataset.created,
    topics: elem.dataset.topics,
    posts: elem.dataset.posts,
    tickets: elem.dataset.tickets
  };

  const getRoleBadge = (role) => {
    if (role === 'admin') {
      return <span className="badge" style={{backgroundColor: '#e74c3c'}}>👑 Admin</span>;
    }
    return <span className="badge" style={{backgroundColor: '#3498db'}}>👤 User</span>;
  };

  return (
    <div className="profile-container">
      <div className="card">
        <div className="profile-header">
          <div className="profile-avatar">
            {profile.username.charAt(0).toUpperCase()}
          </div>
          <div className="profile-info">
            <h2>{profile.username}</h2>
            {getRoleBadge(profile.role)}
          </div>
        </div>

        <div className="profile-details">
          <div className="detail-item">
            <strong>Email:</strong> {profile.email}
          </div>
          <div className="detail-item">
            <strong>Member Since:</strong> {profile.created}
          </div>
        </div>
      </div>

      <div className="card">
        <h3>Activity Statistics</h3>
        <div className="stats-grid">
          <div className="stat-item">
            <div className="stat-icon">💬</div>
            <div className="stat-value">{profile.topics}</div>
            <div className="stat-label">Topics Created</div>
          </div>
          <div className="stat-item">
            <div className="stat-icon">📝</div>
            <div className="stat-value">{profile.posts}</div>
            <div className="stat-label">Posts</div>
          </div>
          <div className="stat-item">
            <div className="stat-icon">🎫</div>
            <div className="stat-value">{profile.tickets}</div>
            <div className="stat-label">Tickets</div>
          </div>
        </div>
      </div>

      <div className="card">
        <h3>Quick Actions</h3>
        <ul className="quick-actions-list">
          <li>
            <a href="?forum/index" className="btn btn-secondary">
              💬 Browse Forum
            </a>
          </li>
          <li>
            <a href="?tickets/index" className="btn btn-secondary">
              🎫 My Tickets
            </a>
          </li>
          <li>
            <a href="?tickets/new" className="btn btn-primary">
              ➕ New Ticket
            </a>
          </li>
        </ul>
      </div>
    </div>
  );
}

const profileRoot = ReactDOM.createRoot(document.getElementById('profile-info'));
profileRoot.render(<Profile />);
