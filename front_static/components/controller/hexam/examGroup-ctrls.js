angular
    .module('ohapp')
    .controller('examGroupCtrl', function examGroupCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $scope.submitted = false;

        $scope.lead = function (page) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page};
            p['name'] = $scope.soso_end_type;

            $http
                .get('/tg/list.json', {params: p})
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
            $scope.types = "add";
        };
        $scope.edit = function (id) {
            $http
                .get('/tg/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.types = "edit";
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //提交表单操作、判断是否为新增和修改
        $scope.chose = function () {
            if ($scope.types == "edit") {
                $http
                    .post('/tg/update.json', {
                        id: $scope.updates.id,
                        name: $scope.updates.name
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.types = "";
                            $scope.lead();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else if ($scope.types == "add") {
                $http
                    .post('/tg/add.json', {
                        name: $scope.updates.name
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "新增成功！", "success");
                            $scope.types = "";
                            $scope.lead();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            }
        };
        //搜索
        $scope.soso = function () {
            $scope.soso_end_type = $scope.search_name;
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
                        .post('/tg/del.json', {id: id})
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "删除成功！", "success");
                                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
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

        //弹窗关闭
        $scope.close_fade = function(){
            $scope.types = "";
        }
    });
