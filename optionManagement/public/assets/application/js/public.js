
/**
* 查看证书
* @param  {[type]} id [description]
* @return {[type]}    [description]
*/
var viewCertificate = function(id){
    window.location.href = "user/show_certificate?id="+id;
}

/**
 * 消息
 * @return {[type]} [description]
 */
var notification = function(){
    window.location.href = "user/notification";
}


/**
* 上传资料
* @return {[type]} [description]
*/
var uploadData = function(id){
    window.location.href = "user/score_data/show?id="+id;
};


/**
* 查看评分
* @param  {[type]} id [description]
* @return {[type]}    [description]
*/
var viewCreditRating = function(id){
    window.location.href = "user/credit_rating?id="+id;
}

// var loginOut = function(){
//   $.ajax({
//     type:'post',
//     url:'api/login_out',
//     dataType:'json',
//     timeout : 0, //超时时间设置，单位毫秒
//     data:{
//       _token:"{{ csrf_token() }}",
//     },
//
//     success:function(data){
//       $("#logined").addClass('display');
//       $("#notlogin").removeClass('display');
//     },error:function(err){
//     }
//
//   });
// }

// var login = function(data){
//     $.ajax({
//         type:'post',
//         url:'api/login',
//         dataType:'json',
//         timeout : 0, //超时时间设置，单位毫秒
//         data:{
//             _token:"{{ csrf_token() }}",
//             data:data
//         },
//
//         success:function(data){
//             if(data.error == '500001'){
//                 loginOut();
//                 // window.location.href = "login";
//             }else if(data.error == '200'){
//             }else{
//                 alert("服务器错误！");
//                 loginOut();
//                 // window.location.href = window.location.href;
//             }
//         },error:function(err){
//             if(err.status == 422){
//                 alert('参数错误！');
//             }
//         }
//
//     });
// }




/*****************************公共部分开始********************************/

/**
 * sleep
 * @param  {[type]} n [description]
 * @return {[type]}   [description]
 */
function sleep(n) { //n表示的毫秒数
    var start = new Date().getTime();
    while (true) if (new Date().getTime() - start > n) break;
}

//阻止冒泡事件  
function stopBubble(e) {  
    if (e && e.stopPropagation) {//非IE  
        e.stopPropagation();  
    }  
    else {//IE  
        window.event.cancelBubble = true;  
    }  
} 

// 阻止默认行为
function stopDefault(e){
    window.event? window.event.returnValue = false : e.preventDefault();
}
 
/**
 * 获取文件名
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function getFileName(obj){  
  var fileName="";  
    
  if(typeof(fileName) != "undefined"){  
    fileName = $(obj).val().split("\\").pop();  
    fileName=fileName.substring(0, fileName.lastIndexOf("."));  
  }  
  return fileName;  
}


/*
*名称:图片上传本地预览插件 v1.1
*作者:周祥
*时间:2013年11月26日
*介绍:基于JQUERY扩展,图片上传预览插件 目前兼容浏览器(IE 谷歌 火狐) 不支持safari
*插件网站:http://keleyi.com/keleyi/phtml/image/16.htm
*参数说明: Img:图片ID;Width:预览宽度;Height:预览高度;ImgType:支持文件类型;Callback:选择文件显示图片后回调方法;
*使用方法: 
<div>
<img id="ImgPr" width="120" height="120" /></div>
<input type="file" id="up" />
把需要进行预览的IMG标签外 套一个DIV 然后给上传控件ID给予uploadPreview事件
$("#up").uploadPreview({ Img: "ImgPr", Width: 120, Height: 120, ImgType: ["gif", "jpeg", "jpg", "bmp", "png"], Callback: function () { }});
*/
jQuery.fn.extend({
    uploadPreview: function (opts) {
        var _self = this,
            _this = $(this);
        opts = jQuery.extend({
            Img: "ImgPr",
            Width: 100,
            Height: 100,
            ImgType: ["gif", "jpeg", "jpg", "bmp", "png"],
            Callback: function () {}
        }, opts || {});
        _self.getObjectURL = function (file) {
            var url = null;
            if (window.createObjectURL != undefined) {
                url = window.createObjectURL(file)
            } else if (window.URL != undefined) {
                url = window.URL.createObjectURL(file)
            } else if (window.webkitURL != undefined) {
                url = window.webkitURL.createObjectURL(file)
            }
            return url;
        };
        _this.change(function () {
            if (this.files[0].size > 2097152) {
               alert("上传的文件大小不能超过2M");
               this.value = null;
               return false;
            } 

            if (this.value) {
                if (!RegExp("\.(" + opts.ImgType.join("|") + ")$", "i").test(this.value.toLowerCase())) {
                    alert("选择文件错误,图片类型必须是" + opts.ImgType.join("，") + "中的一种");
                    this.value = "";
                    return false;
                }
                $("#" + opts.Img).attr('src', _self.getObjectURL(this.files[0]));

                // if ($.browser.msie) {
                //     try {
                //         $("#" + opts.Img).attr('src', _self.getObjectURL(this.files[0]))
                //     } catch (e) {
                //         var src = "";
                //         var obj = $("#" + opts.Img);
                //         var div = obj.parent("div")[0];
                //         _self.select();
                //         if (top != self) {
                //             window.parent.document.body.focus()
                //         } else {
                //             _self.blur()
                //         }
                //         src = document.selection.createRange().text;
                //         document.selection.empty();
                //         obj.hide();
                //         obj.parent("div").css({
                //             'filter': 'progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)',
                //             'width': opts.Width + 'px',
                //             'height': opts.Height + 'px'
                //         });
                //         div.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = src
                //     }
                // } else {
                //     $("#" + opts.Img).attr('src', _self.getObjectURL(this.files[0]))
                // }
                opts.Callback();
            }
        })
    }
});

/**
* 检测手机号格式
* @return {[type]} [description]
*/
var checkMobileCorrect = function(mobile){
    var myreg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/; 
    return myreg.test(mobile);
}

/**
 * 检测密码至少8为，包含一个字母一个数字
 */
var checkPwd = function(pwd){
    if( pwd.length >= 8 && pwd != null ){
        var reg = new RegExp(/^(?![^a-zA-Z]+$)(?!\D+$)/);
        if( reg.test(pwd) ){
            return true;
        }
    }
    return false;
}

/**
 * 检测身份证号码
 * @param  {[type]} card [description]
 * @return {[type]}      [description]
 */
var checkIdCardNo = function(card){  
   // 身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X  
   var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;  
   return reg.test(card);
}  