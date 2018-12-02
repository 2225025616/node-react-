const Sequelize = require('sequelize');
const {dbPool} = require('../lib/db');

let User = dbPool.define('user', {
    id: {
      type: Sequelize.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: Sequelize.STRING
    },
    phone_number: {
      type: Sequelize.STRING
    },
    id_card: {
      type: Sequelize.STRING
    },
    address: {
      type: Sequelize.STRING,
      unique: true
    },
    department : {
      type: Sequelize.STRING,
    },
    createdAt: {
      type: Sequelize.DATE
    },
    updatedAt: {
      type: Sequelize.DATE
    },
    status: {
      type: Sequelize.INTEGER
    }
  }, {
    freezeTableName: true,
    timestamps: false
  });
  
  let Admin = dbPool.define('admin', {
    id: {
      type: Sequelize.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    name: {
      type: Sequelize.STRING
    },
    phone_number: {
      type: Sequelize.STRING
    },
    id_card: {
      type: Sequelize.STRING
    },
    createdAt: {
      type: Sequelize.DATE
    },
    updatedAt: {
      type: Sequelize.DATE
    },
    username: {
      type: Sequelize.STRING
    },
    pw: {
      type: Sequelize.STRING
    },
    salt: {
      type: Sequelize.STRING
    },
    token: {
      type: Sequelize.STRING
    }
  }, {
    freezeTableName: true,
    timestamps: false
  });



const Transaction = dbPool.define('transactions', {
    id : {
      type: Sequelize.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    from : {
      type: Sequelize.STRING
    },
    to : {
      type: Sequelize.STRING
    },
    value : {
      type: Sequelize.INTEGER
    },
    txHash : {
      type: Sequelize.STRING
    },
    type : {
      type: Sequelize.INTEGER
    },
    subtype : {
      type: Sequelize.INTEGER
    },
    time : {
      type: Sequelize.DATE
    }
  }, {
    freezeTableName: true,
    timestamps: false
  });



//   User.hasMany(Transaction, {foreignKey: 'from', sourceKey: 'address'});
//   User.hasMany(Transaction, {foreignKey: 'to', sourceKey: 'address'});
  Transaction.belongsTo(User, {foreignKey: 'from', targetKey: 'address',as: 'fromUser'});
  Transaction.belongsTo(User, {foreignKey: 'to', targetKey: 'address',as:'toUser'});


module.exports = {
    User,
    Admin,
    Transaction
};