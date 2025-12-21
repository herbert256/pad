// Login Form Component
function LoginForm() {
  return (
    <div className="auth-form card">
      <form method="POST" action="?auth/login">
        <div className="form-group">
          <label htmlFor="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter your username"
            required
            autoFocus
          />
        </div>

        <div className="form-group">
          <label htmlFor="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
          />
        </div>

        <div className="form-actions">
          <button type="submit" className="btn btn-primary btn-block">
            🔐 Login
          </button>
        </div>
      </form>
    </div>
  );
}

const loginRoot = ReactDOM.createRoot(document.getElementById('login-form'));
loginRoot.render(<LoginForm />);
