angular
    .module( 'ohapp' )
    .controller( 'testExamCtrl', function testExamCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.lead = function (current) {
            $http
                .get('/exercises/list.json',{params:{page:current}})
                .success(function (data) {
                    if (data.status) {
                        $scope.exam = data.data;
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

        //选择支付方式
        $scope.buy_this = function(id,price,name){
            $scope.pay_type = 1;
            $scope.li_id = id;
            $scope.price_Paid = price;
            $scope.li_name = name;
        }

        //购买真题
        //确定支付
        $scope.pay_end = function (type) {
            $http
                .post('/order/add.json',{product_body:'真题购买',product_subject:$scope.li_name,type:3,relation_id:$scope.li_id,total_fee:$scope.price_Paid})
                .success(function (data) {
                    if(data.status){
                        $scope.pay_order(data.data,type);
                    }else{
                        swal("药学工具网提醒您", data.message,"error");
                    }
                });
        }

        $scope.pay_order = function (order_id,type) {
            $scope.location = window.location.href;
            if(type==2){
                $scope.location = undefined;
            }
            $http
                .post('/pay/pay.json',{order_id:order_id,pay_method:type,return_url:$scope.location})
                .success(function (data) {
                    if(data.status==undefined||data.status){
                        if(type==1){
                            $scope.choses_type_alipay = data;
                            return;
                        }
                        $scope.logn_wei_show = 1;
                        $scope.choses_types = data.data.url;
                        console.log($scope.choses_types)
                    }else{
                        swal("药学工具网提醒您", data.message,"error");
                    }
                });
        }
        $scope.close_proup = function () {
            $scope.logn_wei_show = 0;
            $scope.pay_type = 0;
            // $scope.timmer = $interval(search_order, 3000);
        }









    });
