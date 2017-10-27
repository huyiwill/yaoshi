angular
    .module( 'ohapp' )
    .controller( 'my_accountCtrl', function my_accountCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        $rootScope.load_active = 2;


        //获取验证码
        $http
            .get('/code/list.json')
            .success(function (data) {
                if(data.status){
                    $scope.code_list = data.data;
                }else{
                    swal("OMG!", data.message, "error");
                }
            });
        
        //关闭弹窗
        $scope.close_proup = function () {
            $scope.shose_show = 0;
            $scope.shose_shows = 0;
        }

        //邀请好友
        $scope.yao = function () {
            if($scope.sign_msg.code==undefined||$scope.sign_msg.code==''){
                swal("OMG!", '数据获取失败，暂不能邀请好友', "error");
                return;
            }
            var url = window.location.href;
            var index = url.indexOf('/my_account');
            url = url.slice(0,index);
            $scope.share_url = url+'/logo.html?signin=0&vercode='+$scope.sign_msg.code;
            $scope.shose_shows = 1;
        }

        //获取别人的邀请信息
        $scope.ti_score = function(code){
            $http
                .post('/code/add.json',{code:code})
                .success(function (data) {
                    if(data.status){
                        swal("易百加提醒您", "操作成功","success");
                    }else{
                        swal("OMG!", data.message, "error");
                    }
                });
        }









    });
