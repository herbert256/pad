// Ticket Details Component
function TicketDetails() {
  const elem = document.getElementById('ticket-details');
  const ticket = {
    id: elem.dataset.id,
    title: elem.dataset.title,
    type: elem.dataset.type,
    status: elem.dataset.status,
    priority: elem.dataset.priority
  };
  const role = elem.dataset.role;
  const userId = elem.dataset.userId;

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
      'bug': '🐛 Bug',
      'feature': '✨ Feature',
      'question': '❓ Question'
    };
    return typeMap[type] || '📄 Ticket';
  };

  return (
    <div className="ticket-details card">
      <div className="ticket-header">
        <h2>
          <span className="ticket-type-badge">{getTypeBadge(ticket.type)}</span>
          #{ticket.id}
        </h2>
        {getStatusBadge(ticket.status)}
      </div>

      <div className="ticket-meta">
        <div className="meta-item">
          <strong>Priority:</strong>
          <span className={`priority-${ticket.priority}`}>{ticket.priority}</span>
        </div>
      </div>

      {role === 'admin' && (
        <div className="admin-actions">
          <h4>Admin Actions</h4>
          <form method="POST" action={`?tickets/view&id=${ticket.id}`}>
            <select name="status" className="form-control">
              <option value="open" selected={ticket.status === 'open'}>Open</option>
              <option value="in_progress" selected={ticket.status === 'in_progress'}>In Progress</option>
              <option value="resolved" selected={ticket.status === 'resolved'}>Resolved</option>
              <option value="closed" selected={ticket.status === 'closed'}>Closed</option>
            </select>
            <button type="submit" className="btn btn-primary">Update Status</button>
          </form>
        </div>
      )}
    </div>
  );
}

const detailsRoot = ReactDOM.createRoot(document.getElementById('ticket-details'));
detailsRoot.render(<TicketDetails />);
