angular
    .module('ohapp')
    .controller('memberCtrl', function memberCtrl($scope, $injector, $rootScope, $interval) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        //路由跳转提示消息
        $scope.$on('$stateChangeStart', function (evt, toState, toParams, fromState, fromParams) {
            // 如果需要阻止事件的完成
            $interval.cancel($scope.timmer);
        });

        //查看是否为支付宝跳转回来的页面
        if (location.href.indexOf('seller_id') != -1) {
            swal({
                title: "易百加提醒您",
                text: "支付成功！如未到账，请刷新页面",
                timer: 2000,
                allowOutsideClick: true
            });
        }

        //假定数据
        $scope.price_Original = 368;
        $scope.price_Discount = 312;
        $scope.price_Discount = 0.01;


        //不使用积分抵扣
        $scope.is_score = 0;
        //
        //获取用户的信息
        $scope.load_user = function () {
            $http
                .post('/account.json')
                .success(function (data) {
                    if (data.status) {
                        if (data.data.length == 0) {
                            return;
                        }
                        $scope.sign_msg = data.data;
                        $scope.score = data.data.score;
                    }
                });
        }
        $scope.load_user();

        $scope.mumber_audible = function () {
            //初始化数据
            $scope.is_month = 1;

            //获取当前的时间
            var time_now = new Date();
            $scope.year_now = time_now.getFullYear();
            $scope.month_now = time_now.getMonth() + 1;
            $scope.day_now = time_now.getDate();

            $scope.nonth_change(1)

            $scope.buy_mumber_audible = 1;
        }

        //选择充值方式（月份、年份）
        $scope.chose_time = function (i) {
            $(".month").val('');
            if (i == 2) {
                $scope.is_month = 2;
                $scope.year_change(1);

            } else {
                $scope.nonth_change(1);
                $scope.is_month = 1;
            }
        }

        //相关充值方式的计算
        $scope.nonth_change = function (month) {
            if (month == '') {
                month = 1
            }
            $scope.chose_month = month;
            $scope.month_type = 1;
            $scope.non_num = month;

            if (Number(month) + Number($scope.month_now) > 12) {
                $scope.show_year = Number($scope.year_now) + 1;
                $scope.show_month = Number(month) + Number($scope.month_now) - 12;
                $scope.show_day = $scope.day_now;
            } else {
                $scope.show_year = Number($scope.year_now);
                $scope.show_month = Number(month) + Number($scope.month_now);
                $scope.show_day = $scope.day_now;
            }
            $scope.is_date = $scope.show_year + "-" + $scope.show_month + "-" + $scope.show_day;
            $scope.calcul_price();
        }
        $scope.year_change = function (year) {
            if (year == '') {
                year = 1
            }
            $scope.month_type = 2;
            $scope.non_num = year;
            $scope.chose_month = year * 12;
            $scope.show_year = Number($scope.year_now) + Number(year);
            $scope.show_month = Number($scope.month_now);
            $scope.show_day = $scope.day_now;
            $scope.is_date = $scope.show_year + "-" + $scope.show_month + "-" + $scope.show_day;
            $scope.calcul_price();
        }

        //计算价格
        $scope.calcul_price = function () {
            // 实际价格
            $scope.price_Paids = ($scope.price_Discount) * $scope.chose_month;
            $scope.show_score_btn = ($scope.price_Paids >= 1 && $scope.score * 0.1 > $scope.price_Paids * 0.01);
            $scope.coore_dis = $scope.price_Paids * 0.01;
            //积分抵扣
            if ($scope.score > 0 && $scope.is_score) {
                if ($scope.score * 0.1 > $scope.price_Paids * 0.01) {
                    $scope.price_Paid = $scope.price_Paids - $scope.price_Paids * 0.01;
                    $scope.coore_di = $scope.price_Paids * 0.01;
                } else {
                    // $scope.price_Paid = $scope.price_Paids - $scope.score * 0.1;
                    // $scope.coore_di = $scope.score * 0.1;
                    $scope.price_Paid = $scope.price_Paids;
                    $scope.coore_di = 0;
                }
            } else {
                $scope.price_Paid = $scope.price_Paids;
                $scope.coore_di = 0;
            }
        }
        //切换积分抵扣
        $scope.score_d = function () {
            $scope.is_score = !$scope.is_score;
            $scope.calcul_price();
        }

        //购买逻辑
        //确定支付
        $scope.pay_end = function (type) {
            if ($scope.is_score) {
                $scope.pay_is_deductible = 1;
            } else {
                $scope.pay_is_deductible = 2;
            }
            $http
                .post('/order/add.json', {
                    product_body: '会员充值',
                    product_subject: '会员充值',
                    type: 2,
                    total_fee: $scope.price_Paid,
                    time_type: $scope.month_type,
                    number: $scope.non_num,
                    is_deductible: $scope.pay_is_deductible
                })
                .success(function (data) {
                    if (data.status) {
                        $scope.pay_order(data.data, type);
                        $scope.order_msg(data.data);
                    } else {
                        swal("易百加提醒您", data.message, "error");
                    }
                });
        }

        $scope.pay_order = function (order_id, type) {
            $http
                .post('/pay/pay.json', {order_id: order_id, pay_method: type, return_url: window.location.href})
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
                        swal("易百加提醒您", data.message, "error");
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
                                title: "易百加提醒您",
                                text: "支付成功！",
                                timer: 2000,
                                allowOutsideClick: true
                            });

                            //重新获取用户的信息
                            $scope.load_user();
                            $scope.mumber_audible();
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
                        swal("易百加提醒您", data.message, "error");
                    }
                });
        }

        //卡号开通
        $scope.chose_cart = function (cart_number) {
            $http
                .post('/member/add.json', {number: cart_number})
                .success(function (data) {
                    if (data.status) {
                        swal({
                            title: "易百加提醒您",
                            text: "开通成功！",
                            timer: 2000,
                            allowOutsideClick: true
                        });
                        $scope.buy_mumber_audible = 0;
                        $scope.cart_number = undefined;
                        $scope.load_user();
                    } else {
                        swal("易百加提醒您", data.message, "error");
                    }
                });
        }


    });
