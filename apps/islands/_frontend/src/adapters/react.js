import { createElement } from 'react';
import { createRoot } from 'react-dom/client';

export const name = 'React';

export function island(Component) {
  return (element, props) => {
    const root = createRoot(element);
    root.render(createElement(Component, props));
    return () => root.unmount();
  };
}
