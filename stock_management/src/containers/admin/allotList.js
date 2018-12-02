import React, { Component } from 'react'
import { Table, InputNumber, Modal, Form, Button, Radio,message,Breadcrumb } from 'antd'
import { getUserList, getExpectedList,addExpectedList,modifyExpectedList,delExpectedList,getExpectedBatchById,modifyExpectedBatchStatus} from '../../utils/request'
import {formatTime,transArr} from '../../utils/common'
import moment from 'moment'
import 'moment/locale/zh-cn'
import './form.less'
import './allotList.less'
import 'jquery'
import '../../../controller/config.js'
import '../../../controller/metaMaskShare.js'
import '../../../controller/metaMaskOption.js'

let ref = null,table=null
const FormItem = Form.Item;
class StockForm extends Component {
  constructor(props) {
    super(props)
    this.state = {
      detail: {},
    }
  }
  handleSubmit = (e) => {
    const { submit,data } = this.props
    e.preventDefault()
    this.props.form.validateFields((err, values) => {
      if (!err) {
        ref.destroy()
        values['id']=data.id
        submit(values)
      }
    })
  }
  componentWillMount () {
    this.props.form.setFieldsValue({
      value: this.props.data.value,
    });
  }
  render () {
    const { getFieldDecorator } = this.props.form
    return (
      <Form onSubmit={this.handleSubmit}>
        <FormItem label={this.props.data.name+" 预发数量"}>
          {getFieldDecorator('value', {
            rules: [{ required: true, message: '请输入预发数量!' }]
          })(
            <InputNumber min={1} style={{width: '100%'}} placeholder="预发数量（最小值为1）" autoFocus/>
          )}
        </FormItem>
        <FormItem>
          <Button type="primary" htmlType="submit" style={{width: '48%'}}>
            保存
          </Button><Button type="primary" onClick={()=>ref.destroy()} style={{width: '48%',marginLeft: '4%'}}>
            取消
          </Button>
        </FormItem>
      </Form>
    )
  }
}
const MyStock = Form.create()(StockForm)

class MyList extends Component {
  constructor(props) {
    super(props);
    this.state = {
      userList: [],
      current: 1,
      total: 0,
      selectedRowKeys:[],
      userIds:[]
      };
    this.userCol= [
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
    ]
  }


  putIn =(e) =>{
    this.setState({param: e.target.value})
  }
  showUsers = (page, pageSize,param = null) => {
    this.updateKey()
    getUserList ({
      offset: (page-1)*pageSize,
      limit: pageSize,
      parameter: param
    }).then(data => {
      data.records.map(item => {
        return item['key'] = item.id
      })
      this.setState({
        current: data.page,
        total: data.totalCount,
        userList: data.records,
        selectedRowKeys:[]
      })
    })
  }
  search =()=>{
    this.showUsers(this.state.current,5,this.state.param)
  }
  componentWillMount () {
    this.showUsers(1,5)
  }
  do = (e) => {
    this.updateKey()
    addExpectedList({batch_id:this.props.id,user_id:this.state.userIds}).then(res => {
      res && message.success('添加成功！')
      this.setState({showUserList: false})
      this.props.update()
    })
  }

  updateKey = () => {
    let userIds = this.state.userIds
    // 根据索引值转换成id, 添加到userIds里
    if (this.state.selectedRowKeys) {
      this.state.selectedRowKeys.map(item => {
        userIds.push(this.state.userList[item].id)
      })
      this.setState({userIds})
    }
  }

  onSelectChange = (selectedRowKeys, selectedRows) => {
    selectedRows.map(i=>{
      Object.assign(i,{value: 0})
    })
    this.setState({selectedRowKeys})
  }

  render (){
    const pagination = {
      current: this.state.current,
      total: this.state.total,
      defaultPageSize: 5,
      onChange: (page) => this.showUsers(page,5,this.state.param),
      showTotal: (total) => '共 ' + total + ' 条数据'
    }

    const rowSelection = {
      selectedRowKeys: this.state.selectedRowKeys,
      onChange: this.onSelectChange,
      onSelectAll: (selected) => {
        if (selected) {
          message.warning('全选仅选择当前页全部数据！')
        }
      }
    }

    return (
      <div>
        <p className='searchBar'>
          <input placeholder='请输入姓名/手机号/身份证号/钱包地址/部门' style={{width:300}} onChange={(e)=>this.putIn(e)}/>
          <Button type="primary" onClick={this.search}>搜索</Button>
        </p>
        <Table
          {...this.state}
          columns={this.userCol}
          dataSource={this.state.userList}
          rowKey='userK'
          pagination={pagination}
          rowSelection={rowSelection}
        />
        <p className='btns'>
          <Button type="primary" onClick={this.do}>添加</Button>&nbsp;
          <Button type="primary" onClick={()=>{table.destroy()}}>关闭</Button>
        </p>
      </div>
    )
  }
}
export default class allotList extends Component {
  constructor(props) {
    super(props);
    this.state = {
      data: [],
      current: 1,
      total: 0,
      showUserList: false,
      };
    this.columns = [
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
        title: '钱包地址',
        dataIndex: 'address',
        // width: '40%',
        key: 'address',
      },
      {
        title: '预发数量',
        dataIndex: 'value',
        key: 'value',
        editable: true,
        render:(value)=>{
          return value||0
        }
      },
      {
        title: '操作',
        key: 'action',
        render: (row) => {
          const type = this.props.history.location.search.split('&').length > 1
          return (
            <span>
              {type?
                (<span>已发放</span>):
                (<span>
                  <a onClick={() => this.showModal(row)}>修改 </a>
                  <a onClick={() => this.del(row.id)}>删除</a>
                </span>)
              }
            </span>
          );
        },
      },
    ]
  }
  del = (id)=>{
    delExpectedList({id:id}).then(res=>{
      res&&(message.success('删除成功')) && this.showList()
    })
  }
  showList = () => {
    getExpectedList({id:this.props.history.location.search.split('=').pop()}).then(res=>{
      console.log(res)
      this.setState({data:res})
    })
  }

  save=(values)=> {
    modifyExpectedList(values).then(res=>{
      res&&(message.success('修改成功')) && this.showList()
    })
  }

  showModal = (row) => {
    ref = Modal.info({
      title: '',
      maskClosable: true,
      content: <MyStock submit={this.save} data={row}></MyStock>,
      okText: ' ',
      okType: 'none'
    })
  }
  showUsers = (page, pageSize,param = null) => {
    getUserList ({
      offset: (page-1)*pageSize,
      limit: pageSize,
      parameter: param
    }).then(data => {
      this.setState({
        pagination: {
          current: data.pageNum,
          total: data.totalCount,
          showTotal: (total) => '共 ' + totalCount + ' 条数据'
        },
        userList: data.records
      })
    })
  }
  componentWillMount () {
    this.showList()
  }
  show = ()=>{
    table= Modal.info({
      title: '选择用户',
      maskClosable: true,
      content: <MyList id={this.props.history.location.search.split('=').pop()} update={this.showList} />,
      okText: ' ',
      okType: 'none'
    })
  }
  submitUser = ()=>{
    let contractAddress,expectedList
    getExpectedBatchById({id:this.props.history.location.search.split('=').pop()}).then(res=>{
      res&&getExpectedList({id:this.props.history.location.search.split('=').pop()}).then(resp=>{
        resp&&(window.distributeOption(res[0].contract_address,resp).then(
          ds=>{
            (ds.status===0)&&modifyExpectedBatchStatus({id:this.props.history.location.search.split('=').pop(),txHash:ds.data}).then(dt=>{
              dt&&(this.props.history.goBack())
            })
          }
        ))
      })
    })
  }
  render() {
    const type = this.props.history.location.search.split('&').length < 2
    return (
      <div>
        <Breadcrumb>
          <Breadcrumb.Item onClick={()=> this.props.history.goBack()}>返回</Breadcrumb.Item>
          <Breadcrumb.Item>预发放名单</Breadcrumb.Item>
        </Breadcrumb>
        {type&&
          (<Radio.Group>
            <Radio.Button value="top" onClick={this.show}>选择预发名单</Radio.Button>
            <Radio.Button value="left" onClick={this.submitUser}>确认预发名单</Radio.Button>
          </Radio.Group>)
        }
        <Form layout='inline'>
          <FormItem label="总量">
            <span>{this.state.data.value}</span>
          </FormItem>
        </Form>
        <Table
          bordered
          dataSource={this.state.data}
          columns={this.columns}
        />

      </div>
    );
  }
}