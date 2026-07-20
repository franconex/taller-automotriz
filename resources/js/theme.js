export default function () {
    return {
        theme: Alpine.$persist('system').as('taller-pro-theme'),

        init() {
            this.applyTheme();
        },

        get resolved() {
            if (this.theme === 'dark') return 'dark';
            if (this.theme === 'light') return 'light';
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        },

        applyTheme() {
            document.documentElement.classList.toggle('dark', this.resolved === 'dark');
        },

        setTheme(value) {
            this.theme = value;
            this.applyTheme();
        },

        cycleTheme() {
            const modes = ['light', 'dark', 'system'];
            const idx = modes.indexOf(this.theme);
            this.setTheme(modes[(idx + 1) % modes.length]);
        },
    };
}
