document.addEventListener('DOMContentLoaded', function() {
    const rolesList = document.getElementById('rolesList');
    const controllersList = document.getElementById('controllerList');
    const allowList = document.querySelectorAll('.allowList');
    let allowData = {};
   
    fetch('/access/', {  
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка при получении данных с сервера');
            }
           console.log(response.json());
        })
        .then(data => {
            // Обрабатываем полученные данные
            console.log(data);
            allowData = data;
        })
        .catch(error => {
            // Обработка ошибок
            console.error('Произошла ошибка:', error);
        });

    let role;
    let conditionData = {};


    function getCurrentRole() {
        const currentRole = document.querySelector('.role');
        currentRole.textContent = `Текущая роль: ${event.target.textContent}`
        currentRole.style.display = 'block';
    }
    rolesList.addEventListener('click', function() {
        role = event.target.getAttribute('value');
        getCurrentRole();
        addCheckedList(role);
        console.log(role);
    })

    allowList.forEach(function(input) {
        input.addEventListener('click', function() {
            const setCheck = event.target.checked;
            const action = event.target.getAttribute('value');
            const controller = event.target.getAttribute('name');

            console.log(setCheck);
            console.log(action);
            console.log(controller);
            console.log(role);
            if (!conditionData[role]) {
                conditionData[role] = {};
            }
            if (!conditionData[role][controller]) {
                conditionData[role][controller] = {};
            }
            conditionData[role][controller][action] = setCheck;
            dataToSend[role][controller][action] = setCheck;
            console.log(conditionData);
        });
    });

    function addCheckedList(role) {
        console.log(conditionData);
        const controllers = allowData[role];
        for (const controller in controllers) {
            const actions = controllers[controller];
            for (const action in actions) {
                const allow = actions[action];
                const input = document.querySelector(`input[name="${controller}"][value="${action}"]`)
                if (allow) {
                    input.checked = true;

                } else {
                    input.checked = false;

                }
            }
        }
        if (conditionData[role]) {
            const conditionControllers = conditionData[role];
            for (const controller in conditionControllers) {
                const actions = conditionControllers[controller];
                for (const action in actions) {
                    const allow = actions[action];
                    const input = document.querySelector(`input[name="${controller}"][value="${action}"]`)
                    if (allow) {
                        input.checked = true;
                    } else {
                        input.checked = false;
                    }
                }
            }

        }

        document.getElementById('controllersList').style.display = 'block';
        console.log(dataToSend);
    }

    const delbtnModal = document.querySelector('[data-bs-whatever="delRolesModal"]');
    const ol = document.querySelector('.list-group-numbered');
    const fol = document.querySelectorAll('ol input');
    const delbtn = document.querySelector('.del');
    let rolesDataToSend = [];

    ol.addEventListener('click', function(event) {
        if (event.target.closest('.rolesList')) {
            const input = event.target.querySelector('input');
            input.checked = !input.checked;

        }
        console.log(ol);
        console.log(event.target.closest('.rolesList'));
        console.log(event.target.querySelector('input'));
    });

    const btn = document.getElementById("send");
    btn.addEventListener('click', function() {
        fetch('/access/', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(dataToSend),
            })
            .then((response) => {
                location.replace('/access');   
                return response.json();
            })

    });
});