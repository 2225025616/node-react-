import React, { Component } from 'react'
import './login.less'
import { Form, Input, Icon, Button} from 'antd'
import { loginRequest } from '../../utils/request'
import './login.less'
const FormItem = Form.Item

class LoginForm extends Component {
  constructor(props){
    super(props)
    this.state = {
      isLogin: false,
    }
  }

  handleSubmit = (e) => {
    e.preventDefault()
    const { history } = this.props
    this.props.form.validateFields((err, values) => {
      if (!err) {
        loginRequest(values).then((data) => {
          if (data) {
            localStorage.setItem('admin', JSON.stringify(data))
            history.push('/main/user')
          }
        })
      }
    })
  }
  render () {
    const { getFieldDecorator } = this.props.form
    return (
      <div className="login-container">
        <div className="head"></div>
        <div className="login-box">
          <h1>期权管理后台</h1>
          <Form className="login-form" onSubmit={this.handleSubmit}>
            <FormItem>
              {getFieldDecorator('userName', {
                rules: [
                  {
                    required: true,
                    message: '用户名是必需的'
                  }
                ]
              })(
                <Input size="large" prefix={<Icon type="user" style={{ fontSize: 13 }} size="large"/>} placeholder="用户名" />
              )}
            </FormItem>
            <FormItem>
              {getFieldDecorator('password', {
                rules: [
                  {
                    required: true,
                    message: '密码是必需的'
                  }
                ]
              })(
                <Input size="large" prefix={<Icon type="lock" style={{ fontSize: 13 }} />} type="password" placeholder="密码" />
              )}
            </FormItem>
            <FormItem>
              <Button className="login-btn" size="large" type="primary" htmlType="submit">登录</Button>
            </FormItem>
          </Form>
        </div>
      </div>
    )
  }
}
const Login = Form.create()(LoginForm)
export default Login
