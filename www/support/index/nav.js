// Navigation Component
function Navigation() {
  const elem = document.getElementById('nav-component');
  const navData = JSON.parse(elem.dataset.nav);
  const currentPage = elem.dataset.currentPage || 'index';
  const isLoggedIn = elem.dataset.loggedIn === 'true';

  const visibleItems = navData.filter(item => {
    if (item.hideWhenLoggedIn && isLoggedIn) return false;
    if (!item.public && !isLoggedIn) return false;
    return true;
  });

  return (
    <nav className="navbar">
      <div className="container">
        <div className="nav-brand">
          <a href="?index">🎯 PAD Support</a>
        </div>
        <ul className="nav-menu">
          {visibleItems.map((item, index) => (
            <li key={index}>
              <a
                href={`?${item.page}`}
                className={currentPage === item.page ? 'active' : ''}
              >
                {item.icon} {item.label}
              </a>
            </li>
          ))}
        </ul>
      </div>
    </nav>
  );
}

const navRoot = ReactDOM.createRoot(document.getElementById('nav-component'));
navRoot.render(<Navigation />);
