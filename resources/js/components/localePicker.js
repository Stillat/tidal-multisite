export function initLocalePicker() {
    const selectElement = document.getElementById('entryLocales');

    if (! selectElement) {
        return;
    }

    selectElement.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        const url = selectedOption.value;

        if (url) {
            window.location.href = url;
        }
    });
}