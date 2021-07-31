
var gridSize = [14, 14];
var padding = 2;
var gameon = false;

function getRowName(i) {
    return 'row-' + i;
}

function getColName(i, j) {
    return 'col-' + i + ',' + j;
}

function getCellState(i, j) {
    return document.getElementById(getColName(i, j)).getAttribute('state');
}

function setCellState(i, j, state) {
    document.getElementById(getColName(i, j)).setAttribute('state', state);
}

function toggleCellState(cell) {
    let i = cell.getAttribute('row');
    let j = cell.getAttribute('col');
    setCellState(i, j, getCellState(i, j) === 'dead' ? 'alive' : 'dead');
}

function buildGrid() {
    for (var i = gridSize[0] - 1; i >= 0; i--) {
        let row = document.createElement('div');
        row.id = getRowName(i);
        row.classList.add('row');

        if (i < padding || i >= gridSize[0] - padding) {
            row.setAttribute('context', 'external');
        }

        for (var j = gridSize[1] - 1; j >= 0; j--) {
            let col = document.createElement('div');
            col.id = getColName(i, j);
            col.classList.add('col');
            col.setAttribute('state', 'dead');
            col.setAttribute('row', i);
            col.setAttribute('col', j);
            if (j < padding || j >= gridSize[1] - padding) {
                col.setAttribute('context', 'external');
            }
            col.addEventListener('click', () => {toggleCellState(col)});

            row.appendChild(col);
        }

        document.getElementById('game').appendChild(row);
    }

    document.getElementById('togglegame').addEventListener('click', () => {
        gameon = !gameon;
    });
}

function getCurrentState() {
    let data = {};

    for (let cell of document.getElementsByClassName('col')) {
        if (cell.getAttribute('state') !== 'alive') {
            continue;
        }

        data[cell.getAttribute('row') + ',' + cell.getAttribute('col')] = {
            row: parseInt(cell.getAttribute('row')),
            col: parseInt(cell.getAttribute('col')),
            state: cell.getAttribute('state'),
        };
    };

    return data;
}

function startWsConnection(name) {
    const socket = new WebSocket('ws://localhost:8181');

    socket.addEventListener('open', function (e) {
        // socket.send(JSON.stringify({
        //     action: 'start-game',
        //     data: {
        //         gridSize: gridSize
        //     }
        // }));
    });

    socket.addEventListener('message', function (e) {
        let parsedData = JSON.parse(e.data).data;
        Object.keys(parsedData).map((key) => {
            document.getElementById('col-' + key).setAttribute('state', parsedData[key].state);
        });
    });

    socket.addEventListener('close', function (e) {
        console.log('Connection terminated with status ' + e.code + ': ' + e.reason);
        // alert(e.reason);
    });

    let interval = setInterval(() => {
        if (!gameon) {
            return;
        }

        socket.send(JSON.stringify({
            action: "new-state",
            data: {
                grid: getCurrentState(),
                gridSize: gridSize,
            },
        }));
    }, 1000);
}

window.onload = (event) => {
    buildGrid();
    startWsConnection(name);
};