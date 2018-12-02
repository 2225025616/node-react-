import { serverErrorMsg } from '../config'

export const deepCopy = (source, object = this) => {
  const type = typeof source
  if (type === 'object') {
    let obj = []
    if (typeof source.length === 'undefined') {
      obj = {}
    }
    for (let key in source) {
      obj[key] = deepCopy(source[key], object)
    }
    return obj
  } else if (type === 'function') {
    return source.bind(object)
  } else {
    return source
  }
}

export const compare = (obj1, obj2) => {
  let result = true
  if (obj1 === null || obj2 === null || obj1 === undefined || obj2 === undefined) {
    return obj1 === obj2
  }
  if (typeof obj1 === 'object' && typeof obj2 === 'object') {
    const keys1 = Object.keys(obj1)
    const keys2 = Object.keys(obj2)
    if (keys1.length !== keys2.length) {
      result = false
    }
    for (let key in obj1) {
      const type = typeof obj1[key]
      if (type === 'object') {
        if (!compare(obj1[key], obj2[key])) {
          result = false
        }
      } else if (type === 'function') {
      } else {
        if (obj1[key] !== obj2[key]) {
          result = false
        }
      }
    }
  } else {
    return obj1 === obj2
  }
  return result
}
export const filterNull = (obj) => {
  let filterObj = {}
  for (let key in obj) {
    if (obj[key] !== null) {
      filterObj[key] = obj[key]
    }
  }
  return filterObj
}

export const formatTime = (time, showAll = false) => {
  function format (value) {
    return Number(value) < 10 ? `0${value}` : value
  }
  let year = new Date(time).getFullYear()
  let month = format(new Date(time).getMonth()+1)
  let day = format(new Date(time).getDate())

  let hour = format(new Date(time).getHours())
  let min = format(new Date(time).getMinutes())
  let seconds = format(new Date(time).getSeconds())
  return showAll? `${year}-${month}-${day} ${hour}:${min}:${seconds}`:`${hour}:${min}:${seconds}`
}
export const transformDate = (fmt) =>{ //author: meizz
  var o = {
    "M+" : new Date().getMonth()+1,                 //月份
    "d+" : new Date().getDate(),                    //日
    "h+" : new Date().getHours(),                   //小时
    "m+" : new Date().getMinutes(),                 //分
    "s+" : new Date().getSeconds(),                 //秒
    "q+" : Math.floor((new Date().getMonth()+3)/3), //季度
    "S"  : new Date().getMilliseconds()             //毫秒
  };
  if(/(y+)/.test(fmt))
    fmt=fmt.replace(RegExp.$1, (new Date().getFullYear()+"").substr(4 - RegExp.$1.length));
  for(var k in o)
    if(new RegExp("("+ k +")").test(fmt))
  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
  return fmt;
}
export const responseHandle = (response, message) => {
  if (typeof response === 'string') {
    response = JSON.parse(response)
  }
  if (!response) {
    if (response !== undefined) {
      message.error(serverErrorMsg)
    }
    return Promise.reject(new Error(serverErrorMsg))
  }
  // if (response.status === undefined) {
  //   return Promise.reject(new Error('返回数据格式存在问题,请联系管理员'))
  // }
  let code = parseInt(response.status)
  if (code === 0) {
    // times = 0
    if (response.data) {
      if (response.total) {
        return Object.assign(response.data,response.total[0])
      }
      return response.data
    } else {
      return response
    }
  } else if (code === 1) {
    message.error(response.error)
    return Promise.reject(new Error(response.error))
  } else if (code === 2) {
    message.error('传递参数出错')
    return Promise.reject(new Error('传递参数出错'))
  } else if (code === 401) {
    message.error('请重新登录')
    localStorage.removeItem('admin')
    window.reactHistory.push('/login')
    return Promise.reject(new Error('请重新登录'))
    // }
  } else if (code === 403 || code === 400 || code === 405) {
    message.error('请重新登录', () => {
      localStorage.removeItem('admin')
      window.reactHistory.push('/login')
    })
  } else {
    message.error(response.errorMsg)
  }
  return Promise.reject(new Error(response.errorMsg))
}


export const getUniqueKey = (arr, type = 'key', key = null, isNumber = false) => {
  if (key === null) {
    if (isNumber) {
      key = 0
    } else {
      key = '0'
    }
  }
  let hasExist = arr.filter((item) => {
    if (item[type] === key) {
      return true
    }
  })
  if (hasExist.length) {
    return getUniqueKey(arr, type, isNumber ? key + 1 : parseInt(key) + 1 + '', isNumber)
  } else {
    return key
  }
}

export const transArr = (arr,key) => {
  let list = []
  arr.map(item=>{
    list[list.length] = item[key]
    // console.log(list)
  })
  return list
}
