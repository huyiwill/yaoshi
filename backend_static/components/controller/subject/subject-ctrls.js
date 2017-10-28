angular
    .module('ohapp')
    .controller('subjectCtrl', function subjectCtrl($scope, $injector, $rootScope, ngDialog) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        $rootScope.root = '/subject.html';

        $scope.chose_type = 1;
        $scope.chose_this = function(t){
            if($scope.chose_type==t){
                return;
            };
            $scope.chose_type=t;

        };


    });
