const readline = require('readline');
const fs = require('fs');

function cacheContents(callLogs) {
    let cache = [];
    let memory = {};

    callLogs.forEach(log => {
        let timestamp = log[0];
        let item_id = log[1];

        if (!memory[item_id]) {
            memory[item_id] = { priority: 0, access_count: 0 };
        }

        memory[item_id].priority = Math.max(0, memory[item_id].priority - 1);
        memory[item_id].access_count += 1;

        if (memory[item_id].access_count > 1) {
            memory[item_id].priority += 2 * (memory[item_id].access_count - 1);
        }

        if (memory[item_id].priority <= 3 && cache.includes(item_id)) {
            let index = cache.indexOf(item_id);
            cache.splice(index, 1);
        }

        if (memory[item_id].priority > 5) {
            cache.push(item_id);
            memory[item_id].priority = 0;
        }
    });

    cache.sort((a, b) => a - b);
    return cache.length > 0 ? cache : [-1];
}

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

let callLogs = [];
rl.question('Enter number of call logs: ', (callLogs_rows) => {
    console.log('Enter call logs (format: timestamp item_id):');
    rl.on('line', (callLog) => {
        callLogs.push(callLog.split(' ').map(Number));

        if (callLogs.length == callLogs_rows) {
            rl.close();
            let result = cacheContents(callLogs);
            console.log(result.join('\n'));
        }
    });
});