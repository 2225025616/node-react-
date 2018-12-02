 <!-- /**
     * [index description]
     * @Author   张哲
     * @DateTime 2017-09-12
     * @createby SublimeText3
     * @version  1.0
     * @return   [type]                [description]
     */ -->


@extends('admin')
@section('title', '角色管理')

@section('content')

<div id="content">
 <h4>角色管理 - {{ !empty($data) ? '更新' : '新增' }}</h4>

  <hr class="alt short">
  <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
    @if(!empty($data))
        <div class="form-group">
            <label class="col-lg-3 control-label">ID</label>
            <div class="col-lg-8">
                <div class="bs-component">
                    <p class="form-control-static text-muted">{{$data['role_id']}}</p>
                    <input type="hidden" name="role_id" value="{{$data['role_id']}}">
                </div>
            </div>
        </div>
    @endif

    <div class="form-group">
      <label class="col-lg-3 control-label">角色名称</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <input type="text" name="role_name" class="form-control" placeholder="请输入角色名称" value="{{ !empty($data) ? $data['role_name'] : '' }}" required="required">
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">角色描述</label>
      <div class="col-lg-8">
        <div class="bs-component">
          <textarea class="form-control" name="description" placeholder="请输入角色描述">{{ !empty($data) ? $data['description'] : '' }}</textarea>
        </div>
      </div>
    </div>

    <div class="form-group">
        <label class="col-lg-3 control-label">拥有权限</label>
        <input type="hidden" name="privilages" id="privilages" value="{{ !empty($data) ? $data['privilages'] : '' }}">
        <div class="col-lg-8">
            <div class="bs-component">
                <div id="data-select-tree">
                    
                </div>
            </div>
        </div>
    </div>


    <div class="text-right">
      <button type="submit" class="btn btn-default ph25">提交</button>
    </div>
  </form>
</div>


<script type="text/javascript">
jQuery(document).ready(function() {

    // 选择某个分类
    $('#data-select-tree').fancytree({
      source: {url: 'api/get_all_modules'},
      checkbox: true,
      selectMode: 3, // 1单选，2多选，3分类全选
      clickFolderMode: 2,
      postProcess: function(event, data) {
        data.result = formatSelectCategoryDataToFancytree(data.response.msg);
      },
      loadChildren: function(event, data) {
        expandAllParents(data.tree.getSelectedNodes());
      },
      select: function(event, data) {
        var selNodes = data.tree.getSelectedNodes();
        // convert to title/key array
        var selKeys = $.map(selNodes, function(node){
           return node.key;
        });
        $("#privilages").val(selKeys.join("|"));
      }
    });

    var _selectedCategory = $('input[name=privilages]').val();
    var _selectedCategory = _selectedCategory.split('|');

    var formatSelectCategoryDataToFancytree = function(data) {
      var re = [];
      var _title = '';
      var _selected = false;
      var _expanded = false;
      var _children = [];

      if( !data ){
        return;
      }
      
      $.each(data, function(index, node) {
        _title = node.name;
        _selected = false;
        _children = formatSelectCategoryDataToFancytree(node.children);

        // 多条记录
        for (var i = 0; i < _selectedCategory.length; i++) {
          if( _selectedCategory[i] == node.module_code ){
            _selected = true;
            break;
          }
        };

        // 单条记录
        // if(node.module_code == _selectedCategory) {
        //   _selected = true;
        // }

        re.push({
          title: _title,
          key: node.module_code,
          status: node.status,
          displayorder: node.displayorder,
          icon: node.icon,
          selected: _selected,
          children: _children,
          folder: true
        });
      });
      return re
    };

    // 递归展开所有节点
    var expandAllParents = function(nodes) {
      $.each(nodes, function(index, node) {
        if(node.getParent() == null || node.getParent().getParent() == null) return false;
        node.getParent().setExpanded();
        return expandAllParents(node.getParent());
      });
    };
});
</script>
@endsection