// Simplified Topic Display - Testing braces
function TopicDisplay() {
  const topicElem = document.getElementById('topic');
  // getAttribute('data'), not dataset.data - a plain data attribute is no data-* one, so
  // dataset.data is undefined and JSON.parse throws (docs/REACT.md)
  const topic = JSON.parse(topicElem.getAttribute('data'));

  return (
    <div style={{ padding: '20px', background: '#f0f8ff', borderRadius: '8px' }}>
      <h1>{topic.title}</h1>
      <p>Views: {topic.views}</p>
      <p>Created: {topic.created_at}</p>
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById('topic-display'));
root.render(<TopicDisplay />);
