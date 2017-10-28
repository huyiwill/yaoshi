angular
    .module( 'ohapp' )
    .controller( 'examSeeCtrl', function examSeeCtrl( $scope, $injector, $rootScope,$stateParams) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.dan = [];
        $scope.duo = [];
        $scope.chu = [];
        $scope.yong = [];
        $scope.wen = [];

        $scope.dan_actual = 0;
        $scope.dan_score = 0;
        $scope.duo_score = 0;
        $scope.duo_actual = 0;
        $scope.chu_score = 0;
        $scope.yong_score = 0;
        $scope.wen_score = 0;
        $scope.chu_right_score = 0;
        $scope.yong_right_score = 0;
        $scope.wen_right_score = 0;

        $http
            .get('/test/preview.json', {params:{id:$stateParams.id}})
            .success(function (data) {
                if (data.status) {
                    $scope.exercises_info = data.data;
                    angular.forEach(data.data.subject_one,function (data) {
                        $scope.dan.push(data);
                        $scope.dan_score +=  Number(data.score);
                    });
                    angular.forEach(data.data.subject_two,function (data) {
                        $scope.duo.push(data);
                        $scope.duo_score +=  Number(data.score);
                    });
                    angular.forEach(data.data.subject_four,function (data) {
                        $scope.chu.push(data);
                        $scope.chu_score +=  Number(data.score);
                    });
                    angular.forEach(data.data.subject_five,function (data) {
                        $scope.yong.push(data);
                        $scope.yong_score +=  Number(data.score);
                    });
                    angular.forEach(data.data.subject_six,function (data) {
                        $scope.wen.push(data);
                        $scope.wen_score +=  Number(data.score);
                    });
                } else {
                    swal("OMG!", data.message, "error");
                }
            });

        //修改
        $scope.change = function () {
            history.back();
        }
        //生成
        $scope.chose = function () {
            $state.go('main.bs.examList');
        }








    });
