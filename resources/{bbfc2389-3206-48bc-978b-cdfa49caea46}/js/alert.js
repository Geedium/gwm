export default class Alert extends HTMLElement {
    constructor() {
        super()

        this.classList.add('alert');

        this.onclick = function() {
            alert('Thanks!');
        };
    }
}

customElements.define('popup-info', Alert);