angular
    .module( 'ohapp' )
    .controller( 'signinCtrl', function signinCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');


        $scope.signin = function () {
            $http
                .post('/login.json',{account:$scope.account,password:$scope.pwd})
                .success(function (data) {
                    console.log(data);
                    if(data.status){
                        swal("干得漂亮！", "登录成功！","success");
                        $state.go("main.home");
                    }else{
                        swal("OMG!", "登录失败了！","error")
                    }
                })
        }













    });
