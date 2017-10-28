angular
    .module( 'ohapp' )
    .controller( 'homeCtrl', function homeCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        $rootScope.root = '/index.html'
        $http
            .post('/account.json')
            .success(function (data) {
                if(data.status){
                    if(data.data.length!=0){
                        $scope.$emit('signMsg', data.data);
                    }else{
                        $state.go("signin");
                    }
                }
            });













    });
