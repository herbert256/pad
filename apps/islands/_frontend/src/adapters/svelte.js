import { mount, unmount } from 'svelte';

export const name = 'Svelte';

export function island(Component) {
  return (element, props) => {
    element.innerHTML = '';
    const instance = mount(Component, { target: element, props });
    return () => unmount(instance);
  };
}
