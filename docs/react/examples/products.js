function ProductList() {
  const productsData = JSON.parse(document.getElementById('example3').dataset.products);
  const [products, setProducts] = React.useState(productsData);
  const [sortBy, setSortBy] = React.useState('name');

  const sortedProducts = [...products].sort((a, b) => {
    if (sortBy === 'name') {
      return a.name.localeCompare(b.name);
    } else {
      return a.price - b.price;
    }
  });

  return (
    <div className="react-component">
      <div className="react-component-label">⚛️ PRODUCT LIST (from PAD server)</div>

      <div style={{marginBottom: '15px'}}>
        <label>
          Sort by:
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            style={{marginLeft: '10px', padding: '5px', borderRadius: '3px'}}
          >
            <option value="name">Name</option>
            <option value="price">Price</option>
          </select>
        </label>
      </div>

      <table style={{width: '100%', borderCollapse: 'collapse'}}>
        <thead>
          <tr style={{background: '#f0f0f0'}}>
            <th style={{padding: '10px', textAlign: 'left'}}>Product</th>
            <th style={{padding: '10px', textAlign: 'right'}}>Price</th>
          </tr>
        </thead>
        <tbody>
          {sortedProducts.map(product => (
            <tr key={product.id} style={{borderBottom: '1px solid #ddd'}}>
              <td style={{padding: '10px'}}>{product.name}</td>
              <td style={{padding: '10px', textAlign: 'right'}}>${product.price.toFixed(2)}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('example3')).render(<ProductList />);
