pragma solidity ^0.4.18;

contract StockShareToken {
    function exchange(address target, uint256 mintedAmount) external returns (bool success);
}

contract Owner {
    address public owner;
    //添加断路器
    bool public stopped = false;

    function Owner() internal {
        owner = msg.sender;
    }

    modifier onlyOwner {
        require (msg.sender == owner);
        _;
    }

    function transferOwnership(address newOwner) external onlyOwner {
        require (newOwner != 0x0);
        require (newOwner != owner);
        emit OwnerUpdate(owner, newOwner);
        owner = newOwner;
    }

    function toggleContractActive() onlyOwner public {
        //可以预置改变状态的条件，如基于投票人数
        stopped = !stopped;
    }

    modifier stopInEmergency {
        require(stopped == false);
        _;
    }

    modifier onlyInEmergency {
        require(stopped == true);
        _;
    }

    event OwnerUpdate(address _prevOwner, address _newOwner);
}


/**
 * @title SafeMath
 * @dev Math operations with safety checks that throw on error
 */
library SafeMath {
    function mul(uint256 a, uint256 b) internal pure returns (uint256) {
        uint256 c = a * b;
        assert(a == 0 || c / a == b);
        return c;
    }
    function div(uint256 a, uint256 b) internal pure returns (uint256) {
        // assert(b > 0); // Solidity automatically throws when dividing by 0
        uint256 c = a / b;
        // assert(a == b * c + a % b); // There is no case in which this doesn't hold
        return c;
    }
    function sub(uint256 a, uint256 b) internal pure returns (uint256) {
        assert(b <= a);
        return a - b;
    }
    function add(uint256 a, uint256 b) internal pure returns (uint256) {
        uint256 c = a + b;
        assert(c >= a);
        return c;
    }
}

contract Token is Owner{

    using SafeMath for uint256;

    // Public variables of the token
    string public name;
    uint public expireTime;
    uint public openTime;
    string public symbol;
    uint8 public decimals;
    uint256 public totalSupply;
    address public owner;
    string public exercisePrice;

    StockShareToken share;

    //自由期权
    mapping (address => uint256) public freeBalances;
    //锁定期权
    mapping (address => uint256) public lockedBalances;
    //持有期权资格
    mapping (address => bool) public optionRights;

    //代理
    mapping(address => mapping(address => uint)) approved;

    //交易 account: to account
    event Transfer(address indexed from, address indexed to, uint256 value);
    //认证代理
    event Approval(address indexed owner, address indexed spender, uint256 value);
    //换购股权
    event ExchangeShare(address indexed account, uint256 value);
    //发放期权 account: to account
    event Distribute(address indexed account, uint256 value);
    //回收过期期权 account: from account
    event Destroy(address indexed account, uint value);
    //解锁期权
    event Unlock(address indexed account, uint value);

    /** 
     *
     * Fix for the ERC20 short address attack
     * http://vessenes.com/the-erc20-short-address-attack-explained/
     */
    modifier onlyPayloadSize(uint256 size) {
        require(msg.data.length == size + 4);
        _;
    }

    modifier beforeExpire() {
        require(now < expireTime);
        _;
    }

    modifier afterOpen() {
        require(now > openTime);
        _;
    }

    //    //查询账户余额
    //    function balanceOf(address sender) constant external returns (uint256 balance){
    //        return balances[sender];
    //    }

    //查询自由余额
    function balanceOfFree(address sender) constant external returns (uint256 balance){
        return freeBalances[sender];
    }
    //查询锁定余额
    function balanceOfLocked(address sender) constant external returns (uint256 balance){
        return lockedBalances[sender];
    }
    //查询总余额
    function balanceOf(address sender) constant external returns (uint256 balance){
        return lockedBalances[sender] + freeBalances[sender];
    }
    //查询期权权限
    function rightOf(address sender) constant external returns (bool right){
        return optionRights[sender];
    }

    //允许spender多次取出您的帐户，最高达value金额。value可以设置超过账户余额
    function approve(address spender, uint value) external beforeExpire afterOpen returns (bool success) {
        require(optionRights[spender]);
        approved[msg.sender][spender] = value;
        emit Approval(msg.sender, spender, value);

        return true;
    }

    //返回spender仍然被允许从accountOwner提取的金额
    function allowance(address accountOwner, address spender) constant external returns (uint remaining) {
        return approved[accountOwner][spender];
    }

    function _transfer(address _from, address _to, uint256 _value) internal {
        require(_to != 0x0);
        require(_from != _to);
        require(freeBalances[_from] >= _value);
        require(freeBalances[_to].add(_value) > freeBalances[_to]);
        freeBalances[_from] = freeBalances[_from].sub(_value);
        freeBalances[_to] = freeBalances[_to].add(_value);
        emit Transfer(_from, _to, _value);
    }

    function _distribute(address _to, uint256 _value) internal {
        require(_to != 0x0);
        require(msg.sender != _to);
        require(lockedBalances[msg.sender] >= _value);
        require(lockedBalances[_to].add(_value) > lockedBalances[_to]);
        lockedBalances[msg.sender] = lockedBalances[msg.sender].sub(_value);
        lockedBalances[_to] = lockedBalances[_to].add(_value);
        emit Distribute(_to, _value);
    }

    function transfer(address _to, uint256 _value) external stopInEmergency onlyPayloadSize(2 * 32) beforeExpire afterOpen{
        require(optionRights[_to]);
        _transfer(msg.sender, _to, _value);
    }

    function transferFrom(address from, address to, uint256 value) external stopInEmergency beforeExpire afterOpen returns (bool success) {
        require(value > 0);
        require(value <= approved[from][msg.sender]);
        require(value <= freeBalances[from]);
        require(optionRights[to]);

        approved[from][msg.sender] = approved[from][msg.sender].sub(value);
        _transfer(from, to, value);
        return true;
    }

    //解锁期权 ratio: 锁定余额的百分比（75 -> 75%）
    function unlockOption(address[] addresses, uint ratio)public stopInEmergency onlyOwner beforeExpire afterOpen {
        require(addresses.length > 0);
        require(ratio<=100 && ratio >0);

        for(uint i=0;i<addresses.length;i++){
            uint amount = lockedBalances[addresses[i]].mul(ratio).div(100);
            lockedBalances[addresses[i]] = lockedBalances[addresses[i]].sub(amount);
            freeBalances[addresses[i]] = freeBalances[addresses[i]].add(amount);
            emit Unlock(addresses[i], amount);
        }
    }
    //发放锁定期权
    function distributeOption(address[] addresses, uint[] amount)public stopInEmergency onlyOwner beforeExpire afterOpen{
        require(addresses.length > 0);
        require(addresses.length == amount.length);

        for(uint i=0;i<addresses.length;i++){
            _distribute(addresses[i], amount[i]);
            optionRights[addresses[i]] = true;
        }
    }
    //回收全部期权
    function destroyOption(address _address)public stopInEmergency onlyOwner afterOpen{
        uint amount = freeBalances[_address]+lockedBalances[_address];

        freeBalances[_address] = 0;
        lockedBalances[_address] = 0;
        lockedBalances[owner] = lockedBalances[owner].add(amount);

        emit Destroy(_address, amount);
    }

    //授予权限
    function grantRights(address[] addresses)public stopInEmergency onlyOwner beforeExpire afterOpen{
        for(uint i=0;i<addresses.length;i++){
            optionRights[addresses[i]] = true;
        }
    }

    //移除权限
    function removeRights(address _address)public stopInEmergency onlyOwner afterOpen{
        delete optionRights[_address];
        // destroyOption(_address);
    }

    //换购股权
    function exchangeShare(uint amount)public stopInEmergency beforeExpire afterOpen{
        require(freeBalances[msg.sender] >= amount);
        uint balance = freeBalances[msg.sender];
        freeBalances[msg.sender] = freeBalances[msg.sender].sub(amount);
        share.exchange(msg.sender, amount);
        assert(balance == freeBalances[msg.sender].add(amount));
        emit ExchangeShare(msg.sender, amount);
    }

    //设置行权价格
    function setExercisePrice(string price)public stopInEmergency onlyOwner beforeExpire afterOpen{
        exercisePrice = price;
    }

    //查询行权价格
    function getExercisePrice() constant external returns (string price){
        return exercisePrice;
    }

    //设置期权过期时间
    function setExpireTime(uint expire) public onlyOwner stopInEmergency{
        expireTime = expire;
    }

}

contract StockOptionToken is Token {
    //optionSympol: 当期期权代号, initialSupply: 当期期权供给量, expire: 当期期权过期时间, shareAddress: 股权合约地址, price: 行权价格
    function StockOptionToken(string optionSymbol, uint256 initialSupply, uint open, uint expire, address shareAddress, string price) public {
        //uint256 initialSupply = 150000000;
        symbol = optionSymbol;
        openTime = open;
        expireTime = expire;
        name = "Numchain Stock Option";
        decimals = 0;
        owner = msg.sender;
        optionRights[owner] = true;
        share = StockShareToken(shareAddress);
        exercisePrice = price;
        totalSupply = initialSupply * 10 ** uint256(decimals);
        lockedBalances[msg.sender] = totalSupply;
    }
}