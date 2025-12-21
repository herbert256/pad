// Register Form Component
function RegisterForm() {
  const [password, setPassword] = React.useState('');
  const [confirm, setConfirm] = React.useState('');
  const [passwordMatch, setPasswordMatch] = React.useState(true);

  const handlePasswordChange = (e) => {
    setPassword(e.target.value);
    if (confirm) {
      setPasswordMatch(e.target.value === confirm);
    }
  };

  const handleConfirmChange = (e) => {
    setConfirm(e.target.value);
    setPasswordMatch(password === e.target.value);
  };

  return (
    <div className="auth-form card">
      <form method="POST" action="?auth/register">
        <div className="form-group">
          <label htmlFor="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="Choose a username"
            required
            autoFocus
          />
        </div>

        <div className="form-group">
          <label htmlFor="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="your.email@example.com"
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Choose a strong password"
            value={password}
            onChange={handlePasswordChange}
            required
          />
        </div>

        <div className="form-group">
          <label htmlFor="confirm">Confirm Password</label>
          <input
            type="password"
            id="confirm"
            name="confirm"
            placeholder="Re-enter your password"
            value={confirm}
            onChange={handleConfirmChange}
            required
          />
          {!passwordMatch && confirm && (
            <small className="error-text">Passwords do not match</small>
          )}
        </div>

        <div className="form-actions">
          <button
            type="submit"
            className="btn btn-primary btn-block"
            disabled={!passwordMatch}
          >
            📝 Create Account
          </button>
        </div>
      </form>
    </div>
  );
}

const registerRoot = ReactDOM.createRoot(document.getElementById('register-form'));
registerRoot.render(<RegisterForm />);
