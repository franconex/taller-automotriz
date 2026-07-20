import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import theme from './theme';

Alpine.plugin(persist);

Alpine.data('theme', theme);

window.Alpine = Alpine;

Alpine.start();
