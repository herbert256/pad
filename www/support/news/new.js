// New News Article Form Component
function NewArticleForm() {
  return (
    <div className="card">
      <form method="POST" action="?news/new">
        <div className="form-group">
          <label htmlFor="title">Article Title</label>
          <input
            type="text"
            id="title"
            name="title"
            placeholder="Enter article title"
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="content">Content</label>
          <textarea
            id="content"
            name="content"
            placeholder="Write your article content..."
            rows="15"
            required
          ></textarea>
        </div>

        <div className="form-actions">
          <button type="submit" className="btn btn-primary">
            Publish Article
          </button>
          <a href="?news/index" className="btn btn-secondary">
            Cancel
          </a>
        </div>
      </form>
    </div>
  );
}

const formRoot = ReactDOM.createRoot(document.getElementById('new-article-form'));
formRoot.render(<NewArticleForm />);
