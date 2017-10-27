angular
    .module( 'ohapp' )
    .controller( 'hassessExamCtrl', function hassessExamCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.submitted = false;
        $scope.is_state = 2;

        $scope.lead = function (page) {
            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page,assess_id:7,state:$scope.is_state};
            var name = 'name';
            p[name] = $scope.soso_search_name;

            $http
                .get('/au/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.cheackTotal = 0;
                        $scope.list = data.data;
                        angular.forEach($scope.list,function(data,index){
                            data.total_score = Number(data.theory_score) + Number(data.practice_score)
                        })
                        console.log($scope.list)
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

        //
        $scope.chose_type = function (i) {
            if(i==$scope.is_state){
                return;
            }
            $scope.is_state = i;
            $scope.lead();
        }

        $scope.add = function () {
            $scope.submitted = false;
            $scope.updates = {};
            $scope.add_hass = 1;
            $scope.types = "add";
        };
        $scope.edit = function (id) {
            $http
                .get('/au/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.submitted = false;
                        $scope.add_hass = 1;
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
                        .post('/au/update.json', {
                            id: $scope.updates.id,
                            name: $scope.updates.name,
                            remark:$scope.updates.remark,
                            test_id: $scope.updates.test_id,
                            pass_score: $scope.updates.pass_score,
                            start_time: $scope.updates.start_time,
                            end_time: $scope.updates.end_time,
                            time: $scope.updates.time
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                $scope.add_hass = 0;
                                $scope.lead();
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
                        .post('/au/add.json', {
                            name: $scope.updates.name,
                            remark:$scope.updates.remark,
                            test_id: $scope.updates.test_id,
                            pass_score: $scope.updates.pass_score,
                            start_time: $scope.updates.start_time,
                            end_time: $scope.updates.end_time,
                            time: $scope.updates.time
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                $scope.add_hass = 0;
                                $scope.lead();
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
            $scope.soso_search_name = $scope.search_name;
            $scope.lead();
            // $scope.soso_end_type = $scope.soso_type;
            // $scope.soso_end_text = $scope.soso_text;
            // $scope.lead(1, '', $scope.soso_end_type, $scope.soso_end_text);
        }
        //设置禁用或者恢复
        $scope.set_jin = function (id) {
            $http
                .post('/au/status.json', {id: id, status: 2})
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
                .post('/au/status.json', {id: id, status: 1})
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
                        .post('/au/del.json', {id: id})
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








    });
