// New Ticket Form Component
function NewTicketForm() {
  return (
    <div className="card">
      <form method="POST" action="?tickets/new">
        <div className="form-group">
          <label htmlFor="type">Ticket Type</label>
          <select id="type" name="type" required>
            <option value="bug">🐛 Bug Report</option>
            <option value="feature">✨ Feature Request</option>
            <option value="question">❓ Question</option>
          </select>
        </div>

        <div className="form-group">
          <label htmlFor="title">Title</label>
          <input
            type="text"
            id="title"
            name="title"
            placeholder="Brief description of the issue"
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="description">Description</label>
          <textarea
            id="description"
            name="description"
            placeholder="Provide detailed information about your ticket..."
            rows="10"
            required
          ></textarea>
        </div>

        <div className="form-group">
          <label htmlFor="priority">Priority</label>
          <select id="priority" name="priority" required>
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
          </select>
        </div>

        <div className="form-actions">
          <button type="submit" className="btn btn-primary">
            Submit Ticket
          </button>
          <a href="?tickets/index" className="btn btn-secondary">
            Cancel
          </a>
        </div>
      </form>
    </div>
  );
}

const formRoot = ReactDOM.createRoot(document.getElementById('new-ticket-form'));
formRoot.render(<NewTicketForm />);
