// Tickets List Component
function TicketsList() {
  const elem = document.getElementById('tickets-list');
  const userId = elem.dataset.userId;
  const role = elem.dataset.role;

  const [tickets, setTickets] = React.useState([]);
  const [loading, setLoading] = React.useState(true);
  const [filter, setFilter] = React.useState('all');

  React.useEffect(() => {
    // In production, fetch user's tickets
    setTimeout(() => {
      setTickets([]);
      setLoading(false);
    }, 300);
  }, [userId]);

  const getStatusBadge = (status) => {
    const statusMap = {
      'open': { color: '#3498db', label: 'Open' },
      'in_progress': { color: '#f39c12', label: 'In Progress' },
      'resolved': { color: '#2ecc71', label: 'Resolved' },
      'closed': { color: '#95a5a6', label: 'Closed' }
    };
    const s = statusMap[status] || statusMap['open'];
    return <span className="badge" style={{backgroundColor: s.color}}>{s.label}</span>;
  };

  const getTypeBadge = (type) => {
    const typeMap = {
      'bug': '🐛',
      'feature': '✨',
      'question': '❓'
    };
    return typeMap[type] || '📄';
  };

  if (loading) {
    return <div className="loading">Loading tickets...</div>;
  }

  if (tickets.length === 0) {
    return (
      <div className="empty-state">
        <p>You haven't submitted any tickets yet.</p>
        <p>Create your first ticket to report bugs or request features!</p>
      </div>
    );
  }

  return (
    <div className="tickets-container">
      <div className="filter-buttons">
        <button
          className={`btn ${filter === 'all' ? 'btn-primary' : 'btn-secondary'}`}
          onClick={() => setFilter('all')}
        >
          All
        </button>
        <button
          className={`btn ${filter === 'open' ? 'btn-primary' : 'btn-secondary'}`}
          onClick={() => setFilter('open')}
        >
          Open
        </button>
        <button
          className={`btn ${filter === 'closed' ? 'btn-primary' : 'btn-secondary'}`}
          onClick={() => setFilter('closed')}
        >
          Closed
        </button>
      </div>

      <div className="tickets-list">
        {tickets.map(ticket => (
          <div key={ticket.id} className="ticket-item card">
            <div className="ticket-header">
              <h3>
                <span className="ticket-icon">{getTypeBadge(ticket.type)}</span>
                <a href={`?tickets/view&id=${ticket.id}`}>
                  #{ticket.id} {ticket.title}
                </a>
              </h3>
              {getStatusBadge(ticket.status)}
            </div>
            <p className="meta">
              Created: {ticket.created_at} • Priority: {ticket.priority}
            </p>
          </div>
        ))}
      </div>
    </div>
  );
}

const ticketsRoot = ReactDOM.createRoot(document.getElementById('tickets-list'));
ticketsRoot.render(<TicketsList />);
