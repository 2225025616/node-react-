pragma solidity ^0.4.18;


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
        OwnerUpdate(owner, newOwner);
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
    string public symbol;
    uint8 public decimals;
    uint256 public totalSupply;
    address public owner;
    uint256 public freezeEndTime;

    // This creates an array with all balances
    mapping (address => uint256) public balances;
    //代理
    mapping(address => mapping(address => uint)) approved;
    //期权合约地址
    mapping(address => bool) optionAddress;

    event Transfer(address indexed from, address indexed to, uint256 value);
    event Approval(address indexed owner, address indexed spender, uint256 value);
    event Exchange(address indexed from, uint256 value);
    event Freeze(uint endTime);

    modifier pauseExchange {
        require(now >= freezeEndTime);
        _;
    }

     /**
      *
      * Fix for the ERC20 short address attack
      * http://vessenes.com/the-erc20-short-address-attack-explained/
      */
    modifier onlyPayloadSize(uint256 size) {
        require(msg.data.length == size + 4);
        _;
    }


    //查询账户余额
    function balanceOf(address sender) constant external returns (uint256 balance){
        return balances[sender];
    }

    //允许spender多次取出您的帐户，最高达value金额。value可以设置超过账户余额
    function approve(address spender, uint value) external returns (bool success) {
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
        require(balances[_from] >= _value);
        require(balances[_to].add(_value) > balances[_to]);
        balances[_from] = balances[_from].sub(_value);
        balances[_to] = balances[_to].add(_value);   
        emit Transfer(_from, _to, _value);
    }

    
    function transfer(address _to, uint256 _value) external stopInEmergency pauseExchange onlyPayloadSize(2 * 32){
        _transfer(msg.sender, _to, _value);
    }

    
    function transferFrom(address from, address to, uint256 value) external stopInEmergency pauseExchange returns (bool success) {
        require(value > 0);
        require(value <= approved[from][msg.sender]);
        require(value <= balances[from]);

        approved[from][msg.sender] = approved[from][msg.sender].sub(value);
        _transfer(from, to, value);
        return true;
    }


    function exchange(address target, uint256 mintedAmount) external stopInEmergency pauseExchange {
        require(optionAddress[msg.sender]);//调用人必须在地址列表
        require(mintedAmount > 0);
        balances[target] = balances[target].add(mintedAmount);
        totalSupply = totalSupply.add(mintedAmount);
        assert(balances[target] >= mintedAmount);
        emit Exchange(target, mintedAmount);
    }

    //开启锁定期暂停转让
    function freeze(uint _endTime) onlyOwner stopInEmergency public {
        require(_endTime > now);
        freezeEndTime = _endTime;
        emit Freeze(_endTime);
    }

    function defrost() onlyOwner stopInEmergency public {
        freezeEndTime = now;
        emit Freeze(now);
    }

    //设置期权合约地址列表
    function addOptionAddress(address[] _optionAddress) onlyOwner public {
        for (uint i=0; i<_optionAddress.length; i++) {
             optionAddress[_optionAddress[i]] = true;
         }
    }

    //移除持币权限
    function delOptionAddress(address _optionAddress)onlyOwner public {
        delete optionAddress[_optionAddress];
    }


}


contract StockShareToken is Token {

  function StockShareToken() public {
    uint256 initialSupply = 0;
    name = "Numchain Stock Share";
    symbol = "NSS";
    decimals = 0;
    owner = msg.sender;
    
    totalSupply = initialSupply * 10 ** uint256(decimals);
    assert (totalSupply >= initialSupply);

    balances[msg.sender] = totalSupply;
    emit Transfer(0x0, msg.sender, totalSupply);
  }
}
