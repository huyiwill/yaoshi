angular
    .module('ohapp')
    .controller('testCtrl', function testCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $rootScope.root = "/test.html";
        $scope.submitted = false;

        //定义变量数组
        $scope.arr_is_discount = ['', '打折', '不打折'];
        $scope.updates = {};
        $scope.root_type = ['', '未销售', '销售中'];
        $scope.updates.is_discount = 1;


        $scope.lead = function (page, order, filter_type, id) {

            //若有数据传递，将传递过来的变量push进对象中。。
            var p = {page: page, order: order};
            var filter = filter_type;
            p[filter] = id;

            $http
                .get('/exercises/list.json', {params: p})
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


        //编写时间转换函数
        $scope.time_root = function (time) {
            var time = new Date(time * 1000);
            var y = time.getFullYear();//年
            var m = time.getMonth() + 1;//月
            var d = time.getDate();//日
            var h = time.getHours();//时
            var mm = time.getMinutes();//分
            var s = time.getSeconds();//秒
            m = m > 9 ? m = m : m = "0" + m;
            d = d > 9 ? d = d : d = "0" + d;
            h = h > 9 ? h = h : h = "0" + h;
            mm = mm > 9 ? mm = mm : mm = "0" + mm;
            s = s > 9 ? s = s : s = "0" + s;
            return (y + "-" + m + "-" + d + " " + h + ":" + mm + ":" + s);
        }


        $scope.add = function () {
            $scope.updates = {};
            $scope.updates.is_discount = 1;
            ngDialog.open({template: 'backend_static/views/popup/p-test.html', scope: $scope});
            $scope.types = "add";
            $rootScope.$on('ngDialog.opened', function (e, $dialog) {
                $('#discount_ends').jHsDate({
                    maxDate: '2025-12-31',
                    format: 'yyyy-MM-dd hh时mm分',
                    callBack: function () {

                    }
                });
                $('#datetimeformat').jHsDate({
                    maxDate: '2025-12-31',
                    format: 'yyyy-MM-dd hh时mm分',
                    callBack: function () {
                        $scope.updates.test_ends = $('#datetimeformat').val();
                        $scope.$apply();
                    }
                });
            });
        };
        $scope.edit = function (id) {
            $http
                .get('/exercises/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.updates = data.data;
                        $scope.updates.discount_ends = $scope.time_root($scope.updates.discount_end);
                        $scope.updates.test_ends = $scope.time_root($scope.updates.test_end);
                        ngDialog.open({template: 'backend_static/views/popup/p-test.html', scope: $scope});
                        $scope.types = "edit";
                        $rootScope.$on('ngDialog.opened', function (e, $dialog) {
                            $('#discount_ends').jHsDate({
                                maxDate: '2025-12-31',
                                format: 'yyyy-MM-dd hh时mm分'
                            });
                            $('#datetimeformat').jHsDate({
                                maxDate: '2025-12-31',
                                format: 'yyyy-MM-dd hh时mm分',
                                callBack: function () {
                                    $scope.updates.test_ends = $('#datetimeformat').val();
                                    $scope.$apply();
                                }
                            });
                        });
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
            if (form.$valid && $scope.updates.photo != undefined) {
                if ($scope.updates.is_discount == 2) {
                    $scope.updates.discount = '';
                    $("#discount_ends").val('');
                }
                if ($scope.updates.role != 2) {
                    $scope.updates.post = undefined;
                }
                if ($scope.types == "edit") {
                    $http
                        .post('/exercises/update.json', {
                            id: $scope.updates.id,
                            test_group_id: 0,
                            name: $scope.updates.name,
                            time: $scope.updates.time,
                            remark: $scope.updates.remark,
                            price: $scope.updates.price,
                            is_discount: $scope.updates.is_discount,
                            discount: $scope.updates.discount,
                            discount_end: $("#discount_ends").val(),
                            test_end: $("#datetimeformat").val(),
                            role: $scope.updates.role,
                            post: $scope.updates.post,
                            photo: $scope.updates.photo
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
                } else if ($scope.types == "add") {
                    $http
                        .post('/exercises/add.json', {
                            test_group_id: 0,
                            name: $scope.updates.name,
                            time: $scope.updates.time,
                            remark: $scope.updates.remark,
                            price: $scope.updates.price,
                            is_discount: $scope.updates.is_discount,
                            discount: $scope.updates.discount,
                            discount_end: $("#discount_ends").val(),
                            test_end: $("#datetimeformat").val(),
                            role: $scope.updates.role,
                            post: $scope.updates.post,
                            photo: $scope.updates.photo
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
                }
            } else {
                $scope.submitted = true;
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
                .post('/exercises/status.json', {id: id, status: 2})
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
                .post('/exercises/status.json', {id: id, status: 1})
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
                        .post('/exercises/del.json', {id: id})
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

        $scope.xiaoshou = function (id, type) {
            $http
                .post('/exercises/type.json', {id: id, type: type})
                .success(function (data) {
                    if (data.status) {
                        swal("干得漂亮！", "修改成功！", "success");
                        $scope.lead('', '', $scope.soso_end_type, $scope.soso_end_text);
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }


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

        function ajaxupload_photo(data) {
            photoMsg = data;
            $scope.updates.photo = data;
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
