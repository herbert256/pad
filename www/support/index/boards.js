// Forum Boards Preview Component
function ForumBoardsPreview() {
  return (
    <div className="boards-preview">
      <p className="info-message">
        💬 Visit the <a href="?forum/index">Forum</a> to join discussions and get help from the community!
      </p>
    </div>
  );
}

const boardsRoot = ReactDOM.createRoot(document.getElementById('forum-boards'));
boardsRoot.render(<ForumBoardsPreview />);
