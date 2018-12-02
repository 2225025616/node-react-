import 'babel-polyfill'
import React from 'react'
import ReactDOM from 'react-dom'
import zhCN from 'antd/lib/locale-provider/zh_CN'
import { BrowserRouter, Route } from 'react-router-dom'
import App from './containers/App'
import './styles/index.less'
import 'antd/lib/date-picker/style/css'
import moment from 'moment'
import 'moment/locale/zh-cn'
import { message, LocaleProvider } from 'antd'

moment.locale('zh-cn')

message.config({
  duration: 2
})

let Router = BrowserRouter
ReactDOM.render(
  <LocaleProvider locale={zhCN}>
    <Router>
      <Route path="/" component={App}/>
    </Router>
  </LocaleProvider>
  , document.getElementById('root')
)
