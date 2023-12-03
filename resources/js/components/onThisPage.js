export function initOnThisPage() {
    var tocContainer = document.getElementById('toc-container');

    if (tocContainer == null) {
        return;
    }

    var headings = document.querySelectorAll('#content-container .heading-permalink'),
        wrapper = document.getElementById('toc-wrapper'),
        tocLinks = [],
        tocElement = document.createElement('ul'),
        isFirst = true,
        lastLevel = null,
        listStack = [tocElement];

    if (headings.length == 0) {
        return;
    }

    window._testHeadings = headings;
    wrapper.style.display = 'block';


    for (var i = 0; i < headings.length; i++) {
        var heading = headings[i].parentElement,
            level = parseInt(heading.tagName.charAt(1)),
            innerText = heading.innerText.substring(1),
            tocEntry = document.createElement('li'),
            tocLink = document.createElement('a');

        tocLink.classList.add('toc-link');

        tocLink.href = '#' + headings[i].id;
        tocLink.innerText = innerText;
        tocLink.title = innerText;
        tocEntry.appendChild(tocLink);

        tocLinks.push(tocLink);

        if (!isFirst && level > lastLevel) {
            var subList = document.createElement('ul');
            listStack[listStack.length - 1].lastChild.appendChild(subList);
            listStack.push(subList);
        }

        if (!isFirst && level < lastLevel) {
            listStack.pop();
            if (listStack.length == 0) {
                listStack.push(tocElement);
            }
        }

        listStack[listStack.length - 1].appendChild(tocEntry);

        isFirst = false;
        lastLevel = level;
    }

    tocContainer.appendChild(tocElement);

    function checkForBreakpointVisibility() {
        if (window.innerWidth < 1024) {
            wrapper.style.display = 'none';
        } else {
            wrapper.style.display = 'block';
        }
    }

    let lastActiveLink = null;

    function checkForAnyLinkVisibility() {

        let halfScreenHeight = window.innerHeight / 2;
        let lastHeadingAboveHalfScreen;

        headings.forEach(heading => {
            if (heading.getBoundingClientRect().top < halfScreenHeight) {
                lastHeadingAboveHalfScreen = heading;
            }
        });

        if (lastActiveLink) {
            lastActiveLink.classList.remove('active');
        }

        if (lastHeadingAboveHalfScreen) {
            const id = lastHeadingAboveHalfScreen.getAttribute('id');
            let link = document.querySelector(`.toc-link[href="#${id}"]`);
            link.classList.add('active');
            lastActiveLink = link;
        }

        let lastVisibleSection = null;

        for (let section of headings) {
            const rect = section.getBoundingClientRect();

            if (rect.top < window.innerHeight * 0.5) {
                lastVisibleSection = section;
            } else {
                break;
            }
        }

        if (!lastVisibleSection) {
            return;
        }

        const tocLink = document.querySelector(`.toc-link[href="#${lastVisibleSection.getAttribute('id')}"]`);

        if (!tocLink) {
            return;
        }

        tocLink.classList.add('active');
    }

    window.addEventListener('resize', checkForBreakpointVisibility);
    window.addEventListener('scroll', checkForAnyLinkVisibility);

    checkForBreakpointVisibility();
    checkForAnyLinkVisibility();
}
