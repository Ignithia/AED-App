class BottomNavigation extends HTMLElement {
  connectedCallback() {
    this.innerHTML = `
            <nav class="bottom-navigation">
                <a href="index.php" class="icon-home" aria-label="Home"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="events.php" class="icon-calendar" aria-label="Events"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="account.php" class="icon-user" aria-label="Account"></a>
                <div class="divider-bottom-navigation"></div>
                <a href="settings.php" class="icon-settings" aria-label="Settings"></a>
            </nav>
        `;
  }
}

customElements.define("bottom-navigation", BottomNavigation);
