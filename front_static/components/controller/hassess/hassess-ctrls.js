angular
    .module( 'ohapp' )
    .controller( 'hassessCtrl', function hassessCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');


        $scope.submitted = false;

        $scope.lead = function (page) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p={page:page};
            var name = 'name';
            p[name] = $scope.soso_search_name;

            $http
                .get('/assess/list.json', {params: p})
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
            $scope.submitted = false;
            $scope.updates = {};
            $scope.add_hass = 1;
            $scope.types = "add";
        };
        $scope.edit = function (id) {
            $http
                .get('/assess/info.json', {params: {id: id}})
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
                        .post('/assess/update.json', {
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
                        .post('/assess/add.json', {
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
                .post('/assess/status.json', {id: id, status: 2})
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
                .post('/assess/status.json', {id: id, status: 1})
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
                        .post('/assess/del.json', {id: id})
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
        $scope.total_all = function(m){
            for (var i = 0; i < $scope.men_drop.length; i++) {
                if (m === true) {
                    $scope.men_drop[i].state = true;
                    console.log($scope.men_drop[i].members.length)
                    for(var j = 0;j<$scope.men_drop[i].members.length;j++){
                        $scope.men_drop[i].members[j].state = true;
                    }
                    $scope.selectAll = true;
                } else {
                    $scope.men_drop[i].state = false;
                    for(var j = 0;j<$scope.men_drop[i].members.length;j++){
                        $scope.men_drop[i].members[j].state = false;
                    }
                    $scope.selectAll = false;
                }
            }
            console.log($scope.men_drop)
            $scope.total();
        }
        //选中逻辑判定
        $scope.cheackTotal = 0;
        $scope.selectAll = false;
        $scope.all = function (m,index) {
            for (var i = 0; i < $scope.men_drop[index].members.length; i++) {
                if (m === true) {
                    $scope.men_drop[index].members[i].state = true;
                } else {
                    $scope.men_drop[index].members[i].state = false;
                }
            }
            $scope.total();
        };
        //计算选中的题数
        $scope.total = function () {
            $scope.cheackTotal = 0;
            for (var i = 0; i < $scope.men_drop.length; i++) {
                //
                for(var j = 0;j<$scope.men_drop[i].members.length;j++){
                    if ($scope.men_drop[i].members[j].state == true) {
                            $scope.cheackTotal = 1;
                            return;
                        }
                }
            }
        }
        //提交选中
        // $scope.chose_ti = function () {
        //     //获取选中的id
        //     $scope.chose_arr = [];
        //     for (var i = 0; i < $scope.list.length; i++) {
        //         if ($scope.list[i].state == true) {
        //             $scope.chose_arr.push($scope.list[i].id);
        //         }
        //     }
        //     $scope.delete($scope.chose_arr);
        //
        // }


        //处理表单中的时间
        $('#start_time').jHsDate({
            maxDate: '2025-12-31',
            minDate: new Date(),
            format: 'yyyy-MM-dd hh时mm分',
            callBack: function () {
                $scope.updates.start_time = $('#start_time').val();
                $scope.$apply();
            }
        });
        $('#end_time').jHsDate({
            maxDate: '2025-12-31',
            minDate: new Date(),
            format: 'yyyy-MM-dd hh时mm分',
            callBack: function () {
                $scope.updates.end_time = $('#end_time').val();
                $scope.$apply();
            }
        });


        //关联成员
        $scope.chose_men = function (id) {
            $scope.shijuan_id = id;
            $scope.chose_types = 'chose_men';
            $http
                .get('/am/drop.json')
                .success(function (data) {
                    if (data.status) {
                        $scope.men_drop = data.data;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //关闭关联
        $scope.close_proup = function () {
            $scope.chose_types = '';
        }
        $scope.end_chose_group = function () {
            $scope.uids=[];
            for (var i = 0; i < $scope.men_drop.length; i++) {
                for(var j = 0;j<$scope.men_drop[i].members.length;j++){
                    if ($scope.men_drop[i].members[j].state == true) {
                        $scope.uids.push($scope.men_drop[i].members[j].uid)
                    }
                }
            }
            $http
                .post('/assess/relation.json', {uid: $scope.uids,assess_id:$scope.shijuan_id})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "关联成功！", "success");
                        $scope.close_proup();
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }







    });
