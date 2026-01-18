// Fancy Topic Display Component
function TopicDisplay() {
  // Get data from all 4 reactData divs
  // IMPORTANT: Use getAttribute('data') not dataset.data
  const topicElem = document.getElementById('topic');
  const boardElem = document.getElementById('board');
  const userElem = document.getElementById('user');
  const postsElem = document.getElementById('posts');

  const topic = JSON.parse(topicElem.getAttribute('data'));
  const board = JSON.parse(boardElem.getAttribute('data'));
  const user = JSON.parse(userElem.getAttribute('data'));
  const posts = JSON.parse(postsElem.getAttribute('data'));

  const [selectedPost, setSelectedPost] = React.useState(null);

  // Format date
  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  // Get status badge color
  const getStatusColor = () => {
    if (topic.is_locked) return '#e74c3c';
    if (topic.is_pinned) return '#f39c12';
    return '#3498db';
  };

  return (
    <div style={{
      maxWidth: '1000px',
      margin: '0 auto',
      animation: 'fadeIn 0.5s ease-in'
    }}>
      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
          from { opacity: 0; transform: translateX(-20px); }
          to { opacity: 1; transform: translateX(0); }
        }
        .post-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
      `}</style>

      {/* Breadcrumb */}
      <div style={{
        padding: '12px 20px',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: 'white',
        borderRadius: '8px',
        marginBottom: '20px',
        fontSize: '14px',
        display: 'flex',
        alignItems: 'center',
        gap: '8px'
      }}>
        <span style={{ opacity: 0.8 }}>📚 Forum</span>
        <span style={{ opacity: 0.6 }}>›</span>
        <span style={{ opacity: 0.9 }}>{board.name}</span>
        <span style={{ opacity: 0.6 }}>›</span>
        <span style={{ fontWeight: 'bold' }}>{topic.title}</span>
      </div>

      {/* Topic Header Card */}
      <div style={{
        background: 'white',
        borderRadius: '12px',
        padding: '30px',
        marginBottom: '24px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.1)',
        border: `3px solid ${getStatusColor()}`
      }}>
        <div style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'start',
          marginBottom: '20px'
        }}>
          <div>
            <h1 style={{
              margin: '0 0 12px 0',
              fontSize: '32px',
              color: '#2c3e50',
              fontWeight: 'bold'
            }}>
              {topic.title}
            </h1>
            <div style={{
              display: 'flex',
              gap: '12px',
              flexWrap: 'wrap',
              alignItems: 'center'
            }}>
              <div style={{
                display: 'inline-flex',
                alignItems: 'center',
                gap: '8px',
                padding: '6px 12px',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                color: 'white',
                borderRadius: '20px',
                fontSize: '14px',
                fontWeight: '500'
              }}>
                👤 {user.username}
                {user.role === 'admin' && (
                  <span style={{
                    background: 'rgba(255,255,255,0.3)',
                    padding: '2px 8px',
                    borderRadius: '10px',
                    fontSize: '12px'
                  }}>ADMIN</span>
                )}
              </div>

              <div style={{
                padding: '6px 12px',
                background: '#ecf0f1',
                borderRadius: '20px',
                fontSize: '14px',
                color: '#7f8c8d'
              }}>
                📅 {formatDate(topic.created_at)}
              </div>

              <div style={{
                padding: '6px 12px',
                background: '#ecf0f1',
                borderRadius: '20px',
                fontSize: '14px',
                color: '#7f8c8d'
              }}>
                👁 {topic.views} views
              </div>
            </div>
          </div>

          <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
            {topic.is_pinned === 1 && (
              <div style={{
                padding: '8px 16px',
                background: '#f39c12',
                color: 'white',
                borderRadius: '8px',
                fontSize: '14px',
                fontWeight: 'bold',
                textAlign: 'center'
              }}>
                📌 PINNED
              </div>
            )}
            {topic.is_locked === 1 && (
              <div style={{
                padding: '8px 16px',
                background: '#e74c3c',
                color: 'white',
                borderRadius: '8px',
                fontSize: '14px',
                fontWeight: 'bold',
                textAlign: 'center'
              }}>
                🔒 LOCKED
              </div>
            )}
          </div>
        </div>

        {/* Board Info */}
        <div style={{
          padding: '16px',
          background: '#f8f9fa',
          borderRadius: '8px',
          borderLeft: '4px solid #667eea'
        }}>
          <div style={{ fontSize: '12px', color: '#7f8c8d', marginBottom: '4px' }}>
            BOARD
          </div>
          <div style={{ fontSize: '16px', fontWeight: '600', color: '#2c3e50' }}>
            {board.name}
          </div>
          <div style={{ fontSize: '14px', color: '#7f8c8d', marginTop: '4px' }}>
            {board.description}
          </div>
        </div>
      </div>

      {/* Posts Section */}
      <div style={{
        background: 'white',
        borderRadius: '12px',
        padding: '30px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.1)'
      }}>
        <h2 style={{
          margin: '0 0 24px 0',
          fontSize: '24px',
          color: '#2c3e50',
          display: 'flex',
          alignItems: 'center',
          gap: '12px'
        }}>
          💬 Posts
          <span style={{
            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            color: 'white',
            padding: '4px 12px',
            borderRadius: '20px',
            fontSize: '16px'
          }}>
            {posts.length}
          </span>
        </h2>

        {posts.length === 0 ? (
          <div style={{
            textAlign: 'center',
            padding: '40px',
            color: '#95a5a6',
            fontSize: '16px'
          }}>
            <div style={{ fontSize: '48px', marginBottom: '16px' }}>💭</div>
            No posts yet. Be the first to post!
          </div>
        ) : (
          <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
            {posts.map((post, index) => (
              <div
                key={post.id}
                className="post-card"
                onClick={() => setSelectedPost(post.id === selectedPost ? null : post.id)}
                style={{
                  padding: '20px',
                  background: selectedPost === post.id
                    ? 'linear-gradient(135deg, #667eea15 0%, #764ba215 100%)'
                    : '#f8f9fa',
                  borderRadius: '12px',
                  cursor: 'pointer',
                  transition: 'all 0.3s ease',
                  border: selectedPost === post.id
                    ? '2px solid #667eea'
                    : '2px solid transparent',
                  animation: `slideIn 0.3s ease ${index * 0.1}s both`,
                  position: 'relative',
                  overflow: 'hidden'
                }}
              >
                {/* Post number badge */}
                <div style={{
                  position: 'absolute',
                  top: '12px',
                  right: '12px',
                  background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                  color: 'white',
                  width: '32px',
                  height: '32px',
                  borderRadius: '50%',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontSize: '14px',
                  fontWeight: 'bold'
                }}>
                  #{index + 1}
                </div>

                <div style={{ display: 'flex', gap: '16px' }}>
                  {/* User Avatar */}
                  <div style={{
                    width: '48px',
                    height: '48px',
                    borderRadius: '50%',
                    background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    color: 'white',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '20px',
                    fontWeight: 'bold',
                    flexShrink: 0
                  }}>
                    {post.username.charAt(0).toUpperCase()}
                  </div>

                  {/* Post Content */}
                  <div style={{ flex: 1 }}>
                    <div style={{
                      display: 'flex',
                      justifyContent: 'space-between',
                      alignItems: 'center',
                      marginBottom: '12px'
                    }}>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                        <span style={{
                          fontWeight: 'bold',
                          fontSize: '16px',
                          color: '#2c3e50'
                        }}>
                          {post.username}
                        </span>
                        {post.user_id === user.id && (
                          <span style={{
                            background: '#3498db',
                            color: 'white',
                            padding: '2px 8px',
                            borderRadius: '10px',
                            fontSize: '12px',
                            fontWeight: '500'
                          }}>
                            YOU
                          </span>
                        )}
                      </div>
                      <span style={{
                        fontSize: '14px',
                        color: '#95a5a6'
                      }}>
                        {formatDate(post.created_at)}
                      </span>
                    </div>

                    <div style={{
                      fontSize: '15px',
                      lineHeight: '1.6',
                      color: '#34495e',
                      marginBottom: '8px'
                    }}>
                      {post.content}
                    </div>

                    {selectedPost === post.id && (
                      <div style={{
                        marginTop: '12px',
                        padding: '12px',
                        background: 'white',
                        borderRadius: '8px',
                        fontSize: '13px',
                        color: '#7f8c8d',
                        animation: 'fadeIn 0.3s ease'
                      }}>
                        <div style={{ display: 'flex', gap: '16px' }}>
                          <div>
                            <strong>Post ID:</strong> {post.id}
                          </div>
                          <div>
                            <strong>User ID:</strong> {post.user_id}
                          </div>
                          <div>
                            <strong>Updated:</strong> {formatDate(post.updated_at)}
                          </div>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Info Footer */}
      <div style={{
        marginTop: '24px',
        padding: '20px',
        background: 'linear-gradient(135deg, #667eea15 0%, #764ba215 100%)',
        borderRadius: '12px',
        border: '2px dashed #667eea',
        textAlign: 'center',
        fontSize: '14px',
        color: '#7f8c8d'
      }}>
        <div style={{ marginBottom: '8px', fontSize: '24px' }}>✨</div>
        <div>
          This page demonstrates the <strong>{'{ reactData }'}</strong> tag pattern
        </div>
        <div style={{ marginTop: '8px', fontSize: '13px', opacity: 0.8 }}>
          Data automatically fetched from 4 providers and displayed with React
        </div>
      </div>
    </div>
  );
}

const root = ReactDOM.createRoot(document.getElementById('topic-display'));
root.render(<TopicDisplay />);
