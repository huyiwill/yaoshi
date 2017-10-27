angular
    .module('ohapp')
    .controller('examListCtrl', function examListCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');



        //关闭新增
        $scope.cancel = function () {
            $scope.add = 0;
        }
        $scope.submitted = false;

        //定义变量数组
        $scope.arr_is_discount = ['', '打折', '不打折'];
        $scope.arr_is_show = ['', '不显示', '显示'];


        $scope.lead = function (page, order, filter_type, id) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page, order: order,name:$scope.soso_name};

            $http
                .get('/test/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.cheackTotal = 0;
                        $scope.list = data.data;
                        $scope.total_page = Number(data.total)
                        $(".tcdPageCode").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
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


        $scope.add = function () {
            $scope.updates = {};
            $scope.add = 1;
            $scope.types = "add";
        };
        $scope.edit = function (id) {
            $http
                .get('/test/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.add = 1;
                        $scope.types = "edit";
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

        $scope.chose = function (form) {
            if (form.$valid) {
                if ($scope.updates.is_discount == 2) {
                    $scope.updates.discount = '';
                }
                if ($scope.updates.role != 2) {
                    $scope.updates.post = undefined;
                }
                if ($scope.types == "edit") {
                    $http
                        .post('/test/update.json', {
                            id: $scope.updates.id,
                            test_group_id: $scope.updates.test_group_id,
                            name: $scope.updates.name,
                            remark: $scope.updates.remark
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                $scope.cancel();
                                $scope.lead();
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else if ($scope.types == "add") {
                    $http
                        .post('/test/add.json', {
                            test_group_id: $scope.updates.test_group_id,
                            name: $scope.updates.name,
                            remark: $scope.updates.remark
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                $scope.cancel();
                                $scope.lead();
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }
            } else {
                $scope.submitted = true;
            }
        };
        //搜索
        $scope.soso = function () {
            $scope.soso_name = $scope.search_name;
            $scope.soso_end_text = $scope.soso_text;
            $scope.lead(1);
        }
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
                        .post('/test/del.json', {id: id})
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
            $scope.lead(p);
        };


        //非公共部分
        // 获取相关下拉菜单信息
        $http
            .get('/tg/drop.json')
            .success(function (data) {
                if (data.status) {
                    $scope.roles = data.data;
                } else {
                    swal("OMG!", data.message, "error");
                }
            });


    });
