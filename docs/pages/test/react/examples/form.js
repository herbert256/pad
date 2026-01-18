function FormExample() {
  const [name, setName] = React.useState('');
  const [email, setEmail] = React.useState('');
  const [submitted, setSubmitted] = React.useState(false);

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ CONTROLLED FORM</div>
      {!submitted ? (
        <form onSubmit={handleSubmit}>
          <div style={{marginBottom: '10px'}}>
            <label>
              Name:
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                style={{marginLeft: '10px', padding: '5px', borderRadius: '3px', border: '1px solid #ccc'}}
                required
              />
            </label>
          </div>
          <div style={{marginBottom: '10px'}}>
            <label>
              Email:
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                style={{marginLeft: '10px', padding: '5px', borderRadius: '3px', border: '1px solid #ccc'}}
                required
              />
            </label>
          </div>
          <button type="submit">Submit</button>
        </form>
      ) : (
        <div style={{padding: '15px', background: '#d4edda', borderRadius: '5px'}}>
          <strong>Form submitted!</strong>
          <p>Name: {name}</p>
          <p>Email: {email}</p>
          <button onClick={() => setSubmitted(false)} style={{marginTop: '10px'}}>
            Reset Form
          </button>
        </div>
      )}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example2')).render(<FormExample />);
