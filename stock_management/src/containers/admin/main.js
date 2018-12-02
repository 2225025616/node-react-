import React, { PureComponent } from 'react'
import { Menu, Icon, Avatar, Dropdown, Layout } from 'antd'
import { Link, Route, Switch, Redirect } from 'react-router-dom'
import menuList from '../../config/menu'
import { loginSuccessPage, loginSuccessMenu } from '../../config'
import UserList from './user'
import UserType from './userType'
import StockList from './stock'
import TransList from './trans'
import StockType from './stockType'
import Grant from './grant'
import Allot from './allotList'
import './main.less'
const { Header, Sider, Content } = Layout

const Item = Menu.Item

const components = [
  [
    UserList
  ],
  [
    StockList
  ],
  [
    TransList
  ],
]

class Main extends PureComponent {
  constructor(props){
    super(props)
    this. state = {
      openKeys: [],
      selectedKeys: [],
      collapsed: false,
    }
  }
  onOpenChange (openKeys) {
    const latestOpenKey = openKeys.find(key => this.state.openKeys.indexOf('' + key) === -1)
    let rootSubmenuKeys = menuList.filter(item => item.children).map(i => i.key)
    if (rootSubmenuKeys.indexOf('' + latestOpenKey) === -1) {
      this.setState({ openKeys })
    } else {
      this.setState({
        openKeys: latestOpenKey ? [latestOpenKey] : []
      })
    }
  }
  toggle = () => {
    this.setState({
      collapsed: !this.state.collapsed,
    });
  }
  onSelect = ({selectedKeys}) => {
    this.setState({
      selectedKeys
    })
  }
  filter (pathname) {
    let openKey = menuList.filter(item => {
      if (item.children) {
        return !item.children.every(i => {
          if (i.key !== pathname) {
            return true
          }
          return false
        })
      }
      return true
    })
    return openKey
  }
  exit = () => {
    const { history } = this.props
    localStorage.removeItem('admin')
    history.push('/login')
  }
  componentWillMount () {
    const { history } = this.props
    let openKey = this.filter(history.location.pathname)
    let selectedKeys = []
    selectedKeys = [history.location.pathname]
    if (!openKey.length) {
      let arr = history.location.pathname.split('/')
      if (arr.length > 2) {
        let parentPath = '/' + arr[1] + '/' + arr[2]
        openKey = this.filter(parentPath)
        selectedKeys = [parentPath]
      } else {
        openKey = [{
          key: '/main/user'
        }]
        selectedKeys = [loginSuccessPage]
      }
    }
    this.setState({
      openKeys: [openKey[0].key],
      selectedKeys: selectedKeys
    })
  }
  render () {
    const menu = (
      <Menu>
        <Menu.Item>
          <a><Icon type="user" /> 修改密码</a>
          <a onClick={this.exit}><Icon type="logout" /> 退出</a>
        </Menu.Item>
      </Menu>
    );
    if (!localStorage.getItem('admin')) {
      this.exit()
    }
    const user = JSON.parse(localStorage.getItem('admin'))
    return (
        <Layout>
            <Sider
            trigger={null}
            collapsible
            collapsed={this.state.collapsed}
            >
              <Menu
                  openKeys={this.state.openKeys}
                  selectedKeys={this.state.selectedKeys}
                  onOpenChange={this.onOpenChange}
                  onSelect={this.onSelect}
                  mode="inline"
                  theme="dark"
              >
                  <Item className="header">
                    <Icon type='appstore-o' />
                    <span>期权管理后台</span>
                  </Item>
                  {menuList.map((item) => {
                      return (
                          <Item key={item.key}>
                          <Link to={item.key}>
                              <Icon type={item.icon} />
                              <span>{item.title}</span>
                          </Link>
                          </Item>
                      )
                  })}
              </Menu>
            </Sider>
            <Layout>
              <Header className="rightHeader">
                  <Icon
                    className="trigger"
                    type={this.state.collapsed ? 'menu-unfold' : 'menu-fold'}
                    onClick={this.toggle}
                  />
                  <div>
                    <Dropdown overlay={menu}>
                      <Avatar className="avatar" size="large" icon="user" />
                    </Dropdown>
                    <span className="username">{user.name}</span>
                  </div>
              </Header>
              <Content style={{ margin: '24px 16px', padding: 24, background: '#fff', minHeight: 280 }}>
                  <Switch>
                    <Route exact path="/main" render={() => <Redirect to={'/main/user'} />} />
                      <Route path="/main/user/:type" component={UserType}/>
                      <Route path="/main/stock/:type" component={StockType}/>
                      <Route path="/main/grant" component={Grant}/>
                      <Route path="/main/allot" component={Allot}/>
                      {menuList.map((item,index) =>
                          (
                            <Route exact key={item.key}
                              path={item.key}
                              component={components[index][0]}
                          />)
                      )}
                  </Switch>
              </Content>
            </Layout>
        </Layout>
    )
  }
}

export default Main

