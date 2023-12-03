let siteHeader = document.getElementsByTagName('header')[0],
    sidebars = document.getElementsByClassName('sticky-sidebar'),
    contentContainer = document.getElementById('content-container');

function debounce(func, timeout = 52) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
};

function getVisibleHeight(elem) {
    const rect = elem.getBoundingClientRect();
    const windowHeight = (window.innerHeight || document.documentElement.clientHeight);

    if (rect.bottom <= 0) {
        return 0;
    }

    if (rect.top >= windowHeight) {
        return 0;
    }

    if (rect.top < 0) {
        return rect.bottom;
    }

    if (rect.bottom > windowHeight) {
        return windowHeight - rect.top;
    }

    return rect.height;
}

let hasDoneInitial = false;

function adjustSidebarHeights(isScroll, initialCallback) {
    requestAnimationFrame(function () {
        if (window.innerWidth < 1024) {
            for (let sidebar of sidebars) {
                sidebar.style.height = 'initial';
                sidebar.style.maxHeight = 'initial';
            }

            return;
        }

        let visibleHeight = (window.innerHeight),
            headerHeight = getVisibleHeight(siteHeader),
            usableHeight = Math.round(visibleHeight - headerHeight - 25);

        if (contentContainer.clientHeight < visibleHeight && isScroll) {
            return;
        }

        for (let sidebar of sidebars) {
            sidebar.style.height = usableHeight + 'px';
            sidebar.style.maxHeight = usableHeight + 'px';
        }

        if (!hasDoneInitial && initialCallback) {
            initialCallback();
            hasDoneInitial = true;
        }
    });
}

export function setupSidebarHeights() {
    if (!contentContainer) {
        return;
    }

    const leftNavigationMenu = document.getElementById('leftNavigationMenu');

    window.addEventListener('resize', debounce(() => adjustSidebarHeights(false)));
    window.addEventListener('scroll', debounce(() => adjustSidebarHeights(true)));

    adjustSidebarHeights(false, function () {
        const activeMenuItem = leftNavigationMenu.querySelector('.active-menu-item');

        if (!activeMenuItem) {
            return;
        }

        const targetScrollTop = activeMenuItem.offsetTop - leftNavigationMenu.offsetTop,
            padding = 20;

        const maxScrollTop = leftNavigationMenu.scrollHeight - leftNavigationMenu.clientHeight,
            paddedScrollTop = Math.min(targetScrollTop + padding, maxScrollTop);

        leftNavigationMenu.scrollTop = paddedScrollTop;
    });
}
