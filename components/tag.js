class Tag extends HTMLElement {
    static get observedAttributes() {
        return ["name"];
    }

    connectedCallback() {
        this.render();
    }

    attributeChangedCallback() {
        this.render();
    }

    render() {
        const name = this.getAttribute("name") || "Tag";

        this.innerHTML = `<div class="profile-tag-card"><span class="profile-tag-hole" aria-hidden="true"></span><span class="profile-tag-label">${name}</span></div>`;
    }
}

customElements.define("custom-tag", Tag);