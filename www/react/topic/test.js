// Simple test to verify React is working
console.log("Test script loaded!");
console.log("React available?", typeof React !== 'undefined');
console.log("ReactDOM available?", typeof ReactDOM !== 'undefined');

function SimpleTest() {
  return (
    <div style={{
      padding: '20px',
      background: 'lightblue',
      borderRadius: '8px',
      textAlign: 'center',
      fontSize: '24px'
    }}>
      ✅ React is working! The component rendered successfully!
    </div>
  );
}

const testDiv = document.getElementById('topic-display');
console.log("Found topic-display div?", testDiv !== null);

if (testDiv) {
  const root = ReactDOM.createRoot(testDiv);
  root.render(<SimpleTest />);
  console.log("Rendered SimpleTest component");
} else {
  console.error("Could not find topic-display div!");
}
