angular
    .module( 'ohapp' )
    .controller( 'examGradeCtrl', function examGradeCtrl( $scope, $injector, $rootScope,$stateParams) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.dan = [];
        $scope.duo = [];
        $scope.dan_actual = 0;
        $scope.dan_score = 0;
        $scope.duo_score = 0;
        $scope.duo_actual = 0;

        $scope.r_role = ['','其他','药师','医生','护士','学生']
        $http
            .post('/account.json')
            .success(function (data) {
                if(data.status){
                    if(data.data.length==0){
                        return;
                    }
                    $scope.sign_msg = data.data;
                    $scope.sign = 1;
                }
            });

        $http
            .get('/exercises/record/info.json', {params:{id:$stateParams.id}})
            .success(function (data) {
                if (data.status) {
                    $scope.exercises_info = data.data;
                    angular.forEach($scope.exercises_info.exercises_subject[0].text_msg,function (data,index) {
                        if(data.type==1){
                            $scope.dan.push(data);
                            if(data.do_right=='true'){
                                    $scope.dan_actual += Number(data.score);
                            }
                            $scope.dan_score +=  Number(data.score);
                        }else if(data.type == 2){
                            $scope.duo.push(data);
                            if(data.do_right=='true'){
                                $scope.duo_actual += Number(data.score);
                            }
                            $scope.duo_score +=  Number(data.score);
                        }
                    })
                } else {
                    swal("OMG!", data.message, "error");
                }
            });









    });



