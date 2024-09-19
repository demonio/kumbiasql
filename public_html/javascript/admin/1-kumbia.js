(function() {
    const KumbiaJS = {
        ajax: function(eve) {
            eve.preventDefault();
            const mensaje = this.dataset.confirm;
            if (mensaje && !confirm(mensaje)) {
                return false;
            }
            const to = document.querySelector(this.dataset.ajax);
            fetch(this.href)
                .then(response => response.text())
                .then(data => {
                    to.innerHTML = data;
                    console.log([to, this.href]);
                })
                .catch(error => console.error('Error:', error));
        },

        active: function() {
            const to = document.querySelector(this.dataset.active);
            to.classList.remove('active');
            this.classList.add('active');
        },

        alert: function() {
            alert(this.dataset.alert);
        }, 

        checkbox: function() {
            if (this.readOnly) {
                this.checked = this.readOnly = false;
            } else if (!this.checked) {
                this.readOnly = this.indeterminate = true;
            }
        },

        clone_content_append: function() {
            const params = this.dataset.clone_content_append.split(', ');
            const el = document.querySelector(params[0]);
            const to = document.querySelector(params[1]);
            console.log([params, el, to]);
            if (el instanceof HTMLTemplateElement) {
                const clone = document.importNode(el.content, true);
                to.appendChild(clone);
            }
        },

        confirm: function(eve) {
            if (!confirm(this.dataset.confirm)) {
                eve.preventDefault();
                eve.stopImmediatePropagation();
            }
        },

        effect: function(effect) {
            return function() {
                const to = document.querySelector(this.dataset[effect]);
                to.style[effect]();
            }
        },

        formAjax: function(eve) {
            eve.preventDefault();

            const form = this.closest('form');
            const url = form.getAttribute('action');
            let to;
            if (form.dataset.ajaxAppend) {
                to = document.querySelector(form.dataset.ajaxAppend);
            } else if (form.dataset.ajaxPrepend) {
                to = document.querySelector(form.dataset.ajaxPrepend);
            } else {
                to = document.querySelector(form.dataset.ajax);
            }
            const formData = new FormData(form);

            const buttons = form.querySelectorAll('[type="submit"]');
            buttons.forEach(button => button.setAttribute('disabled', 'disabled'));

            const btnName = this.getAttribute('name');
            if (btnName !== undefined) {
                const btnVal = this.value;
                formData.append(btnName, btnVal);
            }

            const fileData = form.querySelector('[type="file"]');
            if (fileData) {
                formData.append('file', fileData.files[0]);
            }

            fetch(url, {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    let mode;
                    if (form.dataset.ajaxAppend) {
                        to.innerHTML += data;
                        mode = 'append';
                    } else if (form.dataset.ajaxPrepend) {
                        to.innerHTML = data + to.innerHTML;
                        mode = 'prepend';
                    } else {
                        to.innerHTML = data;
                        mode = 'normal';
                    }
                    buttons.forEach(button => button.removeAttribute('disabled'));
                    console.log([to, url, mode]);
                })
                .catch(error => console.error('Error:', error));
        },

        live: function() {
            const to = document.querySelector(this.dataset.live);
            const href = this.dataset.href;
            fetch(href, { method: 'POST', body: JSON.stringify({ 'keywords': this.value }) })
                .then(response => response.text())
                .then(data => {
                    to.innerHTML = data;
                    console.log([to, href, this.value]);
                })
                .catch(error => console.error('Error:', error));
        },

        remove: function() {
            const to = this.dataset.remove === 'parent' ? this.parentElement : document.querySelector(this.dataset.remove);
            to.remove();
        },

        selectAjax: function() {
            const to = document.querySelector(this.dataset.ajax);
            const href = this.dataset.href + this.value;
            fetch(href)
                .then(response => response.text())
                .then(data => {
                    to.innerHTML = data;
                    console.log([to, href]);
                })
                .catch(error => console.error('Error:', error));
        },

        selectRedirect: function() {
            const href = this.dataset.redirect + this.value;
            location.href = href;
        },

        selectToggle: function() {
            const to = document.querySelector(this.dataset.changeToggle);
            to.style.display = to.style.display === 'none' ? 'block' : 'none';
        },

        style: function() {
            const params = this.dataset.style.split(', ');
            const selector = params[0];
            const style = params[1];
            document.querySelector(selector).setAttribute('style', style);
        },

        toggleClass: function() {
            const toggleClassData = this.dataset.toggle_class;
            console.log('Data attribute:', toggleClassData);
            if (!toggleClassData) {
                console.error('data-toggle_class attribute is missing');
                return;
            }
            const params = toggleClassData.split(', ');
            console.log('Toggle class params:', params);
            const className = params[0];
            const selector = params[1];
            const elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
                elements.forEach(element => {
                    if (element.classList.contains(className)) {
                        element.classList.remove(className);
                    } else {
                        element.classList.add(className);
                    }
                });
            } else {
                console.error('No elements found for selector:', selector);
            }
        },

        toggleDisplay: function() {
            const to = document.querySelector(this.dataset.toggleDisplay);
            if (window.getComputedStyle(to).display === 'none') {
                to.style.display = 'flex';
            } else {
                to.style.display = 'none';
            }
        },

        bind: function() {
            console.log('bind method called');

            window.addEventListener('load', function(event) {
                const checkboxes = document.querySelectorAll('input[type="checkbox"][readonly]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.indeterminate = true;
                });
            });

            document.body.addEventListener('click', function(event) {
                let target = event.target;
                while (target && !Array.from(target.attributes).some(attr => attr.name.startsWith('data-'))) {
                    target = target.parentElement;
                }
                if (!target) {
                    console.error('No target with data-* found');
                    return;
                }
                const dataAttributes = Array.from(target.attributes)
                    .filter(attr => attr.name.startsWith('data-'))
                    .map(attr => attr.name);

                console.log('Event target:', target);
                console.log('Data attributes:', dataAttributes);

                if (dataAttributes.length > 0) {
                    // Recorrer los atributos data-* y ejecutar la función correspondiente
                    dataAttributes.forEach(attr => {
                        const dataKey = attr.replace('data-', '');
                        if (typeof KumbiaJS[dataKey] === 'function') {
                            KumbiaJS[dataKey].call(target, event);
                        }
                    });
                }
            });
        }
    };
    KumbiaJS.bind();
})();
