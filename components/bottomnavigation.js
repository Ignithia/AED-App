class BottomNavigation extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <nav class="bottom-navigation">
                <a href="index.html" class="icon-map"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="companies.html" class="icon-grid"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="events.html" class="icon-calendar"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="profile.html" class="icon-user"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="settings.html" class="icon-settings"></a>
            </nav>
        `;
    }
}

customElements.define('bottom-navigation', BottomNavigation);
