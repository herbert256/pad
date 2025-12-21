// Ticket Comments Component
function TicketComments() {
  const elem = document.getElementById('ticket-comments');
  const ticketId = elem.dataset.ticketId;
  const userId = elem.dataset.userId;
  const role = elem.dataset.role;

  const [comments, setComments] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    // In production, fetch comments for this ticket
    setTimeout(() => {
      setComments([]);
      setLoading(false);
    }, 300);
  }, [ticketId]);

  if (loading) {
    return <div className="loading">Loading comments...</div>;
  }

  return (
    <div className="ticket-comments">
      <h3>Comments</h3>

      {comments.length === 0 ? (
        <div className="empty-state card">
          <p>No comments yet. Be the first to comment!</p>
        </div>
      ) : (
        comments.map(comment => (
          <div key={comment.id} className="comment-item card">
            <div className="comment-header">
              <strong>{comment.username}</strong>
              {comment.user_id === userId && <span className="badge">You</span>}
              {role === 'admin' && <span className="badge badge-admin">Admin</span>}
              <span className="meta">{comment.created_at}</span>
            </div>
            <div className="comment-content">{comment.content}</div>
          </div>
        ))
      )}

      <div className="card">
        <h4>Add Comment</h4>
        <form method="POST" action={`?tickets/view&id=${ticketId}`}>
          <textarea
            name="comment"
            placeholder="Write your comment..."
            rows="4"
            required
          ></textarea>
          <button type="submit" className="btn btn-primary">
            Post Comment
          </button>
        </form>
      </div>
    </div>
  );
}

const commentsRoot = ReactDOM.createRoot(document.getElementById('ticket-comments'));
commentsRoot.render(<TicketComments />);
