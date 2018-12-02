modifyExpectedBatchStatus distributeOption #股权期权后端

开发版本
node v9.10.1
npm 5.6.0

安装
```bash
cp config.example.js config.js
npm install
npm start ————启动后端
yarn run frontend/npm run frontend ————启动前端
```


## 2018.6.26更新 api

####修改管理员密码

```
post: http://localhost:3001/user/changePw
    {
        id: //管理员 id
        password: //原密码
        newPassword: //新密码
    }
```

####注册管理员

```
post: http://localhost:3001/user/registerAdmin
    {
        name: //管理员姓名
        phone_number: //电话号码
        id_card: //身份证号
        username: //用户名
        password: //密码
    }
```

## 前端方法

### 期权相关：

symbol

```
window.symbol(contractAddress)
    contractAddress: string
```
Total supply

```
window.totalSupply(contractAddress)
```
获取行权价格

```
window.getExercisePrice(contractAddress)
```
设置行权价格

```
window.setExercisePrice(contractAddress, price)
    price: string
```

获取账户余额

```
window.getBalance(contractAddress, account)
    account: string
```
查询账户是否有权限持有期权

```
window.getAccountRight(contractAddress, account)
```
发放期权

```
window.distributeOption(contractAddress, expectedList)
    contractAddress: string
    expectedList: []
```
!!!解锁期权

```
window.unlockOption(contractAddress, ratio, symbol, period)
    ratio: int 0-100,
    symbol: string
```
回收期权

```
window.reclaimOption(contractAddress, account)
```
授予持币权限

```
window.grantOptionRights(contractAddress, accounts[])
```

!!!移除持币权限

```
window.removeOptionRights(accounts[], period)
```



### 股权相关：

symbol

```
window.symbolShare()
```

decimals

```
window.decimalsShare()
```

账户余额

```
window.balanceOfShare(account)
```

Total supply

```
window.totalSupplyShare()
```

冻结期权操作

```
window.freezeShare(endTime)
	endTime: int, unix timestamp
```

解冻期权操作

```
window.defrostShare()
```

为股权合约添加期权合约地址白名单

```
window.addOptionAddress(addresses)
	addresses: [string]
```

从股权合约中移除期权合约地址

```
window.delOptionAddress(addresses)
	addresses: [string]
```

## API

####在数据库中添加合约信息

```
POST: http://localhost:3000/contract/new
{
	contract_address=,
	symbol=,
	supply=,
	open_time=,
    expire_time=,
    price=
}
```

####获得交易记录

```
GET: http://localhost:3001/contract/getTokenTransactions?period=1&type=4&offset=0&limit=10
	address=, //用户钱包，为null就返回所有交易记录
	period=, //期号
	type=, //1 常规交易 2 回收 3 发行 4 解锁 5 行权，为null就返回所有类型
	offset=, //开始行数
	limit= //每页行数
```
>返回：
```json
{
    "status":0,
    "data":{
        "totalCount":160,
        "page":1,
        "totalPage":16,
        "records":[{
            "id":10,
            "from":"0xf4af3f8dc3e742f3c26ed1d9fe1b997b6ed1992a",
            "to":"0x5bfb8f17dc0215fdab5eadf520796cf4a76f294e",
            "value":500,
            "txHash":"0x7b4728099c7dce8c3d68676a45beebb2ac75645a5579376c5ab3df188a4c288b", //交易哈希
            "type":1, //期号
            "subtype":3, //交易类型
            "time":"2018-06-06T01:25:04.000Z"
        }]
    }
}
```


####获得合约地址（返回合约完整信息）

```
GET: http://localhost:3000/contract/getContractAddress?symbol=
    symbol= //合约代号为null就返回所有合约
    offset=, //开始行数
    limit= //每页行数
```

####获得合约起止时间

```
GET: http://localhost:3000/contract/getContractTime?symbol=
	symbol= //合约代号为null就返回所有合约
```

####更新数据库中期权合约的锁定比例

```
POST: http://localhost:3000/option/updateOptionLockedRatio
{
	symbol=,
	ratio= //本次解锁比例
}
```

## 用户管理

#####创建用户

> url 

/user/create

> 输入参数

姓名：name

手机号码：phone_number

身份证号：id_card

钱包地址：address

> 返回值

正常：{status:0,data:true}

异常：{status:1001,error: 'error info'}

#####修改用户

> url 

/user/modify

> 输入参数

姓名：name

手机号码：phone_number

身份证号：id_card

钱包地址：address

> 返回值

正常：{status:0,data:true}

异常：{status:1002,error: 'error info'}

#####查询记录总数

> url  （不包括已删除）

/user/getCount

> 输入参数

参数名：parameter  ，查询范围包括：姓名(模糊查询 )、手机号、身份证号、钱包地址

> 返回值

类型: int

#####分页查询

> url  （不包括已删除）

/user/getList

> 输入参数

参数名：parameter  ，查询范围包括：姓名(模糊查询 )、手机号、身份证号、钱包地址

参数名：offset  分页参数

参数名：limit 分页参数

查询范围： 从 offset 到 limit

> 返回值

类型: [{id: ,name: ,phone_number: ,id_card: ,address: ,createdAt: ,updatedAt:},{}]

#####删除用户

> url  

/user/del

> 输入参数

参数名：id

> 返回值

正常：{status:0,data:true}

异常：{status:1003,error: 'error info'}

#####单个查询

> url  

/user/getUser

> 输入参数

参数名：id

参数名：address

说明：两个参数二选一

> 返回值

{id: ,name: ,phone_number: ,id_card: ,address: ,createdAt: ,updatedAt:}

#####导入用户

> url  

/user/importUsers

> 输入参数

参数名：file （文件类型为xlsx）

> 返回值

正常：{status:0,data:true}

异常：{status:1004,error: 'error info'}



## 期权管理

##### 预发放批次

> url 

GET: /option/getExpectedBatch

> 输入参数

期权ID: id  

> 返回值

批次ID： id

批次名称: name

批次状态：status , 0 未发放 1 已发放

总数量: total

创建时间： createdAt



##### 预发放批次(单个)

> url 

GET: /option/getExpectedBatchById

> 输入参数

批次ID: id  

> 返回值

批次ID： id

合约ID：option_id

批次名称:name

状态:status

合约地址:contract_address



##### 预发放名单

> url 

GET: /option/getExpectedList

> 输入参数

批次ID: id  

> 返回值

名单ID： id

用户ID:  user_id

发放数量: value

用户姓名：name

手机号码：phone_number

身份证号：id_card

钱包地址：address



##### 创建预发放批次

> url 

POST: /option/addExpectedBatch

> 输入参数

期权ID: option_id  

批次名称：name

> 返回值



##### 修改预发放批次状态

> url 

POST: /option/modifyExpectedBatchStatus

> 输入参数

批次ID: id 

> 返回值



##### 修改预发放批次

> url 

POST: /option/modifyExpectedBatch

> 输入参数

批次ID: id 

批次名称:name

> 返回值



##### 添加预发放名单

> url 

POST: /option/addExpectedList

> 输入参数

批次ID: batch_id 

用户ID:user_id

> 返回值



##### 删除预发放名单

> url 

POST: /option/delExpectedList

> 输入参数

名单ID: id 

> 返回值

##### 修改预发放数量

> url 

POST: /option/modifyExpectedList

> 输入参数

名单ID: id 

发放数量:value

> 返回值



