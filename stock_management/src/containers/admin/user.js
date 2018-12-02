import React, { Component } from 'react'
import SearchTable from '../../components/SearchTable'
import { message, Table,Form,Modal,Input,Button,Upload,Row,Col } from 'antd'
import {getUserList, delUser, addUser,editUser,userDetail,getStock} from '../../utils/request'
import {formatTime, transArr} from '../../utils/common'
import '../../../controller/config'
import './main.less'
import './form.less'
import 'jquery'
import '../../../controller/config.js'
import '../../../controller/metaMaskOption.js'

let ref = null
const FormItem = Form.Item
class AddForm extends Component {
  constructor(props) {
    super(props)
    this.state = {
      detail: {},
      previewVisible: false,
      show:'',
      previewImage: '',
      data:[],
      fileList: [{
        uid: -1,
        url: '',
      }],
    }
    this.data = []
  }
  columns= [
    {
      title: '期号',
      dataIndex: 'period',
      key: 'period',
      render:(value,row)=>{
        return (<span>{row.symbol} 第{value}期</span>)
      }
    },
    {
      title: '自由期权',
      dataIndex: 'freeBalance',
      key: 'freeBalance',
    },
    {
      title: '冻结期权',
      dataIndex: 'lockBalance',
      key: 'lockBalance',
    },
    {
      title: '操作',
      key: 'action',
      render: (row, index) => (
        <a className="mt-10" onClick={() => this.remove(row)} target="_blank">回收 &nbsp;</a>
      )
    }
  ]
  remove = (row) =>{
    window.reclaimOption(row.contract_address,this.state.detail.address).then(res=>{
      if(res.status===0){
        this.state.data.splice(this.state.data.indexOf(row),1)
        this.setState({show:row.contract_address})
      }
    })
  }
  handleSubmit = (e) => {
    const { submit } = this.props
    e.preventDefault()
    this.props.form.validateFields((err, values) => {
      if (!err) {
        ref.destroy()
        submit(values, this.props.id,this.props.type)
      }
    })
  }
  componentWillMount () {
    (this.props.type !== 'add'&&this.props.type !== 'upload')? this.getData(this.props.id):''
  }
  getData = (id) => {
    userDetail({id:id}).then(data => {
      getStock(0,10).then(res=>{
        res.records.map(i=>{
          i.period >0 ? window.getBalance(i.contract_address,data.address).then(res=>{
            // console.log(res)
            if (res.data.freeBalance+res.data.lockBalance === 0) {
              return
            } else {
              let list=Object.assign(res.data,{'period':i.period,'contract_address':i.contract_address,'symbol':i.symbol})
              this.data.concat(list)
              this.setState(prevState => ({
                data: prevState.data.concat(list)
              }));
            }
          })
          : window.balanceOfShare(data.address).then(res=>{
            this.props.form.setFieldsValue({
              stock: res.data,
            });
          })
        })
      })
      // window.getBalance()
      if (data) {
        this.setState({detail:data})
        this.props.form.setFieldsValue({
          name: data['name'],
          department: data['department'],
          phone_number: data['phone_number'],
          id_card: data['id_card'],
          address: data['address'],
        });
      }
    })
  }
  getAssets = ()=>{
    getStock(0,10).then(res=>{
      return res.records
    })
  }
  handlePreview = (file) => {
    this.setState({
      previewImage: file.url || file.thumbUrl,
      previewVisible: true,
    });
  }
  update = (file) => {
    if (file.file.size > 1024*1024*5) {
      message.error('上传文件过大,请重新选择！')
      return false
    }
  }
  changeIcon = (file) => {
    let fileList = [{
      uid: -1,
      url: file.data,
    }]
    this.setState({fileList: fileList})
  }
  operate = () => {
    const { detail,del } = this.props
    // (index, row.status, row.id, row.address)
    let accounts =[];
    window.removeOptionRights(detail.address).then(
        res => {
          if(res.status === 0){
            ref.destroy()
            del(detail.id)
          } else {
            message.error(res.error)
          }
        }
      )
  }
  render () {
    const { getFieldDecorator } = this.props.form
    const options = {
      action: window.CONFIG.backend_sever+'/user/importUsers',
      headers: {
        Authorization: JSON.parse(localStorage.getItem('admin') || '{}').token
      },
      fileList:this.state.fileList[0].url ? this.state.fileList : null
    }
    return (
      <div className={this.state.show}>
        {this.props.type !== 'upload'?
        (
          <Form onSubmit={this.handleSubmit} className='userModal'>
          <Row gutter={12}>
            <Col span={10}>
              <FormItem label="姓名" className={(this.props.type === 'edit'||this.props.type === 'add')?'':'onlyShow'} style={{display:(this.props.type === 'edit'||this.props.type === 'add')?'block':'flex'}}>
                {getFieldDecorator('name', {
                  rules: [{ required: (this.props.type === 'edit'||this.props.type === 'add')?true:false, message: '请输入姓名!' }]
                })(
                  <Input type="text" placeholder="姓名" disabled={this.props.type === 'show'||this.props.type === 'del'} />
                )}
              </FormItem>
            </Col>
            <Col span={14}>
              <FormItem label="手机号码" className={(this.props.type === 'edit'||this.props.type === 'add')?'':'onlyShow'} style={{display:(this.props.type === 'edit'||this.props.type === 'add')?'block':'flex'}}>
                {getFieldDecorator('phone_number', {
                  rules: [{ required: (this.props.type === 'edit'||this.props.type === 'add')?true:false, message: '请输入手机号!' }]
                })(
                  <Input type="text" placeholder="手机号" disabled={this.props.type === 'show'||this.props.type === 'del'} />
                )}
              </FormItem>
            </Col>
          </Row>
          <Row gutter={12}>
            <Col span={(this.props.type === 'edit'||this.props.type === 'add')?24:10}>
              <FormItem label="身份证号" className={(this.props.type === 'edit'||this.props.type === 'add')?'':'onlyShow'} style={{display:(this.props.type === 'edit'||this.props.type === 'add')?'block':'flex'}}>
                {getFieldDecorator('id_card', {
                  rules: [{ required: (this.props.type === 'edit'||this.props.type === 'add')?true:false, message: '请输入身份证号!' }]
                })(
                  <Input type="text" placeholder="身份证号" disabled={this.props.type === 'show'||this.props.type === 'del'} />
                )}
              </FormItem>
            </Col>
            <Col span={(this.props.type === 'edit'||this.props.type === 'add')?24:14}>
              <FormItem label="钱包地址" className={(this.props.type === 'edit'||this.props.type === 'add')?'':'onlyShow'} style={{display:(this.props.type === 'edit'||this.props.type === 'add')?'block':'flex'}}>
                {getFieldDecorator('address', {
                  rules: [{ required: (this.props.type === 'edit'||this.props.type === 'add')?true:false, message: '请输入钱包地址!' }]
                })(
                  <Input type="text" placeholder="钱包地址" disabled={this.props.type === 'show'||this.props.type === 'del'}/>
                )}
              </FormItem>
            </Col>
          </Row>
          <FormItem label="部门" className={(this.props.type === 'edit'||this.props.type === 'add')?'':'onlyShow'} style={{display:(this.props.type === 'edit'||this.props.type === 'add')?'block':'flex'}}>
            {getFieldDecorator('department', {
              rules: [{ required: (this.props.type === 'edit'||this.props.type === 'add')?true:false, message: '请输入部门!' }]
            })(
              <Input type="text" placeholder="部门" disabled={this.props.type === 'show'||this.props.type === 'del'}/>
            )}
          </FormItem>
          {
            this.props.type === 'show'||this.props.type === 'del'?
            (<FormItem label="股权" className={(this.props.type === 'edit'||this.props.type === 'add')?'':'onlyShow'} style={{display:(this.props.type === 'edit'||this.props.type === 'add')?'block':'flex'}}>
              {getFieldDecorator('stock')(
                <Input type="text" disabled/>
              )}
            </FormItem>)
            :null
          }
          {
            this.props.type === 'show'||this.props.type === 'del'?
            (<Table {...this.state} columns={this.columns} dataSource={this.state.data} rowKey='key' pagination={false}/>)
            :null
          }
          <FormItem>
            {
              this.props.type === 'show'?
              (<Button type="primary" onClick={()=>ref.destroy()} style={{width: '100%'}}>
                关闭
              </Button>)
              :
              this.props.type === 'del'?
              (<div><Button type="primary" onClick={this.operate} style={{width: '48%'}}>
                删除
              </Button><Button type="primary" onClick={()=>ref.destroy()} style={{width: '48%',marginLeft: '4%'}}>
              取消
            </Button></div>)
              :
              (<Button type="primary" htmlType="submit" style={{width: '100%'}}>
                提交
              </Button>)
            }
          </FormItem>
        </Form>
      )
      :(
        <div className="clearfix">
          <Upload {...options}
            accept = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel'
            onPreview={this.handlePreview}
            onSuccess = {this.changeIcon}
            onChange = {this.update}
            name="file"
          >
            {this.state.fileList.length > 1 ? null : (<Button icon="upload">上传Excel文件</Button>)}
          </Upload>
        </div>
      )}
      </div>
    )
  }
}
const MyForm = Form.create()(AddForm)

class UserList extends Component {
  detailButtons = []
  constructor(props) {
    super(props)
    this.state = {
      pagination: {
        current: 1,
        total: 0,
        showTotal: (total) => '共 ' + total + ' 条数据'
      },
      data: [],
      detail: {},
      userMsg: [],
      visible: '',
    }
  }
  addUser = (values, id, type) => {
    type !== 'add' ? Object.assign(values,{'id': id}) : values
    type === 'add' ?
    (addUser(values).then(
      (data) => {
        if (data) {
          message.success('添加成功')
          this.search(this.state.pagination.current,10)
        }
      }
    ))
    :(editUser(values).then(
      (data) => {
        if (data) {
          message.success('编辑成功')
          this.search(this.state.pagination.current,10)
        }
      }
    ))
  }
  showModal = (row, type) => {
    ref = Modal.info({
      title: type==='add'?'新增用户':(type==='edit'?'修改用户信息':(type==='show'?'查看用户信息':(type==='del'?'删除用户':'上传Excel文件'))),
      maskClosable: true,
      content: <MyForm submit={this.addUser} id={!!row&&row.id||null} type={type} detail={row} del ={this.delUser}></MyForm>,
      okText: ' ',
      okType: 'none'
    })
  }
  options = {
    form: [
      {
        element: 'input',
        name: 'parameter',
        width: '300px',
        placeholder: '请输入姓名/手机号/身份证号/钱包地址/部门'
      }
    ],
    buttons: [
      {
        text: '新增',
        onClick: () => this.showModal(null, 'add'),
      },
      {
        text: '导入',
        icon: 'upload',
        onClick: () => this.showModal(null, 'upload'),
      },
      {
        text: '下载模版',
        icon: 'download',
        href: '../../../templates/users.xlsx'
      }
    ],
    table: {
      columns: [
        {
          title: '姓名',
          dataIndex: 'name',
          key: 'name',
        },
        {
          title: '手机号',
          dataIndex: 'phone_number',
          key: 'phone_number',
        },
        {
          title: '身份证号',
          dataIndex: 'id_card',
          key: 'id_card',
        },
        {
          title: '部门',
          dataIndex: 'department',
          key: 'department',
        },
        {
          title: '钱包地址',
          dataIndex: 'address',
          key: 'address',
          render: (value) => {
            return (<span title={value}>{value.length>8? value.substr(0, 4) + '...' + value.substr(value.length - 4, 4) : value}</span>)
          }
        },
        {
          title: '创建时间',
          dataIndex: 'createdAt',
          key: 'createdAt',
          render: (value) => {
            return (<span>{value? formatTime(value, true) : ''}</span>)
          }
        },
        {
          title: '操作',
          key: 'action',
          render: (row) => {
            return(
              <span>
                <a className="mt-10" onClick={() => this.showModal(row, 'edit')} target="_blank">编辑 </a>&nbsp;
                <a className="mt-10" onClick={() => this.showModal(row, 'del')} target="_blank">删除 </a>&nbsp;
                <a className="mt-10" onClick={() => this.showModal(row, 'show')} target="_blank">查看 </a>
              </span>
            )
          }
        }
      ]
    }
  }
  delUser = (id) => {
    delUser({
      id: id
    }).then((data) => {
      if (data) {
        this.state.data = this.state.data.filter((item) => {
          return item.id !== id
        })
        this.setState({visible: 'show'+id,total:this.state.total-1})
        message.success('操作成功')
        this.search(1,10)
      } else {
        message.error('操作不成功，请联系管理员')
        return true
      }
    })
  }
  search = (page, pageSize,values) => {
    getUserList ({
      offset: (page-1)*pageSize,
      limit: pageSize,
      ...values
    }).then(data => {
      this.setState({
        pagination: {
          current: data.pageNum,
          total: data.totalCount,
          showTotal: (total) => '共 ' + totalCount + ' 条数据'
        },
        data: data.records
      })
    })
  }
  componentWillMount () {
    let type = this.props.history.location.pathname.split('/').pop()
    let id = this.props.history.location.search.split('=').pop()
    type === 'del'?this.getData(parseInt(id)):''
  }
  getData = (id) => {
    userDetail({id:id}).then(data => {
      if (data) {
        this.setState({detail:data})
        // this.props.form.setFieldsValue({
        //   account: detail['account'],
        //   nickname: detail['nickname'],
        //   password: detail['password'],
        // });
      }
    })
  }
  render() {
    return (
      <div className={this.state.visible}>
        <SearchTable options={this.options} data={this.state.data} pagination={this.state.pagination} search={this.search} {...this.props}/>
      </div>
    );
  }
}
export default UserList
