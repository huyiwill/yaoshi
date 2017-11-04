angular
    .module('ohapp')
    .controller('examBuyCtrl', function examBuyCtrl($scope, $injector, $rootScope, $stateParams,$interval) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        //查看是否为支付宝跳转回来的页面
        if (location.href.indexOf('seller_id') != -1) {
            swal({
                title: "药学工具网提醒您",
                text: "支付成功！",
                timer: 2000,
                allowOutsideClick: true
            });
            $scope.shose_step = 3;
        }else{
            $scope.shose_step = 1;
        }
        //路由跳转提示消息
        $scope.$on('$stateChangeStart', function (evt, toState, toParams, fromState, fromParams) {
            // 如果需要阻止事件的完成
            $interval.cancel($scope.timmer);
        });

        $scope.is_score = 0;
        $http
            .post('/account.json')
            .success(function (data) {
                if (data.status) {
                    if (data.data.length == 0) {
                        return;
                    }
                    $scope.sign_msg = data.data;
                    $scope.score = $scope.sign_msg.score;
                } else {
                    swal("OMG!", '请先登录', "error");
                    $state.go("main.logo", {"signin": 1});
                }
            });
        //获取历年真题信息
        $http
            .get('/exercises/info.json', {params: {id: $stateParams.id}})
            .success(function (data) {
                if (data.status) {
                    $scope.ex_msg = data.data;

                    $scope.score_pank();

                } else {
                    swal("OMG!", data.message, "error");
                }
            });


        //积分抵扣
        $scope.score_d = function () {
            $scope.is_score = !$scope.is_score;
            $scope.score_pank();
        }

        //计算打折数据
        $scope.score_pank = function () {
            //判断是否在打折
            if ($scope.ex_msg.is_discount == 1) {
                $scope.can_discount = $scope.ex_msg.price;
                //在打折时间范围内
                if ($scope.ex_msg.discount_end * 1000 > Date.parse(new Date())) {
                    $scope.can_discount = $scope.can_discount * $scope.ex_msg.discount/10;
                }
            }
            //是否积分兑换
            $scope.is_btn_sc = $scope.can_discount>1&&($scope.score / 10 > $scope.can_discount/100);
            $scope.pay_discounts = $scope.can_discount/100;
            if ($scope.is_score) {
                if (!$scope.is_btn_sc) {
                    // $scope.pay_discount = $scope.score / 10;
                    $scope.pay_discount = 0;
                } else {
                    $scope.pay_discount = $scope.can_discount/100;
                }
            } else {
                $scope.pay_discount = 0;
            }

            $scope.pay_score = $scope.can_discount - $scope.pay_discount;
        }

        //提交订单
        $scope.place_order = function () {
            if ($scope.is_score) {
                $scope.pay_is_deductible = 1;
            } else {
                $scope.pay_is_deductible = 2;
            }

            $http
                .post('/order/add.json', {
                    relation_id: $scope.ex_msg.id,
                    product_body: '购买历年真题',
                    product_subject: '购买' + $scope.ex_msg.name,
                    type: 3,
                    total_fee: $scope.pay_score,
                    is_deductible: $scope.pay_is_deductible
                })
                .success(function (data) {
                    if (data.status) {
                        $scope.order_msg(data.data);
                        $scope.shose_step = 2;
                    } else {
                        swal("药学工具网提醒您", data.message, "error");
                    }
                });
        }
        //获取订单信息
        $scope.order_msg = function (id) {
            $scope.order_id = id;
            $http
                .get('/order/info.json', {params:{id:id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.order_info = data.data;
                    } else {
                        swal("药学工具网提醒您", data.message, "error");
                    }
                });
        }
        $scope.pay_order = 1;
        //支付
        $scope.pay_now = function (type) {
            $http
                .post('/pay/pay.json',{order_id:$scope.order_id,pay_method:type,return_url:window.location.href})
                .success(function (data) {
                    if(data.status==undefined||data.status){
                        if(type==1){
                            $scope.choses_type_alipay = data;
                            return;
                        }
                        $scope.logn_wei_show = 1;
                        $scope.choses_types = data.data.url;
                        $scope.timmer = $interval(search_order, 3000);
                    }else{
                        swal("药学工具网提醒您", data.message,"error");
                    }
                });
        }

        // $scope.close_proup = function () {
        //     $scope.logn_wei_show = 0;
        //     $interval.cancel($scope.timmer);
        // }
        function search_order() {
            $http
                .get('/order/info.json', {params:{id:$scope.order_id}})
                .success(function (data) {
                    if (data.status) {
                        if(data.data.state==2){
                            $scope.close_proup();
                            $interval.cancel($scope.timmer);
                            swal({
                                title: "药学工具网提醒您",
                                text: "支付成功！",
                                timer: 2000,
                                allowOutsideClick: true
                            });
                            $scope.shose_step = 3;
                        }
                    }
                });
        }


    });
