import React, { PureComponent } from 'react'
import AsyncBundle from '../components/AsyncBundle'
import { Route, Switch, Redirect } from 'react-router-dom'
import { BackTop } from 'antd'
import Main from './admin/main'
import ErrorBoundary from '../components/ErrorBoundary'
import Login from './admin/login'

class App extends PureComponent {
  render () {
    const { history } = this.props
    window.reactHistory = history
    // const user = JSON.parse(localStorage.getItem('admin'))
    return (
      <ErrorBoundary>
        <Switch>
          <Route exact path="/" render={(props) => {
            return localStorage.getItem('admin') ? <Redirect to={'/main/user'}/> : <Redirect to='/login'/>
          }}/>
          <Route path="/login" component={Login}/>
          <Route path="/main" component={Main}/>
          <Redirect to="/404" />
          <div>
            <BackTop />
          </div>
        </Switch>
      </ErrorBoundary>
    )
  }
}

export default App
