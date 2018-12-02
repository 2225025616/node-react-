import React, { Component } from 'react'
import { message, Breadcrumb,Form,Modal,Input,Button,Table,Row,Col } from 'antd'
import { getExpectedBatch, addExpectedBatch,modifyExpectedBatch,getContractById} from '../../utils/request'
import {formatTime} from '../../utils/common';
import moment from 'moment'
import 'moment/locale/zh-cn'

let ref = null
const FormItem = Form.Item
class optionAdd extends Component {
  constructor(props) {
    super(props)
    this.state = {
      detail: {}
    }
  }
  handleSubmit = (e) => {
    const { submit,data } = this.props
    e.preventDefault()
    this.props.form.validateFields((err, values) => {
      if (!err) {
        ref.destroy()
        data&&(values['id']=data.id)
        submit(values)
      }
    })
  }
  componentWillMount () {
    this.props.data && this.props.form.setFieldsValue({
      name: this.props.data.name,
    });
  }
  
  render () {
    const { getFieldDecorator } = this.props.form
    return (
      <Form onSubmit={this.handleSubmit} layout='inline'>
        <FormItem label="批次名称">
          {getFieldDecorator('name', {
            rules: [{ required: true, message: '请输入批次名称!' }]
          })(
            <Input type="text" placeholder="批次名称"/>
          )}
        </FormItem>
        <FormItem>
          <Button type="primary" htmlType="submit" className='addBtn'>保存</Button>
        </FormItem>
      </Form>
    )
  }
}
const AddForm = Form.create()(optionAdd)


export default class grant extends Component {
  constructor(props) {
    super(props)
    this.state = {
      current: 1,
      total: 0,
      data: [],
      show: 'none',
      requestdata: null,
      dataList:{}
    }
  }
  columns= [
    {
      title: '批次ID',
      dataIndex: 'id',
      key: 'id',
    },
    {
      title: '批次名称',
      dataIndex: 'name',
      key: 'name',
    },
    {
      title: '状态',
      dataIndex: 'status',
      key: 'status',
      render: (value) => {
        return (<span>{value? '已发放' : '未发放'}</span>)
      }
    },
    {
      title: '预发总量',
      dataIndex: 'total',
      key: 'total',
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
      title: '交易hash',
      dataIndex: 'txHash',
      key: 'txHash',
      render: (value)=>{
        return (<span title={value}>{value&&value.length>8? value.substr(0, 4) + '...' + value.substr(value.length - 4, 4) : value}</span>)
      }
    },
    {
      title: '操作',
      key: 'action',
      render: (row) => (
        <span>
          {
            row.status?(<a className="mt-10" onClick={() => this.props.history.push('/main/allot?type=0&id='+row.id)} target="_blank">查看名单 &nbsp;</a>):
            (<span><a onClick={() => this.showModal('name',row)}>修改 </a>&nbsp;
            <a className="mt-10" onClick={() => this.props.history.push('/main/allot?id='+row.id)} target="_blank">设置名单 &nbsp;</a>
          </span>)
          }
        </span>
      )
    }
  ]
  search = (id) => {
    getExpectedBatch ({id:id}).then(data => {
      this.setState({
        data: data
      })
    })
  }
  listCol= [
    {
      title: '期权代号',
      dataIndex: 'symbol',
      key: 'symbol',
    },
    {
      title: '发行总量',
      dataIndex: 'supply',
      key: 'supply',
    },
    {
      title: '已发放总金额',
      dataIndex: 'total',
      key: 'total',
    },
    {
      title: '期权开始时间',
      dataIndex: 'open_time',
      key: 'open_time',
      render: (value) => {
        return (<span>{value? formatTime(value, true) : ''}</span>)
      }
    },
    {
      title: '过期时间',
      dataIndex: 'expire_time',
      key: 'expire_time',
      render: (value) => {
        return (<span>{value? formatTime(value, true) : ''}</span>)
      }
    },
    {
      title: '冻结比例',
      dataIndex: 'freeze',
      key: 'freeze',
      render: (value) => {
        return (<span>{value? value+'%' : ''}</span>)
      }
    },
    {
      title: '行权价格',
      dataIndex: 'price',
      key: 'price',
    }
  ]
  componentWillMount () {
    this.search(this.props.history.location.search.split('=').pop())
    this.showContract(this.props.history.location.search.split('=').pop())
  }
  showContract = (id)=>{
    getContractById({id:id}).then(res=>{
      this.setState({dataList:res[0]})
    })
  }
  showModal = (grade,row = null) => {
    ref = Modal.info({
      title: '',
      maskClosable: true,
      content: <AddForm submit={grade==='name'?this.save:this.addList} data={row}/>,
      okText: ' ',
      okType: 'none'
    })
  }
  save=(values)=> {
    modifyExpectedBatch(values).then(res=>{
      res&&(message.success('修改成功')) && this.search(this.props.history.location.search.split('=').pop())
    })
  }
  addList = (value) => {
    value['option_id']=parseInt(this.props.history.location.search.split('=').pop())
    addExpectedBatch(value).then(res=>{
      res&&(message.success('新增成功')) && this.search(this.props.history.location.search.split('=').pop())
    })
  }
  render() {
    return (
      <div>
        <Breadcrumb separator="/">
          <Breadcrumb.Item onClick={()=> this.props.history.goBack()}>返回</Breadcrumb.Item>
          <Breadcrumb.Item>预发放</Breadcrumb.Item>
        </Breadcrumb>
        <Button type="primary" onClick={()=>this.showModal('batch')}>新增批次</Button>
        <Row gutter={16}>
          <Col span={4}>期权代号: {this.state.dataList.symbol} </Col>
          <Col span={4}>发行总量: {this.state.dataList.supply} </Col>
          <Col span={4}>行权价格: {this.state.dataList.price}</Col>
        </Row>
        <Table {...this.state} columns={this.columns} dataSource={this.state.data} rowKey='key' pagination={false} bordered={true}/>
      </div>
    );
  }
}