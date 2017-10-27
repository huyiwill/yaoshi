angular
    .module('ohapp')
    .controller('memberGroupCtrl', function memberGroupCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');


        $scope.submitted = false;
        $scope.list_num = 1;

        $scope.lead = function (page) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page};
            var name = 'name';
            p[name] = $scope.soso_search_name;

            $http
                .get('/ag/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.cheackTotal = 0;
                        $scope.list = data.data;
                        $(".tcdPageCode").createPage({
                            pageCount: data.total,
                            current: data.current,
                            backFn: function (current) {
                                next(current)
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        $scope.lead();


        $scope.add_group = function () {
            $scope.types = 'add_group';
            // $scope.submitted = false;
            $scope.updates = {};
            // $scope.add_hass = 1;
            // $scope.types = "add";
        };
        //关闭弹窗
        $scope.close_fade = function () {
            $scope.types = '';
        }
        $scope.edit = function (id) {
            $http
                .get('/ag/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.types = "update_group";
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //提交表单操作、判断是否为新增和修改
        $rootScope.$on('ngDialog.opened', function (e, $dialog) {
            $scope.submitted = false;
        });
        $scope.chose = function () {
            if ($scope.types == "update_group") {
                $http
                    .post('/ag/update.json', {
                        id: $scope.updates.id,
                        name: $scope.updates.name
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.types = '';
                            $scope.lead();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else if ($scope.types == "add_group") {
                $http
                    .post('/ag/add.json', {
                        name: $scope.updates.name,
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "新增成功！", "success");
                            $scope.types = '';
                            $scope.lead();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            }

        };
        //搜索
        $scope.soso = function (search_name) {
            $scope.soso_search_name = search_name;
            $scope.lead();
            // $scope.soso_end_type = $scope.soso_type;
            // $scope.soso_end_text = $scope.soso_text;
            // $scope.lead(1, '', $scope.soso_end_type, $scope.soso_end_text);
        }
        //设置禁用或者恢复
        $scope.set_jin = function (id) {
            $http
                .post('/ag/status.json', {id: id, status: 2})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "禁用成功！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        $scope.recovery = function (id) {
            $http
                .post('/ag/status.json', {id: id, status: 1})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "恢复成功！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        };
        //删除
        $scope.delete = function (id) {
            swal({
                title: "确定删除吗？",
                text: "删除后不可恢复该操作！",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确定删除！",
                cancelButtonText: "取消删除！",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $http
                        .post('/ag/del.json', {id: id})
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "删除成功！", "success");
                                $scope.lead();
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else {
                    swal.close();
                }
            });
        }
        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p, '', $scope.soso_end_type, $scope.soso_end_text);
        };
        //排序
        $(".tr_order").on('click', ".fa-sort-amount-asc", function () {
            $(this).attr('class', 'fa fa-fw fa-sort-amount-desc');
            var order = $(this).data('order') + ' asc';
            $scope.lead('', order, $scope.soso_end_type, $scope.soso_end_text);
        });
        $(".tr_order").on('click', ".fa-sort-amount-desc", function () {
            $(this).attr('class', 'fa fa-fw fa-sort-amount-asc');
            var order = $(this).data('order') + ' desc';
            $scope.lead('', order, $scope.soso_end_type, $scope.soso_end_text);
        });


        //非公共部分

        //选中逻辑判定
        $scope.cheackTotal = 0;
        $scope.selectAll = false;
        $scope.all = function (m) {
            for (var i = 0; i < $scope.am_list.length; i++) {
                if (m === true) {
                    $scope.am_list[i].state = true;
                } else {
                    $scope.am_list[i].state = false;
                }
            }
            $scope.total();
        };
        //计算选中的题数
        $scope.total = function () {
            $scope.cheackTotal = 0;
            for (var i = 0; i < $scope.am_list.length; i++) {
                if ($scope.am_list[i].state == true) {
                    $scope.cheackTotal = 1;
                    return;
                }
            }
        }
        //关联成员
        $scope.guanlian = function(id){
            $scope.group_id = id;
            //获取成员信息
            $http
                .get('/am/list.json')
                .success(function (data) {
                    if (data.status) {
                        $scope.am_list = data.data;
                        $scope.list_num = 2;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //取消关联成员
        $scope.putin = function(){
            $scope.group_id = '';
            $scope.list_num = 1;
        }
        //提交关联成员
        $scope.end_ti = function(){
            //计算选中的id
            $scope.uids=[];
            for (var i = 0; i < $scope.am_list.length; i++) {
                if ($scope.am_list[i].state == true) {
                    $scope.uids.push($scope.am_list[i].id);
                }
            };
            $http
                .post('/assess/relation.json', {uid: $scope.uids,assess_id:$scope.group_id})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "关联成功！", "success");
                        $scope.list_num = 1;
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }


    });
