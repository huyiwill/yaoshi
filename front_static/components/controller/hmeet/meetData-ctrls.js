angular
  .module('ohapp')
  .controller('meetDataCtrl', function meetDataCtrl($scope, $injector, $rootScope){
    var $http = $injector.get('$http');
    var $location = $injector.get('$location');
    var $state = $injector.get('$state');
    var $timeout = $injector.get('$timeout');
    var $config = $injector.get('$config');
    var $session = $injector.get('$session');

    $rootScope.root = "/meeting.html";
    $scope.submitted = false;

    $scope.ex_type = ['未发布', '已发布'];

    $scope.lead = function(page, order){
      //若有数据传递，将传递过来的变量push进对象中。。
      var p = {
        page : page,
        order: order
      };
      p['soso_search_name'] = $scope.soso_search_name;

      $http
        .get('/meetingdata/list.json', {params: p})
        .success(function(data){
          if(data.status){
            $scope.cheackTotal = 0;
            $scope.list = data.data;
            $(".tcdPageCode").createPage({
              pageCount: data.total,
              current  : data.current,
              backFn   : function(current){
                next(current);
              }
            });
          }else{
            swal("OMG!", data.message, "error")
          }
        });
    };

    $scope.lead();

    /**
     * 搜索
     */
    $scope.soso = function(){
      $scope.soso_search_name = $scope.search_name;
      $scope.lead(1, '', $scope.soso_search_name);
    };

    /**
     * 设置禁用
     * */
    $scope.set_jin = function(id, i){
      $http
        .post('/meetingdata/status.json', {
          id   : id,
          index: i
        })
        .success(function(data){
          if(data.status){
            swal("干得漂亮！", "禁用成功！", "success");
            $scope.lead('', '', $scope.soso_search_name);
          }else{
            swal("OMG!", data.message, "error");
          }
        });
    };

    //设置恢复
    $scope.recovery = function(id, index){
      $http
        .post('/meetingdata/status.json', {
          id    : id,
          index : index,
          status: 1
        })
        .success(function(data){
          if(data.status){
            swal("干得漂亮！", "恢复成功！", "success");
            $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
          }else{
            swal("OMG!", data.message, "error");
          }
        });
    };

    /**
     *删除
     */
    $scope.delete = function(id, i){
      swal({
        title             : "确定删除吗？",
        text              : "删除后不可恢复该操作！",
        type              : "warning",
        showCancelButton  : true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText : "确定删除！",
        cancelButtonText  : "取消删除！",
        closeOnConfirm    : false,
        closeOnCancel     : false
      }, function(isConfirm){
        if(isConfirm){
          $http
            .post('/meetingdata/del.json', {
              id   : id,
              index: i
            })
            .success(function(data){
              if(data.status){
                swal("干得漂亮！", "删除成功！", "success");
                $scope.lead('', '', $scope.soso_search_name);
              }else{
                swal("OMG!", data.message, "error");
              }
            });
        }else{
          swal.close();
        }
      });
    };

    //翻页
    function next(p){
      $('#page').val(p);
      $scope.lead(p, '', $scope.soso_end_type, $scope.soso_end_text);
    };

    //排序
    $(".tr_order").on('click', ".fa-sort-amount-asc", function(){
      $(this).attr('class', 'fa fa-fw fa-sort-amount-desc');
      var order = $(this).data('order') + ' asc';
      $scope.lead('', order, $scope.soso_end_type, $scope.soso_end_text);
    });

    $(".tr_order").on('click', ".fa-sort-amount-desc", function(){
      $(this).attr('class', 'fa fa-fw fa-sort-amount-asc');
      var order = $(this).data('order') + ' desc';
      $scope.lead('', order, $scope.soso_end_type, $scope.soso_end_text);
    });

    /**
     * 上传按钮
     */
    $scope.uploadData = function(id, i){
      $scope.add_dataMsg(id, i);
    };

    /**
     *上传资料
     */
    $scope.add_dataMsg = function(id, i){
      $scope.chose_id = id;
      $scope.meeting_add = 1;
      $scope.index = i;
    };

    /**
     *关闭上传资料界面
     */
    $scope.close_proup = function(){
      $scope.meeting_add = 0;
      $("input[type='file']").val('');
    };

    /**
     * 开始上传资料
     */
    $scope.chose = function(){
      var url = "/meetingdata/data.json";
      var file = $scope.fileToUpload;
      if(!file){
        swal("OMG!", '请先上传文件', "error");
        return;
      }
      if(file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type == 'application/docx' || file.type == 'application/pdf' || file.type == 'application/msword' || file.type == 'application/vnd.ms-excel' || file.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || file.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.template' || file.type == 'application/vnd.ms-excel.addin.macroEnabled.12' || file.type == 'application/vnd.ms-excel.sheet.binary.macroEnabled.12'){
        // fileUpload.uploadFileToUrl( file, url,$scope.chose_id );
        var fd = new FormData();
        fd.append("data[]", file);
        fd.append("id", $scope.chose_id);
        fd.append("i", $scope.index);
        $http
          .post('/meetingdata/data.json', fd, {
            transformRequest: angular.identity,
            headers         : {"Content-Type": undefined}
          })
          .success(function(data){
            if(data.status){
              swal("干得漂亮！", "上传成功！", "success");
              $scope.lead('', '', $scope.soso_search_name);
              $scope.close_proup();
              $("input[type='file']").val('');
            }else{
              swal("OMG!", data.message, "error");
            }
          });
      }else{
        swal("OMG!", '上传的格式错误', "error");
      }
    };

    //选中逻辑判定
    $scope.cheackTotal = 0;
    $scope.selectAll = false;
    $scope.all = function(m){
      for(var i = 0; i < $scope.list.length; i++){
        if(m === true){
          $scope.list[i].state = true;
        }else{
          $scope.list[i].state = false;
        }
      }
      $scope.total();
    };
    //计算选中的题数
    $scope.total = function(){
      $scope.cheackTotal = 0;
      for(var i = 0; i < $scope.list.length; i++){
        if($scope.list[i].state == true){
          $scope.cheackTotal = 1;
          return;
        }
      }
    };
    //提交选中
    $scope.chose_ti = function(){
      //获取选中的id
      $scope.chose_arr = [];
      for(var i = 0; i < $scope.list.length; i++){
        if($scope.list[i].state == true){
          $scope.chose_arr.push($scope.list[i].id);
        }
      }
      $scope.delete($scope.chose_arr);

    };

    //发布
    $scope.release = function(id, type){
      swal({
        title             : "药学工具网提醒您",
        text              : "修改发布信息吗！",
        type              : "warning",
        showCancelButton  : true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText : "确定修改！",
        cancelButtonText  : "取消修改！",
        closeOnConfirm    : false,
        closeOnCancel     : false
      }, function(isConfirm){
        if(isConfirm){
          $http
            .post('/meeting/release.json', {
              id        : id,
              is_release: type
            })
            .success(function(data){
              if(data.status){
                swal("干得漂亮", "修改发布信息成功！", "success");
                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
              }else{
                swal("OMG!", data.message, "error");
              }
            });
        }else{
          swal.close();
        }
      });
    };

    //报名
    $scope.sign_up = function(id, type){
      swal({
        title             : "药学工具网提醒您",
        text              : "修改报名信息吗！",
        type              : "warning",
        showCancelButton  : true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText : "确定修改！",
        cancelButtonText  : "取消修改！",
        closeOnConfirm    : false,
        closeOnCancel     : false
      }, function(isConfirm){
        if(isConfirm){
          $http
            .post('/meeting/enroll.json', {
              id          : id,
              enroll_state: type
            })
            .success(function(data){
              if(data.status){
                swal("干得漂亮！", "修改报名信息成功！", "success");
                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
              }else{
                swal("OMG!", data.message, "error");
              }
            });
        }else{
          swal.close();
        }
      });
    };

    //会议
    $scope.close_meet = function(id, type){
      swal({
        title             : "药学工具网提醒您",
        text              : "修改会议信息吗！",
        type              : "warning",
        showCancelButton  : true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText : "确定修改！",
        cancelButtonText  : "取消修改！",
        closeOnConfirm    : false,
        closeOnCancel     : false
      }, function(isConfirm){
        if(isConfirm){
          $http
            .post('/meeting/state.json', {
              id   : id,
              state: type
            })
            .success(function(data){
              if(data.status){
                swal("干得漂亮！", "修改会议信息成功！", "success");
                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
              }else{
                swal("OMG!", data.message, "error");
              }
            });
        }else{
          swal.close();
        }
      });

    };

    //操作
    $scope.change_select = function(id, i){
      $("#selecteds").val('');
      if(i == ''){
        return;
      }else if(i == 2){
        $scope.sign_up(id, 2);
        return;
      }else if(i == 22){
        $scope.sign_up(id, 1);
        return;
      }else if(i == 3){
        $scope.close_meet(id, 2);
        return;
      }else if(i == 33){
        $scope.close_meet(id, 1);
        return;
      }else if(i == 4){
        $scope.add_dataMsg(id);
      }
    }

  });
