class BottomNavigation extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <nav class="bottom-navigation">
                <a href="index.html"><img src="./images/nav-ico/map.svg" alt=""></a>
                <div class="divider-bottom-navigaiton"></div>
                <a href="companies.html"><img src="./images/nav-ico/grid.svg" alt=""></a>
                <div class="divider-bottom-navigaiton"></div>
                <a href="events.html"><img src="./images/nav-ico/callendar.svg" alt=""></a>
                <div class="divider-bottom-navigaiton"></div>
                <a href="profile.html"><img src="./images/nav-ico/user.svg" alt=""></a>
                <div class="divider-bottom-navigaiton"></div>
                <a href="settings.html"><img src="./images/nav-ico/settings.svg" alt=""></a>
            </nav>
        `;
    }
}

customElements.define('bottom-navigation', BottomNavigation);
