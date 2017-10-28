angular
    .module('ohapp')
    .controller('achievement_managementCtrl', function achievement_managementCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $rootScope.root="/achievement_management.html";
        $scope.submitted = false;

        $scope.lead = function (page, order, filter_type, id) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page,order: order};
            var filter = filter_type;
            p[filter] = id;

            $http
                .get('/admin/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.cheackTotal = 0;
                        $scope.list = data.data;
                        $(".tcdPageCode").createPage({
                            pageCount: data.total,
                            current: data.current,
                            backFn: function (current) {
                                next(current);
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error")
                    }
                });
        };
        $scope.lead();









        $scope.add = function () {
            $scope.updates = {};
            ngDialog.open({template: 'backend_static/views/popup/p-admin.html', scope: $scope});
            $scope.types = "add";
        };
        $scope.edit = function (id) {
            $http
                .get('/admin/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        ngDialog.open({template: 'backend_static/views/popup/p-admin.html', scope: $scope});
                        $scope.types = "edit";
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //提交表单操作、判断是否为新增和修改
        $rootScope.$on('ngDialog.opened', function (e, $dialog) {
            $scope.submitted = false;
        });
        $scope.chose = function (form) {
            if ($scope.types == "edit") {
                if(form.$valid){
                    $http
                        .post('/admin/update.json', {
                            id: $scope.updates.id,
                            account: $scope.updates.account,
                            nickname: $scope.updates.nickname,
                            password: $scope.updates.pwd
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                ngDialog.close();
                                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }else{
                    $scope.submitted = true;
                }
            } else if ($scope.types == "add") {
                if(form.$valid){
                    $http
                        .post('/admin/add.json', {
                            account: $scope.updates.account,
                            nickname: $scope.updates.nickname,
                            password: $scope.updates.pwd
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                ngDialog.close();
                                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }else{
                    $scope.submitted = true;
                }
            }

        };
        //搜索
        $scope.soso = function () {
            $scope.soso_end_type = $scope.soso_type;
            $scope.soso_end_text = $scope.soso_text;
            $scope.lead(1, '', $scope.soso_end_type, $scope.soso_end_text);
        }
        //设置禁用或者恢复
        $scope.set_jin = function (id) {
            $http
                .post('/admin/status.json', {id: id, status: 2})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "禁用成功！", "success");
                        $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        $scope.recovery = function (id) {
            $http
                .post('/admin/status.json', {id: id, status: 1})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "恢复成功！", "success");
                        $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
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
                        .post('/admin/del.json', {id: id})
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "删除成功！", "success");
                                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }else{
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

        // 获取相关下拉菜单信息
        $http
            .get('/role/drop.json')
            .success(function (data) {
                if (data.status) {
                    $scope.roles = data.data;
                    $scope.updates = {};
                    $scope.updates.role_id = data.data[0].id;
                } else {
                    swal("OMG!", data.message, "error");
                }
            });

        //选中逻辑判定
        $scope.cheackTotal = 0;
        $scope.selectAll = false;
        $scope.all = function (m) {
            for (var i = 0; i < $scope.list.length; i++) {
                if (m === true) {
                    $scope.list[i].state = true;
                } else {
                    $scope.list[i].state = false;
                }
            }
            $scope.total();
        };
        //计算选中的题数
        $scope.total = function () {
            $scope.cheackTotal = 0;
            for (var i = 0; i < $scope.list.length; i++) {
                if ($scope.list[i].state == true) {
                    $scope.cheackTotal = 1;
                    return;
                }
            }
        }
        //提交选中
        $scope.chose_ti = function () {
            //获取选中的id
            $scope.chose_arr = [];
            for (var i = 0; i < $scope.list.length; i++) {
                if ($scope.list[i].state == true) {
                    $scope.chose_arr.push($scope.list[i].id);
                }
            }
            $scope.delete($scope.chose_arr);

        }

    });
