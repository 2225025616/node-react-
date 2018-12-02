//股权需要主账户私钥的相关操作
const shareABI = window.CONFIG.share_abi;
const shareAddress = window.CONFIG.share_contract_address;


let _symbolShare= () => {
    return new Promise((resolve, reject) => {
        let option = web3.eth.contract(shareABI).at(shareAddress);
        option.name((err, res) => {
            if(!err){
                resolve(res);
            } else {
                reject(err);
            }
        })
    })
};

let _decimalsShare = () =>{
    return new Promise((resolve, reject)=>{
        let option = web3.eth.contract(shareABI).at(shareAddress);
        option.decimals((err, res)=>{
            if (!err) {
                resolve(res);
            } else {
                reject(err);
            }
        })
    })
};

window.balanceOfShare  = (account) =>{
    return new Promise((resolve, reject) =>{
        let option = web3.eth.contract(shareABI).at(shareAddress);
        option.balanceOf(account,(err, res)=>{
            if (!err) {
                resolve({
                    status: 0,
                    data: JSON.parse(res)
                });
            } else {
                reject(err);
            }
        })
    })
};


let _totalSupplyShare  = () => {
    return new Promise((reslove, reject)=>{
        let option = web3.eth.contract(shareABI).at(shareAddress);
        option.totalSupply((err,res)=>{
            if (!err) {
                reslove(res);
            } else {
                reject(err);
            }
        })
    })
};

var _freezeEndTime= () => {
    return new Promise((resolve, reject) => {
        let option = web3.eth.contract(shareABI).at(shareAddress);
        option.freezeEndTime((err, res) => {
            if(!err){
                console.log("result: "+ res);
                resolve(res);
            } else {
                reject(err);
            }
        })
    })
};


window.symbolShare  = async function(){
    try{
        let symbol = await _symbolShare ();
        return {
            symbol: symbol
        }
    } catch (e){
        return {
            status: 2002,
            error: e
        }
    }
};

window.freezeEndTime  = async function(){
    return new Promise((resolve, reject) => {
        let option = web3.eth.contract(shareABI).at(shareAddress);
        let re=null
        option.freezeEndTime((err, res) => {
            if(!err){
                // console.log("result: "+ typeof Number(res)*1000);
                re={
                    status:0,
                    data:Number(res)
                }
                resolve(re);
            } else {
                reject(err);
            }
        })
    })
};

// window.freezeEndTime  = async function(){
//     try{
//         let endTime = await _freezeEndTime ();
//         return {
//             status: 0,
//             endTime: endTime
//         }
//     } catch (e){
//         return {
//             status: 2002,
//             error: e
//         }
//     }
// };

window.decimalsShare  = async function(){
    try{
        let decimals = await _decimalsShare ();
        return {
           decimals: decimals
        }
    } catch (e){
        return {
            status: 2002,
            error: e
        }
    }
};

// window.balanceOfShare  = async function( account){
//     try{
//         let balanceOf = await _balanceOfShare (account);
//         return {
//            balance: balanceOf
//         }
//     } catch (e){
//         return {
//             status: 2002,
//             error: e
//         }
//     }
// };

window.totalSupplyShare  = async function(){
    try{
        let totalSupply = await _totalSupplyShare ();
        return {
           supply: totalSupply
        }
    } catch (e){
        return {
            status: 2002,
            error: e
        }
    }
};

//开始锁定期，暂停股权转让 endTime: unix timestamp
window.freezeShare = async function(endTime){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        return new Promise((resolve, reject)=>{
            let res = null
            setTimeout(
                share.freeze(endTime, (err, res) => {
                    if(err === null){
                        console.log(res);
                        res =  {
                            status: 0,
                            txHash: res
                        };
                    } else {
                        console.log(err);
                        res = {
                            status: 2002,
                            error: err
                        };
                    }
                    resolve(res)
                }),
            100);
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};

//结束锁定期，开启股权转让
window.defrostShare = async function(){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        return new Promise((resolve, reject) => {
            let res=null
            setTimeout(
                share.defrost( (err, res) => {
                    if(err === null){
                        console.log(res);
                        res = {
                            status: 0,
                            txHash: res
                        };
                    } else {
                        console.log(err);
                        res = {
                            status: 2002,
                            error: err
                        };
                    }
                    resolve(res);
                }),
            100);
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};

//添加合法的期权合约地址 addresses: address[]，需要调api：/contract/new (post)
/*{
	"contractAddress":"0xdaa052e499e9eeafb3c48a642e945fdcf7635c2a",
	"symbol":"NSO",
	"period":1,
	"expireTime":1559354873,
	"openTime":"1527819825",
	"exercisePrice":0.9
}*/
window.addOptionAddress = async function(addresses){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        return new Promise(function(resolve, reject){
            setTimeout(function(){
                share.addOptionAddress(addresses, (err, res) => {
                    if(err === null){
                        console.log(res);
                        //调api
                        resolve ({
                            status: 0,
                            txHash: res
                        });
                    } else {
                        console.log(err);
                        resolve ({
                            status: 2002,
                            error: err
                        });
                    }
                })
            },100)
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};

//移除无效的期权合约地址 addresses: address[]
window.delOptionAddress = async function(addresses){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        await share.delOptionAddress(addresses, (err, res) => {
            if(err === null){
                console.log(res);
                return {
                    status: 0,
                    txHash: res
                };
            } else {
                console.log(err);
                return {
                    status: 2002,
                    error: err
                };
            }
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};

//股权 transfer，需要调api：/share/transfer?
window.transferShare = async function(address, amount){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        await share.transfer(address, amount, (err, res) => {
            if(err === null){
                console.log(res);
                //在这里调api
                return {
                    status: 0,
                    txHash: res
                };
            } else {
                console.log(err);
                return {
                    status: 2002,
                    error: err
                };
            }
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};

//股权 transferFrom，需要调api：/share/transfer?
window.transferFromShare = async function(from, to, amount){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        await share.transferFrom(from, to, amount, (err, res) => {
            if(err === null){
                console.log(res);
                //在这里调api
                return {
                    status: 0,
                    txHash: res
                };
            } else {
                console.log(err);
                return {
                    status: 2002,
                    error: err
                };
            }
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};

//股权 approve, spender: address
window.approveShare = async function(spender, amount){
    try {
        let share = web3.eth.contract(shareABI).at(shareAddress);
        await share.approve(spender, amount, (err, res) => {
            if(err === null){
                console.log(res);
                return {
                    status: 0,
                    txHash: res
                };
            } else {
                console.log(err);
                return {
                    status: 2002,
                    error: err
                };
            }
        })
    } catch(e) {
        console.log(e);
        return {
            status: 2003,
            error: e
        };
    }
};