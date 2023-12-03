export function initVersionSelection() {
    const selectElement = document.getElementById('softwareVersions');

    if (! selectElement) {
        return;
    }

    selectElement.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        const url = selectedOption.getAttribute('data-version-url');

        if (url) {
            window.location.href = url;
        }
    });
}