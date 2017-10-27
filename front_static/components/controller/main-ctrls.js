angular
    .module( 'ohapp' )
    .controller( 'mainCtrl', function mainCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        


        $scope.sign = 0;
        $scope.$on('signMsg', function(event, data) {
            $scope.sign_msg = data;
            $scope.sign = 1;
        });

        $http
            .post('/account.json')
            .success(function (data) {
                if(data.status){
                        if(data.data.length==0){
                                return;
                        }
                    $scope.sign_msg = data.data;
                    $rootScope.sign_msg = data.data;
                    $scope.sign = 1;
                }
            });
        $scope.signout = function(){
            $http
                .post('/logout.json')
                .success(function (data) {
                    if(data.status){
                        $scope.sign = 0;
                        $scope.sign_msg='';
                        swal("易百加提醒您", "退出成功","success");
                        $state.go("main.logo",{"signin":1});
                    }
                });
        }


        //不建议这样写
        $(".personals,.sign_updown").hover(function () {
            $(".sign_updown").stop().fadeIn(500);
        },function () {
            $(".sign_updown").stop().fadeOut(500);
        });














    });
