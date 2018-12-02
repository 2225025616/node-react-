import React, { Component } from 'react'
import { message, DatePicker,InputNumber,Form,Modal,Input,Button,Spin } from 'antd'
import SearchTable from '../../components/SearchTable';
import { getStock, addOption,editOption} from '../../utils/request'
import {formatTime} from '../../utils/common';
import moment from 'moment';
import 'moment/locale/zh-cn'
import './main.less'
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
      open_time: '',
      expire_time: '',
      freeze_time: ''
    },
    this.open_time = '',
    this.expire_time = '',
    this.loading=false,
    this.freeze_time = ''
  }
  handleSubmit = (e) => {
    const { submit,type,change,detail } = this.props
    e.preventDefault()
    this.props.form.validateFields((err, values) => {
      if (!err) {
        ref.destroy()
        if (type === 'add') {
          values.expire_time = this.state.expire_time || values.expire_time.toDate().toISOString().slice(0, -5).replace('T',' ')
          values.open_time = this.state.open_time || values.open_time.toDate().toISOString().slice(0, -5).replace('T',' ')
        } else if (type==='freeze'){
          values.freeze_time = this.state.freeze_time || values.freeze_time.toDate().toISOString().slice(0, -5).replace('T',' ')
        } else if(type==='unlock') {
          console.log(detail)
          window.unlockOption(detail.contract_address,values.ratio,detail.symbol,detail.period).then(data=>{
            console.log(data)
            if (data.status === 0) {
              this.updateData(Number(values.ratio))
            }
          })
          return 
        } else {
          values = null
        }
        // window.addOptionAddress(address).then(res=>{
        //   res.status === 0 && submit(values,type)
        // })
        this.loading = true
        change(this.loading)
        window.deploy(values.symbol,values.supply,new Date(values.open_time).getTime(),new Date(values.expire_time).getTime(),values.price).then(
          res=>{
            if(res) {
              this.loading = false
              values['contract_address']=res
              change(this.loading)
              submit(values,type)
            }
          }
        )
      }
    })
  }
  updateData = (value) => {
    editOption({
      symbol:this.props.detail.symbol,
      ratio: value
    }).then(res=>{
      if(res) {
        message.success('解锁成功！',4)
      }
    })
  }
  onChange = (type,e,value) => {
    if (type === 'expire') {
      this.expire_time=value
      this.setState({expire_time: value})
    } else if(type === 'open'){
      this.open_time=value
      this.setState({open_time: value})
    } else {
      this.freeze_time=value
      this.setState({freeze_time: value})
    }
  }
  onOk = (type,e) => {
    if (type === 'expire') {
      this.setState({expire_time: moment(e).format('YYYY-MM-DD H:mm:ss')})
    } else  if(type === 'open'){
      this.setState({open_time: moment(e).format('YYYY-MM-DD H:mm:ss')})
    } else{
      this.setState({freeze_time: moment(e).format('YYYY-MM-DD H:mm:ss')})
    }
  }
  componentWillMount () {
    this.props.type === 'defrost' ? this.showData(): null
  }
  showData = () => {
    window.freezeEndTime().then(
      res=>{
        this.props.form.setFieldsValue({
          defrost_time: formatTime(res.data, true)
        });
      }
    )
  }
  render () {
    const { getFieldDecorator } = this.props.form
    return (
      <div className={this.state.show}>
        <Form onSubmit={this.handleSubmit}  className='optionModal' layout='inline'>
        {this.props.type === 'add'?(
          <div>
            <FormItem label="期权代号">
              {getFieldDecorator('symbol', {
                rules: [{ required: true, message: '请输入期权代号!' }]
              })(
                <Input type="text" placeholder="期权代号" disabled={this.props.type === 'unlock'} />
              )}
            </FormItem>
            <FormItem label="期号">
              {getFieldDecorator('period', {
                rules: [{ required: true, message: '请输入期号!' }]
              })(
                <Input type="text" placeholder="期号" disabled={this.props.type === 'unlock'} />
              )}
            </FormItem>
            <FormItem label="发行总量">
              {getFieldDecorator('supply', {
                rules: [{ required: true, message: '请输入发行总量!' }]
              })(
                <Input type="text" placeholder="发行总量" disabled={this.props.type === 'unlock'} />
              )}
            </FormItem>
            <FormItem label="行权价格">
              {getFieldDecorator('price', {
                rules: [{ required: true, message: '请输入行权价格!' }]
              })(
                <Input type="text" placeholder="行权价格" disabled={this.props.type === 'unlock'}/>
              )}
            </FormItem>
            <FormItem label="开始时间">
              {getFieldDecorator('open_time', {
                rules: [{ required: true, message: '请输入开始时间!' }]
              })(
                <DatePicker
                  showTime
                  format={dateFormat}
                  placeholder="请选择开始时间"
                  onChange={(e,value) => this.onChange('open',e,value)}
                  onOk={(e,value)=>this.onOk('open',e,value)}
                />
              )}
            </FormItem>
            <FormItem label="行权截止日期">
              {getFieldDecorator('expire_time', {
                rules: [{ required: true, message: '请输入行权截止日期!' }]
              })(
                <DatePicker
                  showTime
                  format={dateFormat}
                  placeholder="请选择行权截止日期"
                  onChange={(e,value) => this.onChange('expire',e,value)}
                  onOk={(e,value)=>this.onOk('expire',e,value)}
                />
              )}
            </FormItem>
            {/* <FormItem label="合约地址">
              {getFieldDecorator('contract_address', {
                rules: [{ required: true, message: '请输入合约地址!' }]
              })(
                <Input type="text" placeholder="合约地址" disabled={this.props.type === 'unlock'}/>
              )}
            </FormItem> */}
          </div>
        ):(this.props.type === 'freeze'?(
          <FormItem label="冻结时间">
            {getFieldDecorator('freeze_time', {
              rules: [{ required: true, message: '请选择冻结时间!' }]
            })(
              <DatePicker
                showTime
                format={dateFormat}
                placeholder="请选择冻结时间"
                onChange={(e,value) => this.onChange('freeze',e,value)}
                onOk={(e,value)=>this.onOk('freeze',e,value)}
                disabled={this.props.type !== 'freeze'}
              />
            )}
          </FormItem>
        ):(this.props.type === 'defrost'?
            (<FormItem label="冻结时间">
              {getFieldDecorator('defrost_time')(
                <Input type="text" disabled/>
              )}
              </FormItem>
            )
            :(
              <FormItem label="解锁比例 (%)">
                {getFieldDecorator('ratio', {
                  rules: [{ required: true, message: '请输入解锁比例!' }]
                })(
                  <InputNumber min={0} max={100} placeholder="解锁比例 (%)" style={{width: 200}}/>
                )}
              </FormItem>
            )
        )
        )}
          
          <FormItem>
            <Button type="primary" htmlType="submit" style={{width: '100%'}}>
            {this.props.type === 'add'?'提交':'确定'}
            </Button>
          </FormItem>
        </Form>
      </div>
    )
  }
}
const MyStock = Form.create()(StockForm)

class StockList extends Component {
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
      loading: false,
    }
  }
  change = (v) => {
    this.setState({loading: v})
  }
  addOption = (values,type) => {
    if(type === 'add') {
      addOption(values).then(
        (data) => {
          if (data) {
            message.success('添加成功')
            this.search(0,10)
          }
        }
      )
    } else if(type === 'freeze') {
      window.freezeShare(new Date(values.freeze_time).getTime()).then(
        res=>{
          if(res.status === 0) {
            message.success('冻结成功！')
          }
        }
      )
    } else {
      window.defrostShare().then(
        res=>{
          if(res) {
            message.success('解冻成功！')
          }
        }
      )
    }
    
  }
  showModal = (detail = null, type) => {
    console.log(type)
    ref = Modal.info({
      title: '发行期权',
      maskClosable: true,
      content: <MyStock submit={this.addOption} type={type} detail={detail} change={this.change}></MyStock>,
      okText: ' ',
      okType: 'none'
    })
  }
  options = {
    buttons: [
      {
        text: '发行期权',
        onClick: () => this.showModal(null, 'add'),
      }
    ],
    table: {
      columns: [
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
          title: '已发放总量',
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
          title: '行权截止日期',
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
            return (<span>{value? value+'%' : 0}</span>)
          }
        },
        {
          title: '行权价格',
          dataIndex: 'price',
          key: 'price',
        },
        {
          title: '操作',
          key: 'action',
          render: (row, index) => (
            <span>
              {row.period !== 0?
                (<span>
                {
                  row.freeze?
                  <a className="mt-10" onClick={() => this.props.history.push('/main/stock/unlock?contract_address='+row.contract_address+'&period='+row.period+'&symbol='+row.symbol+'&id='+row.id)} target="_blank">解锁 &nbsp;</a>:null
                }
                <a className="mt-10" onClick={() => this.props.history.push('/main/grant?id='+row.id)} target="_blank">预发放 </a></span>):
                (<span>
                  <a className="mt-10" onClick={() => this.showModal(null, 'freeze')} target="_blank">冻结 &nbsp;</a>
                  <a className="mt-10" onClick={() => this.showModal(null, 'defrost')} target="_blank">解冻 &nbsp;</a>
                </span>)
              }
            </span>
          )
        }
      ]
    }
  }
  search = (page, pageSize) => {
    // // console.log(values)
    getStock ({
      offset: (page-1)*pageSize,
      limit: pageSize,
      // ...values
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
  render() {
    return (
      <Spin spinning={this.state.loading}  className="">
        <SearchTable options={this.options} data={this.state.data} pagination={this.state.pagination} search={this.search} {...this.props}/>
      </Spin>
    );
  }
}
export default StockList
