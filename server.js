var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http, {
    cors: {
        extraHeaders: {
            'Access-Control-Allow-Origin': '*'
        }
    }
});
var Redis = require('ioredis');
var redis = new Redis();
var users = {};

http.listen(8005, () => {
    console.log('listening on *:8005');
});

redis.subscribe('private-channel', function () {
    console.log('Subscribed to Private channel');
});

redis.on('message', function(channel, message) {
    message = JSON.parse(message);
    if (channel == 'private-channel') {
        let data = message.data.data;
        let receiver_id = data.receiver_id;
        let event = message.event;

        io.to(`${users[receiver_id]}`).emit(channel + ':' + message.event, data);
    }
});

io.on('connection', function (socket) {
    socket.on("user_connected", function (user_id) {
        users[user_id] = socket.id;
        // console.log(users);
        io.emit('updateUserStatus', users);
        // console.log("user connected " + user_id);
    });

    socket.on('disconnect', function () {
        var i = getKeyByValue(users, socket.id);
        delete users[i];
        io.emit('updateUserStatus', users);
        // console.log(users);
    });
});

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}
