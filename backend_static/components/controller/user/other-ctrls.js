angular
    .module('ohapp')
    .controller('otherCtrl', function otherCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $scope.updates = {};
        $scope.submitted = false;
        $scope.lead = function (page, order, filter_type, id) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page, order: order,role:1};
            var filter = filter_type;
            p[filter] = id;

            $http
                .get('/user/list.json', {params: p})
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

        //初始化加载数据
        $scope.lead();

        //新增打开弹窗
        $scope.add = function () {
            $scope.updates = {};
            ngDialog.open({template: 'backend_static/views/popup/p-other.html', scope: $scope});
            $scope.types = "add";
        };
        $scope.edit = function (id) {
            $http
                .get('/user/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        ngDialog.open({template: 'backend_static/views/popup/p-other.html', scope: $scope});
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
            if (form.$valid && $scope.updates.head != undefined) {
                if ($scope.types == "edit") {
                    $http
                        .post('/user/update.json', {
                            id:$scope.updates.id,
                            role: 1,
                            head: $scope.updates.head,
                            nickname: $scope.updates.nickname,
                            mobile: $scope.updates.mobile,
                            password: $scope.updates.pwd,
                            name: $scope.updates.name,
                            post: $scope.updates.post,
                            birthday:$scope.updates.birthday,
                            city:$scope.updates.perfect.city,
                            company:$scope.updates.perfect.company,
                            group: $scope.updates.group
                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "修改成功！", "success");
                                ngDialog.close();
                                $scope.updates.heads = '';
                                $scope.updates.paths = '';
                                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                } else if ($scope.types == "add") {
                    $http
                        .post('/user/add.json', {
                            role: 1,
                            head: $scope.updates.head,
                            nickname: $scope.updates.nickname,
                            mobile: $scope.updates.mobile,
                            password: $scope.updates.pwd,
                            name: $scope.updates.name,
                            post: $scope.updates.post,
                            birthday:$scope.updates.birthday,
                            city:$scope.updates.perfect.city,
                            company:$scope.updates.perfect.company,
                            group: $scope.updates.group

                        })
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "新增成功！", "success");
                                ngDialog.close();
                                $scope.updates.heads = '';
                                $scope.updates.paths = '';
                                $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                            } else {
                                swal("OMG!", data.message, "error");
                            }
                        });
                }
            }else{
                $scope.submitted = true;
            }
        };
        //搜索
        $scope.soso = function () {
            $scope.soso_end_type = $("#filter_type").val();
            $scope.soso_end_text = $("#filter_text").val();
            $scope.lead(1, '', $scope.soso_end_type, $scope.soso_end_text);
        }
        //设置禁用或者恢复
        $scope.set_jin = function (id) {
            $http
                .post('/user/status.json', {id: id, status: 2})
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
                .post('/user/status.json', {id: id, status: 1})
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
                        .post('/user/del.json', {id: id})
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
        $(document).ready(function () {
            var img;

        });
        function ajaxupload_photo(data) {
            photoMsg = data;
            $scope.updates.head = data;
            $("#head").attr('src', data);
            $scope.$apply();
        }

        $scope.photo = function () {
            $('#photo').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_photo(img.src);
                }
            });
        }
        //医师资格照片
        $scope.path = function () {
            $('#paths').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_path(img.src);
                }
            });
        }
        function ajaxupload_path(data) {
            photoMsg = data;
            $scope.updates.path = data;
            $("#path").attr('src', data);
            $scope.$apply();
        }
        //group

        $http
            .get('/group/drop.json')
            .success(function (data) {
                $scope.group_list = data.data;
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

        //获取医院信息
        $scope.chose_hosp = function () {
            $scope.shose_show = 1;
            $http
                .get('/region/province/drop.json')
                .success(function (data) {
                    $scope.provinces = data.data;
                });
        }
        //获取城市信息
        $scope.chose_this = function (i) {
            $scope.province_id = i;
            $scope.city_id = undefined;
            $http
                .get('/region/city/drop.json',{params:{province_id:i}})
                .success(function (data) {
                    $scope.cities = data.data;
                });
            $scope.his();
        }
        $scope.chose_city = function (i) {
            $scope.city_id = i;
            $scope.his();
        }

        $scope.chose_level = function (i) {
            $scope.level = i;
            $scope.his();
        }

        //获取城市信息
        $scope.his = function () {
            if($scope.province_id!=undefined&&$scope.city_id!=undefined&&$scope.level!=undefined){
                $http
                    .get('/hospital/drop.json',{params:{province_id:$scope.province_id,city_id:$scope.city_id,level:$scope.level}})
                    .success(function (data) {
                        $scope.hispo = data.data;
                    });
            }
        }

        //关闭弹窗
        $scope.close_proup  =function () {
            $scope.shose_show = 0;
        }
        //提交选中医院
        $scope.ti_score = function (i) {
            $scope.shose_show = 0;
            $scope.hospital_id = i;
            angular.forEach($scope.hispo,function (data,index) {
                if(data.id == i){
                    $scope.hospital = data.name;
                }
            })

        }

    });
