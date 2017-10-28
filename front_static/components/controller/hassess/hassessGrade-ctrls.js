angular
    .module('ohapp')
    .controller('hassessGradeCtrl', function hassessGradeCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $scope.submitted = false;

        $scope.lead = function (page) {
            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page, assess_id: 7, state: 3};
            var name = 'name';
            p[name] = $scope.soso_search_name;

            $http
                .get('/au/list.json', {params: p})
                .success(function (data) {
                    if (data.status) {
                        $scope.cheackTotal = 0;
                        $scope.list = data.data;
                        angular.forEach($scope.list, function (data, index) {
                            data.total_score = Number(data.theory_score) + Number(data.practice_score)+Number(data.other_score);
                        })
                        $scope.total_page = Number(data.total);
                        $scope.total_index = (Number(data.current)-1)*10;
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
        };
        $scope.edit = function (id) {
            $scope.see_ach = 1;
            $scope.chan_ach = 0;
            $http
                .get('/au/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.total_scole = Number($scope.updates.theory_score) + Number($scope.updates.practice_score)+Number($scope.updates.other_score);
                        $scope.submitted = false;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //修改成绩
        $scope.change = function (id) {
            $scope.chan_ach = 1;
            $scope.see_ach = 1;
            $http
                .get('/au/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.other_scores = Number($scope.updates.other_score);
                        $scope.total_scole = Number($scope.updates.theory_score) + Number($scope.updates.practice_score)+Number($scope.other_scores);
                        $scope.submitted = false;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //动态计算成绩
        $scope.jusuan = function (i) {
            if(i==NaN||i==''){i=0}
            $scope.other_scores = parseInt(i);
            $scope.total_scole = Number($scope.updates.theory_score) + Number($scope.updates.practice_score)+Number($scope.other_scores);
            $("#pass_score").val(parseInt(i))
        }
        //确定修改成绩
        $scope.end_ti_add = function () {
            $http
                .post('/au/update.json', {
                    id:$scope.updates.id,
                    other_score:$scope.other_scores
                })
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "修改成功！", "success");
                        $scope.see_ach = 0;
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //搜索
        $scope.soso = function () {
            $scope.soso_search_name = $scope.search_name;
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
                        .post('/au/del.json', {id: id})
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
        //弹窗关闭
        $scope.close_fade = function () {
            $scope.see_ach = 0;
            $scope.fa_ach = 0;
        }
        //选中逻辑判定
        $scope.cheackTotal = 0;
        $scope.selectAll = false;
        $scope.all = function (m) {
            for (var i = 0; i < $scope.list.length; i++) {
                if (m === true) {
                    $scope.list[i].ac_state = true;
                } else {
                    $scope.list[i].ac_state = false;
                }
            }
            $scope.total();
        };
        //计算选中的人数
        $scope.total = function () {
            $scope.cheackTotal = 0;
            for (var i = 0; i < $scope.list.length; i++) {
                if ($scope.list[i].ac_state == true) {
                    $scope.cheackTotal = 1;
                    return;
                }
            }
        }
        
        //发布成绩
        $scope.read_ac = function () {
            $scope.fa_ach = 1;

        }
        //确认发布
        $scope.end_chose = function () {
            //获取选中的人数
            $scope.chose_arr = [];
            for (var i = 0; i < $scope.list.length; i++) {
                if ($scope.list[i].ac_state == true) {
                    $scope.chose_arr.push($scope.list[i].id);
                }
            }
            $http
                .post('/au/state.json', {ids: $scope.chose_arr})
                .success(function (data) {
                    if (data.status) {
                        $scope.fa_ach = 0;
                        swal("干得漂亮！", "您已发布成功！", "success");
                        $scope.lead();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }


    });
