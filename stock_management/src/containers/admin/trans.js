import React, { Component } from 'react'
import { message, DatePicker,Form,Modal,Input,Button } from 'antd'
import SearchTable from '../../components/SearchTable';
import { tradeList,getTransName} from '../../utils/request'
import {formatTime,showMore} from '../../utils/common';
import './main.less'

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
      visible: [false, false, false],
      show: '',
    }
  }
  show = (value,row,index) => {
    getTransName({address: value}).then(
      res=>{
        row[index+'S'] = res
        this.setState({show:'show'+value.toString(2)})
      }
    )

  }
  options = {
    form: [
      {
        element: 'select',
        name: 'type',
        width: '150px',
        options:[
          { label: '常规交易', value: 1 },
          { label: '回收', value: 2 },
          { label: '发行', value: 3 },
          { label: '解锁', value: 4 },
          { label: '行权', value: 5 },
          { label: 'Approval', value: 6 }
        ]
      },
      {
        element: 'input',
        name: 'period',
        width: '100px',
        placeholder: '请输入期号'
      },
      {
        element: 'input',
        name: 'parameter',
        width: '200px',
        placeholder: '请输入from/to/交易hash'
      }
    ],
    table: {
      columns: [
        {
          title: '类型',
          dataIndex: 'subtype',
          key: 'subtype',
          render: (value) => {
            return (<span>{
              value === 1?
                '常规交易':
                (
                  value === 2?
                    '回收':
                    (
                      value === 3?'发行':(value === 4?'解锁':(value === 5?'行权':'Approval'))
                    )
                )
            }</span>)
          }
        },
        {
          title: 'from',
          dataIndex: 'fromUser',
          key: 'fromUser',
          render: (value,row) => {
            return (<span style={{color:row.from==='0xf4af3f8dc3e742f3c26ed1d9fe1b997b6ed1992a'?'#ff4d4f':''}} title={row.from}>
              {value.name}
            </span>)
          }
        },
        {
          title: 'to',
          dataIndex: 'toUser',
          key: 'toUser',
          render: (value,row) => {
            return (<span style={{color:value==='0xf4af3f8dc3e742f3c26ed1d9fe1b997b6ed1992a'?'#ff4d4f':''}} title={row.to}>
              {value&&value.name||''}
            </span>)
          }
        },
        {
          title: '交易hash',
          dataIndex: 'txHash',
          key: 'txHash',
          render: (value)=>{
            return (<span title={value}>{value.length>8? value.substr(0, 4) + '...' + value.substr(value.length - 4, 4) : value}</span>)
          }
        },
        {
          title: '数量',
          dataIndex: 'value',
          key: 'value',
        },
        {
          title: '期号',
          dataIndex: 'type',
          key: 'type',
        },
        {
          title: '交易时间',
          dataIndex: 'time',
          key: 'time',
          render: (value) => {
            return (<span>{value? formatTime(value, true) : ''}</span>)
          }
        },
        
      ]
    }
  }
  search = (page, pageSize,values) => {
    console.log('11112',values)
    values.period = values.period && parseInt(values.period)
    values.type = values.type && parseInt(values.type)
    console.log('657',values)
    tradeList ({
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
  render() {
    return (
      <div className={this.state.show}>
        <SearchTable options={this.options} data={this.state.data} pagination={this.state.pagination} search={this.search} {...this.props}/>
      </div>
    );
  }
}
export default StockList
