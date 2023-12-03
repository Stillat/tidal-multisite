function isScrollbarVisible() {
    return document.documentElement.scrollHeight > document.documentElement.clientHeight;
}

function _updateTheme(preference) {
    localStorage.setItem('themePreference', preference);

    switch (preference) {
        case 'dark':
            document.documentElement.classList.add('dark');
            break;
        case 'light':
            document.documentElement.classList.remove('dark');
            break;
        default:
            setThemeBasedOnSystemPreference();
            break;
    }
}

function getCurrentTheme() {
    return localStorage.getItem('themePreference') || 'system';
}

function setThemeBasedOnSystemPreference() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

const storedPreference = localStorage.getItem('themePreference') || 'system';

_updateTheme(storedPreference);

export function siteData() {
    return {
        menuOpen: false,
        footerPusherHeight: 0,
        isSmallBreakpoint: false,
        scrollbarVisible: false,
        themePreference: '',
        effectiveTheme: '',
        init() {
            this.themePreference = localStorage.getItem('themePreference') || 'system';
            this.updateEffectiveTheme();

            const mediaQuery = window.matchMedia('(min-width: 770px)');

            this.scrollbarVisible = isScrollbarVisible();
            this.isSmallBreakpoint = !mediaQuery.matches;

            if (!this.isSmallBreakpoint) {
                this.menuOpen = false;
            }

            mediaQuery.addEventListener('change', (event) => {
                this.isSmallBreakpoint = !event.matches;
                this.scrollbarVisible = isScrollbarVisible();

                if (!this.isSmallBreakpoint) {
                    this.menuOpen = false;
                }
            });

            window.addEventListener('storage', (event) => {
                if (event.key !== 'themePreference') {
                    return;
                }
            
                this.themePreference = event.newValue;

                if (this.themePreference == 'system') {
                    this.updateEffectiveTheme();
                } else {
                    this.effectiveTheme = this.themePreference;
                }

                _updateTheme(this.themePreference);
            });

            window.matchMedia('(prefers-color-scheme: dark)').addListener(e => {
                const currentPreference = localStorage.getItem('themePreference');

                if (currentPreference !== 'system') {
                    return;
                }

                this.updateEffectiveTheme();

                setThemeBasedOnSystemPreference();
            });
        },
        updateEffectiveTheme() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                this.effectiveTheme = 'dark';
            } else {
                this.effectiveTheme = 'light';
            }
        },
        updateTheme(theme) {
            this.themePreference = theme;

            if (theme == 'system') {
                this.updateEffectiveTheme();
            } else {
                this.effectiveTheme = theme;
            }

            _updateTheme(theme);
        },
        menuEnterTransition() {
            return this.isSmallBreakpoint ? 'transition transform duration-300' : '';
        },
        menuEnterStartTransition() {
            return this.isSmallBreakpoint ? '-translate-x-full' : 'translate-x-0';
        },
        menuLeaveTransition() {
            return this.isSmallBreakpoint ? 'transition transform duration-300' : '';
        },
        menuLeaveEndTransition() {
            return this.isSmallBreakpoint ? '-translate-x-full' : 'translate-x-0';
        },
        toggleMenu() {
            this.menuOpen = !this.menuOpen;
        }
    }
};