import { h, render } from 'preact';

export const name = 'Preact';

export function island(Component) {
  return (element, props) => {
    element.innerHTML = '';
    render(h(Component, props), element);
    return () => render(null, element);
  };
}
