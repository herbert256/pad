// Forum Boards List Component
function ForumBoardsList() {
  const elem = document.getElementById('forum-boards-list');
  const boards = JSON.parse(elem.dataset.boards);

  return (
    <div className="boards-list">
      {boards.map(board => (
        <div key={board.id} className="board-item card">
          <h3>
            <a href={`?forum/board&slug=${board.slug}`}>{board.name}</a>
          </h3>
          <p className="meta">{board.description}</p>
        </div>
      ))}
    </div>
  );
}

const boardsRoot = ReactDOM.createRoot(document.getElementById('forum-boards-list'));
boardsRoot.render(<ForumBoardsList />);
