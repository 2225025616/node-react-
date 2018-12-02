import axios from 'axios'
import qs from 'qs'
import '../../controller/config'
import apiUrl from '../config/apiUrl'
import { message } from 'antd'
import { transformDate, responseHandle } from './common'
let api = axios.create({
  baseURL: window.CONFIG.backend_sever,
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
  },
  responseType: 'json'
})

api.defaults.validateStatus = (status) => {
  return true
}

api.interceptors.request.use(config => {
  let token = JSON.parse(localStorage.getItem('admin') || '{}').token
  if (token && (config.url !== apiUrl.login)) {
    config.headers.common['Authorization'] = token
  }
  // 去除''选项
  let data = {}
  for (let key in config.data) {
    if (config.data[key] !== '') {
      data[key] = config.data[key]
    }
  }
  config.data = data
  config.data = qs.stringify(config.data)
  return config
}, error => {
  return Promise.reject(error)
})

// 修改返回数据格式
api.defaults.transformResponse = (res) => {
  if (res) {
    // 处理时间戳
    if (typeof res !== 'object') {
      res = JSON.parse(res)
    }
    res = transformDate(res)
    return res
  } else {
    // message.error(serverErrorMsg)
  }
}

api.interceptors.response.use(response => {
  if (response.request.responseURL.includes('forceDownload')) {
    var decodedString = String.fromCharCode.apply(null, new Uint8Array(response.data))
    if (decodedString.length < 100) {
      response.data = JSON.parse(decodedString)
      return responseHandle(response.data, message)
    } else {
      return response.data
    }
  } else {
    return responseHandle(response.data, message)
  }
}, error => {
  return Promise.reject(error)
})

export default api
