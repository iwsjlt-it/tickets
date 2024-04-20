document.addEventListener('DOMContentLoaded', function() {
    const controllersList = document.getElementById('controllersList');
    const actionsList = document.getElementById('actionsList');

    console.log(controllersData);

    $('#rolesList').on('click', 'li', function() {
        const role = $(this).text().trim();
        addControllers(role);
        $('#controllers').show();
    });

    function addControllers(role) {
        controllersList.innerHTML = '';
        for (const controller of controllersData[role]) {
            const listItem = document.createElement('li');
            listItem.textContent = controller;
            listItem.classList.add('list-group-item');
            controllersList.appendChild(listItem);
        }
    }
    $('#controllersList').on('click', 'li', function() {
        const controller = $(this).text().trim();
        addActions(controller);
        $('#actions').show();
    });

    function addActions(controller) {
        actionsList.innerHTML = '';
        for (const action of actionsData[controller]) {
            const listItem = document.createElement('li');
            listItem.classList.add('list-group-item');
            const label = document.createElement('label');
            const input = document.createElement('input');
            input.type = 'checkbox';
            input.name = 'action';
            input.value = action;
            label.appendChild(input);
            label.appendChild(document.createTextNode(` ${action}`));
            listItem.appendChild(label);
            actionsList.appendChild(listItem);
        }
    }
});