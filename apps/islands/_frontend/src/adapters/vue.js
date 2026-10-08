import { createApp } from 'vue';

export const name = 'Vue';

export function island(Component) {
  return (element, props) => {
    element.innerHTML = '';
    const app = createApp(Component, props);
    app.mount(element);
    return () => app.unmount();
  };
}
