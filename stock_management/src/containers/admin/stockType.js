import React, { Component } from 'react'
import { Table, Input, Modal, Form, Button, message, DatePicker,Breadcrumb, Icon } from 'antd'
import { getUserList, getUnlockRemind,editOption,updateUnlockRemind,delExpectedList,unlockRemind,modifyExpectedBatchStatus} from '../../utils/request'
import {formatTime,transArr} from '../../utils/common'
import moment from 'moment'
import 'moment/locale/zh-cn'
import './form.less'
import './allotList.less'
import 'jquery'
import '../../../controller/config.js'
import '../../../controller/metaMaskShare.js'
import '../../../controller/metaMaskOption.js'

let ref = null
const FormItem = Form.Item
const dateFormat = 'YYYY-MM-DD HH:mm:ss'
class StockForm extends Component {
  constructor(props) {
    super(props)
    this.state = {
      detail: {},
      date: ''
    }
    this.uuid=0
  }
  handleSubmit = (e) => {
    const { submit,data,id } = this.props
    e.preventDefault()
    this.props.form.validateFields((err, values) => {
      if (!err||(Object.keys(err).length===1&&Object.keys(err)[0]==='date')) {
        ref.destroy()
        let dates = []
        values.date.map(i=>{
          console.log(i._d)
          console.log(i.toDate().toISOString().slice(0, -5).replace('T',' '))
          dates.push(i.toDate().toISOString().slice(0, -5).replace('T',' '))
        })
        let inputs = []
        values.value.map(i=>{
          inputs.push(parseInt(i))
        })
        values['option_id']=parseInt(id)
        values['date']=dates
        values['value']=inputs
        delete values.keys
        let sum = 0
        function getSum (item, index, array){
          sum += item
        }
        values['value'].some(getSum);
        console.log(values)
        sum===100?submit(values):(message.error('解锁数据的解锁比例之和必须等于100'))
      }
    })
  }
  componentDidMount () {
    console.log(this.props.data)
    this.props.type==='edit' && this.showData()
  }
  showData = ()=>{
    this.props.form.setFieldsValue({
      value: this.props.data.value,
      date: moment(this.props.data.datetime.slice(0, -6).replace('T',' '), dateFormat),
    });
  }
  onChange = (e,value) => {
    this.date=value
    console.log(typeof value)
    this.setState({date: value.toString()})
  }
  onOk = (e,value) => {
    console.log(typeof moment(e).format('YYYY-MM-DD H:mm:ss'))
    console.log(typeof moment(e).format('YYYY-MM-DD H:mm:ss').toString())
    // this.setState({date: moment(e).format('YYYY-MM-DD H:mm:ss')})
  }
  add = () => {
    const { form } = this.props;
    // can use data-binding to get
    const keys = form.getFieldValue('keys');
    const nextKeys = keys.concat(this.uuid);
    this.uuid++;
    // can use data-binding to set
    // important! notify form to detect changes
    form.setFieldsValue({
      keys: nextKeys,
    });
  }
  remove = (k) => {
    const { form } = this.props;
    // can use data-binding to get
    const keys = form.getFieldValue('keys');
    // We need at least one passenger
    if (keys.length === 1) {
      return;
    }

    // can use data-binding to set
    form.setFieldsValue({
      keys: keys.filter(key => key !== k),
    });
  }
  render () {
    const { getFieldDecorator, getFieldValue } = this.props.form;
    getFieldDecorator('keys', { initialValue: [] });
    const keys = getFieldValue('keys');

    const formItems = keys.map((k, index) => {
      return (
        <FormItem
          label={index === 0 ? '期权解锁' : ''}
          required={false}
          key={k}
        >
          {getFieldDecorator(`value[${k}]`, {
            validateTrigger: ['onChange', 'onBlur'],
            rules: [{
              required: true,
              whitespace: true,
              message: "请输入解锁比例",
            }],
          })(
            <Input type='number' min={0} max={100} placeholder="解锁比例 (%)" style={{width: 200}}/>
          )}&nbsp;&nbsp;
          {getFieldDecorator(`date[${k}]`, {
            rules: [{
              required: true,
              whitespace: true,
              message: "请输入解锁日期",
            }],
          })(
            <DatePicker
              showTime
              format={dateFormat}
              placeholder="请选择解锁日期"
              onChange={(e,value) => this.onChange(e,value)}
              onOk={(e,value)=>this.onOk(e,value)}
            />
          )}
          {keys.length > 1 ? (
            <Icon
              className="dynamic-delete-button"
              type="minus-circle-o"
              disabled={keys.length === 1}
              onClick={() => this.remove(k)}
            />
          ) : null}
        </FormItem>
      );
    });

    return (
      <Form onSubmit={this.handleSubmit}>
        {formItems}
        <FormItem>
          <Button type="dashed" onClick={this.add} style={{ width: '60%' }}>
            <Icon type="plus" /> 新增数据
          </Button>
        </FormItem>
        <FormItem>
          <Button type="primary" htmlType="submit">保存</Button>&nbsp;
          <Button type="primary" onClick={()=>ref.destroy()}>
            取消
          </Button>
        </FormItem>
      </Form>
    )
  }
}
const MyStock = Form.create()(StockForm)

export default class StockType extends Component {
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
        title: '序号',
        dataIndex: 'id',
        key: 'id',
      },
      {
        title: '日期',
        dataIndex: 'datetime',
        key: 'datetime',
        render: (value) => {
          return (<span>{value? formatTime(value, true) : ''}</span>)
        }
      },
      {
        title: '比例',
        dataIndex: 'value',
        key: 'value',
      },
      {
        title: '交易hash',
        dataIndex: 'txHash',
        key: 'txHash',
      },
      {
        title: '操作',
        key: 'action',
        render: (row) => {
          const type = this.props.history.location.search.split('&').length > 1
          return (
            <span>
              {row.status?
                (<span>已发放</span>):
                  row.status?(<span>已解锁</span>):(<a onClick={() => this.unlock(row)}>解锁</a>)
              }
            </span>
          );
        },
      },
    ]
  }
  unlock=(row)=>{
    const contract_address = this.props.history.location.search.split('&')[0].split('=').pop(),
    symbol=this.props.history.location.search.split('&')[2].split('=').pop(),
    period=this.props.history.location.search.split('&')[1].split('=').pop()
    window.unlockOption(contract_address,row.value,symbol,period).then(data=>{
      console.log(data)
      if (data.status === 0) {
        updateUnlockRemind({id:row.id,txHash:data.data}).then(res=>{
          res&&editOption({
            symbol:symbol,
            ratio: row.value
          }).then(res=>{
            if(res) {
              this.showList()
              message.success('操作成功！',4)
            }
          })
        })
      }
    })
  }
  showList = () => {
    getUnlockRemind({option_id:this.props.history.location.search.split('=').pop()}).then(res=>{
      console.log(res)
      this.setState({data:res})
    })
  }

  save=(values)=> {
    console.log(values)
    unlockRemind(values).then(res=>{
      res&&(message.success('修改成功')) && this.showList()
    })
  }

  showModal = (type,row=null) => {
    ref = Modal.info({
      title: '',
      maskClosable: true,
      content: <MyStock type={type} submit={this.save} data={row} id={this.props.history.location.search.split('=').pop()}></MyStock>,
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
  submitUser = ()=>{
    let contractAddress,expectedList
    unlockRemind({id:this.props.history.location.search.split('=').pop()}).then(res=>{
      res&&getUnlockRemind({option_id:this.props.history.location.search.split('=').pop()}).then(resp=>{
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
    return (
      <div>
        <Breadcrumb>
          <Breadcrumb.Item onClick={()=> this.props.history.goBack()}>返回</Breadcrumb.Item>
          <Breadcrumb.Item>解锁</Breadcrumb.Item>
        </Breadcrumb>
        {this.state.data.length ? null:
          (<Button type='primary' onClick={()=>this.showModal('add')}><Icon type="plus-circle-o" />新增</Button>)
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