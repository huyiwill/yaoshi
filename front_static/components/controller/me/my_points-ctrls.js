angular
    .module('ohapp')
    .controller('my_pointsCtrl', function my_pointsCtrl($scope, $injector, $rootScope, $interval, $stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $rootScope.load_active = 2;
        //路由跳转提示消息
        $scope.$on('$stateChangeStart', function (evt, toState, toParams, fromState, fromParams) {
            // 如果需要阻止事件的完成
            $interval.cancel($scope.timmer);
        });

        //查看是否为支付宝跳转回来的页面
        if (location.href.indexOf('seller_id') != -1) {
            swal({
                title: "药学工具网提醒您",
                text: "支付成功！如未到账，请刷新页面",
                timer: 2000,
                allowOutsideClick: true
            });
        }
        //获取用户的信息
        $http
            .post('/account.json')
            .success(function (data) {
                if (data.status) {
                    if (data.data.length == 0) {
                        return;
                    }
                    $scope.sign_msg = data.data;
                    $rootScope.sign_msg = data.data;
                    $scope.sign = 1;
                }
            });
        $scope.types = $stateParams.type == undefined ? $scope.types = 1 : $scope.types = $stateParams.type;
        $scope.score_type = function (i) {
            $scope.init();
            if ($scope.types == i) {
                return;
            } else {
                $scope.types = i;
                if (i == 2) {
                    $scope.lead();
                }
            }
        }

        //初始化数据
        $scope.init = function () {
            $scope.pays_show = 0;
            $scope.pays_money = '';
        }

        $scope.pay_now = function () {
            $scope.pays_show = 1;
        }

        //确定支付
        $scope.pay_end = function (s, type) {
            if (!isNaN(s) && s > 0) {
                $http
                    .post('/order/add.json', {product_body: '积分充值', product_subject: '积分充值', type: 1, total_fee: s})
                    .success(function (data) {
                        if (data.status) {
                            $scope.pay_order(data.data, type);
                            $scope.order_msg(data.data);
                        } else {
                            swal("药学工具网提醒您", data.message, "error");
                        }
                    });
            } else {
                swal("药学工具网提醒您", "不是有效的数字", "error");
            }

            //
        }
        //订单支付
        $scope.pay_order = function (order_id, type) {
            $scope.location = window.location.href;
            if (type == 2) {
                $scope.location = undefined;
            }
            $http
                .post('/pay/pay.json', {order_id: order_id, pay_method: type, return_url: $scope.location})
                .success(function (data) {
                    if (data.status == undefined || data.status) {
                        if (type == 1) {
                            $scope.choses_type_alipay = data;
                            return;
                        }
                        $scope.logn_wei_show = 1;
                        $scope.choses_types = data.data.url;
                        $scope.timmer = $interval(search_order, 3000);
                    } else {
                        swal("药学工具网提醒您", data.message, "error");
                    }
                });
        }
        // $scope.close_proup = function () {
        //     $scope.logn_wei_show = 0;
        //     $interval.cancel($scope.timmer);
        // }
        function search_order() {
            $http
                .get('/order/info.json', {params: {id: $scope.order_id}})
                .success(function (data) {
                    if (data.status) {
                        if (data.data.state == 2) {
                            $scope.close_proup();
                            $interval.cancel($scope.timmer);
                            swal({
                                title: "药学工具网提醒您",
                                text: "支付成功！",
                                timer: 2000,
                                allowOutsideClick: true
                            });
                            $scope.types = 1;
                            $scope.init();
                            $http
                                .post('/account.json')
                                .success(function (data) {
                                    if (data.status) {
                                        if (data.data.length == 0) {
                                            return;
                                        }
                                        $scope.sign_msg = data.data;
                                        $rootScope.sign_msg = data.data;
                                        $scope.sign = 1;
                                    }
                                });
                        }
                    }
                });
        }

        $scope.order_msg = function (id) {
            $scope.order_id = id;
            $http
                .get('/order/info.json', {params: {id: id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.order_info = data.data;

                    } else {
                        swal("药学工具网提醒您", data.message, "error");
                    }
                });
        }
        $scope.score_type = ['未知', '积分购买', '会员办理抵扣', '历年真题购买抵扣', '邀请码赠送'];
        //获取积分记录
        $scope.lead = function (p) {
            $http
                .get('/score/list.json', {params: {page: p}})
                .success(function (data) {
                    if (data.status) {
                        $scope.score_list = data.data;
                        $scope.total_page = Number(data.total)
                        $(".tcdPageCode").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
                            backFn: function (current) {
                                next(current)
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        $scope.lead();
        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p);
        };


    });
