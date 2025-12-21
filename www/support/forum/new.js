// New Topic/Post Form Component
function NewTopicForm() {
  const elem = document.getElementById('new-form');
  const isReply = elem.dataset.isReply === 'true';
  const boardId = elem.dataset.boardId;
  const topicId = elem.dataset.topicId;

  const actionUrl = isReply
    ? `?forum/new&topic_id=${topicId}`
    : `?forum/new&board_id=${boardId}`;

  return (
    <div className="card">
      <form method="POST" action={actionUrl}>
        {!isReply && (
          <div className="form-group">
            <label htmlFor="title">Topic Title</label>
            <input
              type="text"
              id="title"
              name="title"
              placeholder="Enter topic title"
              required
            />
          </div>
        )}

        <div className="form-group">
          <label htmlFor="content">{isReply ? 'Reply' : 'Message'}</label>
          <textarea
            id="content"
            name="content"
            placeholder={isReply ? 'Write your reply...' : 'Write your message...'}
            rows="10"
            required
          ></textarea>
        </div>

        <div className="form-actions">
          <button type="submit" className="btn btn-primary">
            {isReply ? 'Post Reply' : 'Create Topic'}
          </button>
          <a href={isReply ? `?forum/topic&id=${topicId}` : `?forum/board&board_id=${boardId}`} className="btn btn-secondary">
            Cancel
          </a>
        </div>
      </form>
    </div>
  );
}

const formRoot = ReactDOM.createRoot(document.getElementById('new-form'));
formRoot.render(<NewTopicForm />);
