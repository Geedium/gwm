let messages = {
    en: {
        'home': 'Home',
        'forum': 'Forum',
        'about': 'About',
        'projects': 'Projects',
        'misc': 'Miscellaneous',
        'login': 'Login',
        'outside-routes': 'Outside Routing'
    },
    lt: {
        'home': 'Titulinis',
        'forum': 'Forumas',
        'about': 'Apie',
        'projects': 'Projektai',
        'misc': 'Įvairūs',
        'login': 'Prisijungimas',
        'outside-routes': 'Išoriniai Maršrutai'
    }
};

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/;secure";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
        c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
        }
    }
    return "";
}

function updateLanguage() {
    var a = document.querySelectorAll('[data-i18n]');
    
    a.forEach(element => {
        var text = element.dataset.i18n

        if(text) {
            element.innerHTML = $t(text)
        }
    })
}

function changeLanguage(lang) {
    console.log(`Changing language to ${lang}`)
    setCookie('lang', lang, 1)
    updateLanguage()
}

if(!getCookie('lang')) {
    changeLanguage('en')
}

function $t(msg) {
    const lang = getCookie('lang')
    return messages[lang][msg]
}

class Alert extends HTMLElement {
    constructor() {
        super()
       
    }
}

class Link extends HTMLElement {
    constructor() {
        super()

        this.link = this.getAttribute('to');

        this.rippleEffect = this.getAttribute('ripples');

        if(!this.link) {
            document.body.innerHTML = "<pre>" 
                + "Link does not point to any route!" 
                + "<br/>" 
                + JSON.stringify(this)
                + "</pre>";
        }

        this.onclick = function(e) {
            if(this.rippleEffect) {

                let rect = e.target.getBoundingClientRect();
                let x = e.clientX - rect.left + e.offsetX
                let y = e.clientY - rect.top + e.offsetY

                let ripples = document.createElement('span');
                ripples.classList.add('ripple');
                ripples.style.left = x + 'px';
                ripples.style.top = y + 'px';
                this.appendChild(ripples);

                setTimeout(() => {
                    ripples.remove()
                    window.location.assign(this.link);
                }, 1000);

            } else {
                window.location.assign(this.link);
            }
        };
    }
}

class NavLink extends Link {
    constructor() {
        super()
    }
}

class FooterLink extends Link {
    constructor() {
        super()
    }
}

class DropdownTrigger extends HTMLElement {
    constructor() {
        super()

        const menu = this.getElementsByTagName('dropdown-menu')[0];
        let state = false;
        let oldState = state;

        this.onclick = function(event) {
            oldState = state;
            state = !state;
            menu.setAttribute("open", state);

            event.stopPropagation();
        };

        window.document.onclick = function(event) {
            if(state) {
                const withinBoundaries = event.composedPath().includes(menu)

                if(!withinBoundaries && state != oldState) {
                    state = false;
                    menu.setAttribute("open", state);
                }
            }
        };

    }
}

class DropdownItem extends Link {
    constructor() {
        super()
    }
}

window.addEventListener('DOMContentLoaded', function() {
    window.customElements.define('alert-box', Alert);

    window.customElements.define('app-link', Link);

    if(window.customElements.whenDefined('nav-link') == true) {
        document.body.innerHTML = "@nav-link element is already defined!";
    } else {
        window.customElements.define('nav-link', NavLink);
        console.log('GWM: defining new element named nav-link.');
    }

    window.customElements.define('dropdown-trigger', DropdownTrigger);
    window.customElements.define('dropdown-item', DropdownItem);
    window.customElements.define('footer-link', FooterLink);
    
    updateLanguage()  
})

window.onload = function() {
    const images = document.getElementsByTagName('img');
    for (let image of images) {

        const width = image.getAttribute('data-width');
        const height = image.getAttribute('data-height');

        image.width = !width ? image.naturalWidth : width;
        image.height = !height ? image.naturalHeight : height;
 
        image.ontransitionend = function() {
            image.style.opacity = 1;
        };

        requestAnimationFrame(function() {
            image.style.width = `${!width ? image.naturalWidth : width}px`;
            image.style.height = `${!height ? image.naturalHeight : height}px`;
        });
    }
};