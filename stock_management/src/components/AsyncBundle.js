import React, { PureComponent } from 'react'

class AsyncBundle extends PureComponent { // PureComponent
  state = {
    mod: null
  }
  componentWillMount () {
    this.load(this.props) // return ?
  }
  componentWillReceiveProps (nextProps) {
    if (nextProps.load !== this.props.load) { // this.props.load
      this.load(nextProps)
    }
  }
  load (props) {
    this.setState({
      mod: null
    })
    props.load().then((mod) => {
      // console.log(new Date().getSeconds())
      this.setState({
        mod: mod.default ? mod.default : mod
      })
    })
  }
  render () {
    const Bundle = this.state.mod
    return this.state.mod ? <Bundle {...this.props}></Bundle> : null
  }
}

export default AsyncBundle
