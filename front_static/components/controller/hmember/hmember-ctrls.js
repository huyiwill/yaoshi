angular
    .module( 'ohapp' )
    .controller( 'hmemberCtrl', function hmemberCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');


        $scope.submitted = false;
        $scope.chose_step = 'load';
        $scope.role_arr = ['','其他','药师','医生','护士','学生'];

        $scope.lead = function (page) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page};
            var name = 'search';
            p[name] = $scope.soso_search_name;

            $http
                .get('/am/list.json', {params: p})
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

        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p);
        };


        //点击添加按钮。搜索成员
        $scope.add = function () {
            $scope.chose_step = 'add';
            $scope.chose_search = function (search) {
                $http
                    .get('/am/search.json',{params: {
                        search:search
                    }})
                    .success(function (data) {
                        if (data.status) {
                            $scope.search_msg = data.data;
                            $scope.chose_step = 'result';
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            }

        };
        //搜索后添加成员
        $scope.add_mum = function (id) {
            $http
                .post('/am/add.json',{uid: id})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.lead();
                        $scope.chose_step = 'load';
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //查看用户详情
        $scope.edit = function (id) {
            $http
                .get('/am/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.chose_step='see';
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //关闭查看
        $scope.close_see = function () {
            $scope.chose_step = 'load';
        }

        //搜索
        $scope.soso = function (search_name) {
            $scope.soso_search_name = search_name;
            $scope.lead();
            // $scope.soso_end_type = $scope.soso_type;
            // $scope.soso_end_text = $scope.soso_text;
            // $scope.lead(1, '', $scope.soso_end_type, $scope.soso_end_text);
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
                        .post('/am/del.json', {id: id})
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "删除成功！", "success");
                                $scope.lead();
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
            $scope.lead(p);
        };


        //非公共部分

        //更换分组
        $scope.chose_group = function (id) {
            $scope.chose_types = 'change_group';
            $scope.group_user = id;
            //获取分组
            $http
                .get('/ag/list.json')
                .success(function (data) {
                    if (data.status) {
                        $scope.ag_group = data.data;

                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

        //关闭弹窗
        $scope.close_proup = function () {
            $scope.chose_types = '';
            $scope.group_user = '';
        }

        $scope.chose_this = function (id) {
            $scope.check_id = id;
        }
        //提交更换分组
        $scope.end_chose_group = function(id){
            $http
                .post('/am/update.json', {
                    id: $scope.group_user,
                    members_group_id:$scope.check_id
                })
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "修改成功！", "success");
                        $scope.close_proup();
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }





    });
