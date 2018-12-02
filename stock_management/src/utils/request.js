import apiUrl from '../config/apiUrl'
import api from './api'

const commonPost = (url, params, options = {}) => {
  return api.post(url, params, options)
}

const commonGet = (url, params, options = {}) => {
  return api.get(url,{params: params}, options)
}

// export const downFile = (params) => {
//   return commonPost(apiUrl.downFile, params, {responseType: 'arraybuffer'})
// }

export const loginRequest = (params) => {
  return commonGet(apiUrl.login, params)
}
export const getTransName = (params) => {
  return commonGet(apiUrl.getTransName, params)
}

export const getUserList = (params) => {
  return commonGet(apiUrl.getUserList, params)
}
export const delUser = (params) => {
  return commonPost(apiUrl.delUser, params)
}
export const addUser = (params) => {
  return commonPost(apiUrl.addUser, params)
}
export const editUser = (params) => {
  return commonPost(apiUrl.editUser, params)
}
export const userDetail = (params) => {
  return commonGet(apiUrl.userDetail, params)
}

export const getStock = (params) => {
  return commonGet(apiUrl.getStock, params)
}
export const openOption = (params) => {
  return commonGet(apiUrl.openOption, params)
}
export const unlockOption = (params) => {
  return commonPost(apiUrl.unlockOption, params)
}
export const editOption = (params) => {
  return commonPost(apiUrl.editOption, params)
}
export const addOption = (params) => {
  return commonPost(apiUrl.addOption, params)
}
export const tradeList = (params) => {
  return commonGet(apiUrl.tradeList, params)
}

export const addExpectedBatch = (params) => {
  return commonPost(apiUrl.addExpectedBatch, params)
}
export const modifyExpectedList = (params) => {
  return commonPost(apiUrl.modifyExpectedList, params)
}
export const delExpectedList = (params) => {
  return commonPost(apiUrl.delExpectedList, params)
}
export const addExpectedList = (params) => {
  return commonPost(apiUrl.addExpectedList, params)
}
export const modifyExpectedBatch = (params) => {
  return commonPost(apiUrl.modifyExpectedBatch, params)
}
export const modifyExpectedBatchStatus = (params) => {
  return commonPost(apiUrl.modifyExpectedBatchStatus, params)
}
export const getExpectedList = (params) => {
  return commonGet(apiUrl.getExpectedList, params)
}
export const getExpectedBatch = (params) => {
  return commonGet(apiUrl.getExpectedBatch, params)
}
export const getContractById = (params) => {
  return commonGet(apiUrl.getContractById, params)
}
export const getExpectedBatchById = (params) => {
  return commonGet(apiUrl.getExpectedBatchById, params)
}
export const unlockRemind = (params) => {
  return commonPost(apiUrl.unlockRemind, params)
}
export const updateUnlockRemind = (params) => {
  return commonPost(apiUrl.updateUnlockRemind, params)
}
export const getUnlockRemind = (params) => {
  return commonGet(apiUrl.getUnlockRemind, params)
}