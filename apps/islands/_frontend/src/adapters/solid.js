import { createComponent } from 'solid-js';
import { render } from 'solid-js/web';

export const name = 'Solid';

export function island(Component) {
  return (element, props) => {
    element.innerHTML = '';
    return render(() => createComponent(Component, props), element);
  };
}
