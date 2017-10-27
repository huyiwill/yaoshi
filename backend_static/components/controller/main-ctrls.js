angular
    .module( 'ohapp' )
    .controller( 'mainCtrl', function mainCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.$on('signMsg', function(event, data) {
            $scope.sign_msg = data;
        });
        $http
            .post('/account.json')
            .success(function (data) {
                if(data.status){
                    if(data.data.length==0){
                        $state.go("signin");
                    }
                    $scope.sign_msg = data.data;
                }
            });
        $scope.signout = function(){
            $http
                .post('/logout.json')
                .success(function (data) {
                    if(data.status){
                        $state.go('signin')
                    }else{
                        swal("OMG!", data.message,"error")
                    }
                });
        }



        //定义全局变量
        $rootScope.root_role = ['','其他','药师','医生','护士','学生'];
        $rootScope.root_post = ['','其他','临床药师','咨询药师','调剂药师','执业药师'];
        $rootScope.root_status = ['','正常','禁用','删除'];












    });
