module.exports = {
    listen: {
        host: '0.0.0.0',
        port: 3001
    },
    mysql: {
        host: "192.168.3.116",
        port: "3306",
        user: "root",
        password: "root",
        database: "stock"
    },
    stockContract: {
        address: '0xdaa052e499e9eeafb3c48a642e945fdcf7635c2a',
        ownerAddress: '0xf4af3f8dc3e742f3c26ed1d9fe1b997b6ed1992a',
        ownerPrivateKey: '0xefd2c2fd3d55654535cc531cc9c0f60bd3d7963688fe21f2db1c98f18533cad6'
    },
    loginTimeout: 60*30,
    listenBlocks: false
};
