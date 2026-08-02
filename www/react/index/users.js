// Component that uses server data
function UsersComponent() {
  // Get server data from window object
  const serverMessage = window.serverData.serverMessage;
  const serverTime = window.serverData.serverTime;
  const users = window.serverData.users;

  const [selectedUser, setSelectedUser] = React.useState(null);

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ REACT COMPONENT (with server data)</div>
      <h3>User List from Server</h3>
      <p style={{fontSize: '14px', color: '#666'}}>
        Server message: "{serverMessage}" (generated at {serverTime})
      </p>

      <div style={{marginTop: '15px'}}>
        {users.map(user => (
          <div
            key={user.id}
            style={{
              padding: '10px',
              margin: '5px 0',
              background: selectedUser?.id === user.id ? '#61dafb' : '#f0f0f0',
              borderRadius: '5px',
              cursor: 'pointer',
              transition: 'background 0.2s'
            }}
            onClick={() => setSelectedUser(user)}
          >
            <strong>{user.name}</strong> - {user.role}
          </div>
        ))}
      </div>

      {selectedUser && (
        <div style={{marginTop: '15px', padding: '15px', background: '#e3f2fd', borderRadius: '5px'}}>
          <strong>Selected:</strong> {selectedUser.name} ({selectedUser.role})
        </div>
      )}
    </div>
  );
}

// Render the component
const usersRoot = ReactDOM.createRoot(document.getElementById('users-component'));
usersRoot.render(<UsersComponent />);
