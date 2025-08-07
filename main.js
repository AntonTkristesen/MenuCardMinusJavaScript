document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const body = document.body;
    let messageArea = null;

    function createMessageArea() {
        messageArea = document.createElement('div');
        messageArea.style.display = 'none';
        messageArea.style.padding = '10px';
        messageArea.style.margin = '15px 0';
        messageArea.style.borderRadius = '5px';
        messageArea.style.fontSize = '14px';
        
        const firstChild = body.firstElementChild;
        body.insertBefore(messageArea, firstChild);
    }

    function showMessage(text, isError = false) {
        if (!messageArea) createMessageArea();
        
        messageArea.textContent = text;
        messageArea.style.display = 'block';
        
        if (isError) {
            messageArea.style.backgroundColor = '#f2dede';
            messageArea.style.color = '#a94442';
            messageArea.style.border = '1px solid #ebccd1';
        } else {
            messageArea.style.backgroundColor = '#dff0d8';
            messageArea.style.color = '#3c763d';
            messageArea.style.border = '1px solid #c3e6cb';
        }
        
        if (!isError) {
            setTimeout(() => messageArea.style.display = 'none', 3000);
        }
    }

    function updateMenu() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                
                const currentMenuH2 = Array.from(document.querySelectorAll('h2')).find(h => h.textContent.trim() === 'Menu');
                const newMenuH2 = Array.from(temp.querySelectorAll('h2')).find(h => h.textContent.trim() === 'Menu');
                
                if (currentMenuH2 && newMenuH2) {
                    const currentMenu = currentMenuH2.nextElementSibling;
                    const newMenu = newMenuH2.nextElementSibling;
                    
                    if (currentMenu && newMenu) {
                        currentMenu.innerHTML = newMenu.innerHTML;
                    }
                }
            })
            .catch(() => showMessage('Could not refresh menu', true));
    }

    function handleForm(form) {
        const formData = new FormData(form);
        const action = formData.get('action');
        
        showMessage('Processing...');
        
        fetch('backend.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                let message = 'Operation completed successfully';
                
                if (action === 'add_dish') {
                    message = 'Dish added successfully';
                    form.closest('details')?.removeAttribute('open');
                    form.reset();
                } else if (action === 'edit_dish') {
                    message = 'Dish updated successfully';
                    form.closest('details')?.removeAttribute('open');
                } else if (action === 'delete_dish') {
                    message = 'Dish deleted successfully';
                }
                
                showMessage(message);
                updateMenu();
            } else {
                showMessage('Operation failed. Please try again.', true);
            }
        })
        .catch(() => showMessage('Network error. Please try again.', true));
    }

    body.addEventListener('submit', function(event) {
        const form = event.target;
        const actionAttr = form.getAttribute('action');
        
        if (actionAttr === 'backend.php' || (actionAttr && actionAttr.includes('backend.php'))) {
            event.preventDefault();
            handleForm(form);
        }
    });

    body.addEventListener('click', function(event) {
        if (event.target.matches('button') && event.target.textContent === '×') {
            event.target.closest('details')?.removeAttribute('open');
        }
    });
});