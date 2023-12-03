export function dropdownMenu() {
    return {
        open: false,
        focusedOptionIndex: 0,
        closeOnSelection: false,
        alignRight: false,
        closeOnSelection: false,
        init() {
            this.$watch('open', (value) => {
                if (value) {
                    let rect = this.$el.getBoundingClientRect();
                    this.alignRight = (rect.left + 224 > window.innerWidth);
                }
            });
        },
        setConfig(closeOnSelection) {
            if (typeof closeOnSelection !== 'undefined') {
                this.closeOnSelection = closeOnSelection;
            }
        },
        hideMenu() {
            this.open = false;
            this.focusedOptionIndex = null;
        },
        handleGeneralClick(event) {
            if (this.closeOnSelection) {
                this.hideMenu();
            }
        },
        handleKeydown(event) {
            let items = this.$el.querySelectorAll('.dropdown-item');

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    if (this.focusedOptionIndex === null || this.focusedOptionIndex === items.length - 1) {
                        this.focusedOptionIndex = 0;
                    } else {
                        this.focusedOptionIndex++;
                    }
                    items[this.focusedOptionIndex].focus();
                    break;

                case 'ArrowUp':
                    event.preventDefault();
                    if (this.focusedOptionIndex === null || this.focusedOptionIndex === 0) {
                        this.focusedOptionIndex = items.length - 1;
                    } else {
                        this.focusedOptionIndex--;
                    }
                    items[this.focusedOptionIndex].focus();
                    break;
                case 'Escape':
                    this.hideMenu();
                    break;
                default:
                    break;
            }
        }
    };
};