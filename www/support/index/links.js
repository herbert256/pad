// Quick Links Component
function QuickLinks() {
  const elem = document.getElementById('quick-links');
  const loggedIn = elem.dataset.loggedIn === 'true';
  const links = JSON.parse(elem.dataset.links);

  const visibleLinks = links.filter(link => {
    if (link.hideWhenLoggedIn && loggedIn) return false;
    if (!link.public && !loggedIn) return false;
    return true;
  });

  return (
    <ul className="quick-links-list">
      {visibleLinks.map((link, index) => (
        <li key={index}>
          <a href={link.url}>
            <span className="link-icon">{link.icon}</span>
            <span className="link-text">
              <strong>{link.title}</strong>
              <br />
              <small>{link.description}</small>
            </span>
          </a>
        </li>
      ))}
    </ul>
  );
}

const linksRoot = ReactDOM.createRoot(document.getElementById('quick-links'));
linksRoot.render(<QuickLinks />);
