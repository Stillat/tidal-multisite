export function collapsibleList(allowMultiple, panelConfig) {
    return {
        allowMultiple: allowMultiple,
        panelConfig: panelConfig,
        panels: {},
        init() {
            let foundOpenPanel = false;

            for (let i = 0; i < this.panelConfig.length; i++) {
                const panel = this.panelConfig[i];

                let panelOpen = panel.is_open;

                if (!allowMultiple) {
                    if (!foundOpenPanel && panelOpen) {
                        foundOpenPanel = true;
                    } else {
                        panelOpen = false;
                    }
                }

                this.panels[panel.id] = panelOpen;
            }
        },
        togglePanel(panelId) {
            this.panels[panelId] = !this.panels[panelId];

            if (!allowMultiple) {
                for (const id in this.panels) {
                    if (id == panelId) {
                        continue;
                    }

                    this.panels[id] = false;
                }
            }
        }
    };
};