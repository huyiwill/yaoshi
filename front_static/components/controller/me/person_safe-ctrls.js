angular
    .module( 'ohapp' )
    .controller( 'person_safeCtrl', function person_safeCtrl( $scope, $injector, $rootScope) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        $scope.submitted = false;
        $rootScope.load_active = 1;



        $scope.submitForm = function (form) {
            if(form){
                if($scope.pwd_01 !== $scope.pwd_02){
                    swal("OMG!",'两次密码不一致', "error")
                }else{
                    $http
                        .post('/my/password.json', {password:$scope.pwd_old,new:$scope.pwd_01})
                        .success(function (data) {
                            if (data.status) {
                                swal("干得漂亮！", "密码修改成功！", "success");
                                $http
                                    .post('/logout.json')
                                    .success(function (data) {
                                        if(data.status){
                                            $state.go("main.logo",{'signin':1});
                                        }
                                    });
                            } else {
                                swal("OMG!", data.message, "error")
                            }
                        });
                }
            }else{
                $scope.submitted = true;
            }
        }







    });
