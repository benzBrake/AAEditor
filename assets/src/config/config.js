document.addEventListener('DOMContentLoaded', function () {
    /**
     * Apply a function to an array of DOM elements selected by a selector.
     * @param {string} selector - The CSS selector.
     * @param {function} func - The function to apply to selected elements.
     * @param {HTMLElement} parent - The parent element to search within (default is document).
     */
    function applyFunctionToSelector(selector, func, parent = document) {
        if (!parent) {
            parent = document;
        }
        const selectedElements = parent.querySelectorAll(selector);
        Array.from(selectedElements).forEach(func);
    }

    /**
     * Shortcut for document.querySelector.
     * @param {string} selector - The CSS selector.
     * @param {HTMLElement} parent - The parent element to search within (default is document).
     * @returns {HTMLElement|null} - The first matching element or null if not found.
     */
    function querySelector(selector, parent = document) {
        if (!parent) {
            parent = document;
        }
        return parent.querySelector(selector);
    }

    /**
     * Compare two version numbers.
     * @param {string} serverVersion - The server version number.
     * @param {string} currentVersion - The current version number.
     * @returns {number} - 1 if server version is greater, -1 if current version is greater, 0 if equal.
     */
    function compareVersions(serverVersion, currentVersion) {
        const serverVersionArray = serverVersion.split('.');
        const currentVersionArray = currentVersion.split('.');
        const minLength = Math.min(serverVersionArray.length, currentVersionArray.length);

        for (let i = 0; i < minLength; i++) {
            const a = parseInt(serverVersionArray[i]);
            const b = parseInt(currentVersionArray[i]);
            if (a > b) {
                return 1;
            } else if (a < b) {
                return -1;
            }
        }

        if (serverVersionArray.length > currentVersionArray.length) {
            for (let j = minLength; j < serverVersionArray.length; j++) {
                if (parseInt(serverVersionArray[j]) !== 0) {
                    return 1;
                }
            }
            return 0;
        } else if (serverVersionArray.length < currentVersionArray.length) {
            for (let j = minLength; j < currentVersionArray.length; j++) {
                if (parseInt(currentVersionArray[j]) !== 0) {
                    return -1;
                }
            }
            return 0;
        }

        return 0;
    }

    const xConfig = querySelector('.x-config');
    const xContent = querySelector('.x-content', xConfig);

    // Move the form element
    const form = xConfig.parentNode.querySelector('form');
    form.parentNode.removeChild(form);
    xContent.appendChild(form);

    // Restore the active state
    const xTabs = querySelector('.x-tabs', xConfig);
    let active = sessionStorage.getItem('x-active') || '.x-notice';

    applyFunctionToSelector(active, el => {
        el.classList.add('active');
    }, xContent);

    applyFunctionToSelector(`[data-class="${active.replace('.', '')}"]`, el => {
        el.classList.add('active');
    }, xTabs);

    // Handle tab clicks
    Array.from(xTabs.querySelectorAll(':scope > li')).forEach(el => {
        el.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            window.scrollTo(0, 0);

            applyFunctionToSelector('.x-tabs li', el => {
                el.classList.remove('active');
            });

            event.target.classList.add('active');
            const className = '.' + event.target.dataset.class;
            sessionStorage.setItem('x-active', className);

            applyFunctionToSelector('.x-item', el => {
                el.classList.remove('active');
            }, xContent);

            applyFunctionToSelector(className, el => {
                el.classList.add('active');
            }, xContent);
        });
    });

    // Load the log
    if (typeof window.XEditorUpldateURL === 'string' && window.XEditorUpldateURL.startsWith('http')) {
        fetch(window.XEditorUpldateURL)
            .then(response => response.text())
            .then(text => {
                const fragment = document.createElement('div');
                fragment.innerHTML = text;
                text = fragment.querySelector('.post-content,.entry-content')?.innerHTML;
                let firstNode = fragment.querySelector('h2, h3');

                const serverVersion =  firstNode.innerText.substring(0, 5);
                const xNotice = querySelector('.x-notice', xConfig);
                const title = querySelector('.title', xConfig);

                querySelector('.loading', title).remove();
                querySelector('.latest.version', title).innerHTML = firstNode.innerText;
                querySelector('.message', xNotice).innerHTML = text;
                querySelector('.latest.version', title).classList.add('active');

                if (compareVersions(serverVersion, title.dataset.version) > 0) {
                    querySelector('.latest.found', title).classList.add('active');
                } else {
                    querySelector('.latest', title).classList.add('active');
                }
            })
            .catch(err => console.log('Request Failed', err));
    }

    if (location.hash) {
        // 跳转到相应 Tab
        let hashNode = document.querySelector(location.hash);
        if (hashNode && hashNode.classList.contains('x-item')) {
            document.querySelectorAll('.x-item.active').forEach(i => i.classList.remove('active'));
            let classList = JSON.parse(JSON.stringify([...hashNode.classList]));
            classList = classList.filter(c => c.startsWith('x-') && c !== "x-item");
            document.querySelector(`[data-class="${classList[0]}"]`).click();
        }
    }

    if (window.XEditorModules && document.querySelector('input[name="XModules"]')) {
        let inputNode = document.querySelector('input[name="XModules"]'),
            refNode = inputNode.nextElementSibling;
        inputNode.classList.add('hidden');
        window.XEditorModules.forEach(mConfig => {
            let span = document.createElement('span');
            let checkbox = document.createElement('input');
            checkbox.type = "checkbox";
            checkbox.value = mConfig.file;
            checkbox.id = 'module-' + mConfig.file.replaceAll('.', '-');
            let label = document.createElement('label');
            label.innerText = mConfig.title + '【' + mConfig.description + '】';
            label.setAttribute('for', 'module-' + mConfig.file.replaceAll('.', '-'));
            checkbox.addEventListener('change', checkboxChange);
            span.appendChild(checkbox);
            span.appendChild(label);
            span.classList.add('multiline');
            refNode.before(span);
        });

        let enabledModules = JSON.parse(inputNode.value || '[]');
        enabledModules.forEach(v => {
            let node = inputNode.parentNode.querySelector(`input[value="${v}"]`);
            if (node) node.checked = true;
        });

        function checkboxChange(event) {
            inputNode.value = JSON.stringify([...refNode.parentNode.querySelectorAll('input[type="checkbox"]')].filter(c => c.checked).map(c => c.value))
        }

        // 增加全选和全不选按钮
        let btnAllChecked = document.createElement('button');
        btnAllChecked.innerText = '全选';
        btnAllChecked.type = 'button';
        btnAllChecked.addEventListener('click', () => {
            Array.from(inputNode.parentNode.querySelectorAll('input[type="checkbox"]')).forEach(input => {
                input.checked = false;
                input.click();
            })
        });

        let btnAllUnchecked = document.createElement('button');
        btnAllUnchecked.innerText = '全不选';
        btnAllUnchecked.type = 'button';
        btnAllUnchecked.addEventListener('click', () => {
            Array.from(inputNode.parentNode.querySelectorAll('input[type="checkbox"]')).forEach(input => {
                input.checked = true;
                input.click();
            })
        });
        inputNode.previousElementSibling.appendChild(btnAllChecked);
        inputNode.previousElementSibling.appendChild(btnAllUnchecked);
    }
});
