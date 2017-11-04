angular
    .module('ohapp')
    .controller('assessMarkCtrl', function assessMarkCtrl($scope, $injector, $rootScope, $stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
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

        $scope.r_role = ['', '其他', '药师', '医生', '护士', '学生']
        $http
            .post('/account.json')
            .success(function (data) {
                if (data.status) {
                    if (data.data.length == 0) {
                        return;
                    }
                    $scope.sign_msg = data.data;
                    $scope.sign = 1;
                }
            });
        $http
            .get('/au/info.json', {params: {id: $stateParams.id}})
            .success(function (data) {
                if (data.status) {
                    $scope.exercises_info = data.data;
                    angular.forEach($scope.exercises_info.test_subject[0].text_msg, function (data, index) {
                        if (data.topic_type == 1) {
                            $scope.dan.push(data);
                            if (data.do_right == 'true') {
                                $scope.dan_actual += Number(data.score);
                            }
                            $scope.dan_score += Number(data.score);
                        } else if (data.topic_type == 2) {
                            $scope.duo.push(data);
                            if (data.do_right == 'true') {
                                $scope.duo_actual += Number(data.score);
                            }
                            $scope.duo_score += Number(data.score);
                        } else if (data.topic_type == 4) {
                            $scope.chu.push(data);
                            $scope.chu_score += Number(data.score);
                            if (data.do_score == undefined) {
                                $scope.not_yue = 1
                            } else {
                                $scope.chu_right_score += Number(data.do_score);
                            }
                        } else if (data.topic_type == 5) {
                            $scope.yong.push(data);
                            $scope.yong_score += Number(data.score);
                            if (data.do_score == undefined) {
                                $scope.not_yue = 1
                            } else {
                                $scope.yong_right_score += Number(data.do_score);
                            }
                        } else if (data.topic_type == 6) {
                            $scope.wen.push(data);
                            $scope.wen_score += Number(data.score);
                            if (data.do_score == undefined) {
                                $scope.not_yue = 1
                            } else {
                                $scope.wen_right_score += Number(data.do_score);
                            }
                        }
                    })
                    if ($scope.not_yue == 1) {
                        $scope.total_score = Number($scope.exercises_info.test_subject[0].score);
                    } else {
                        $scope.total_score = Number($scope.exercises_info.test_subject[0].score) + $scope.chu_right_score + $scope.yong_right_score + $scope.wen_right_score;
                    }
                } else {
                    swal("OMG!", data.message, "error");
                }
            });


        //关闭弹窗
        $scope.close_proup = function () {
            $scope.shose_show = 0;
        }
        //打分逻辑
        $scope.add_fen = function (i, index, ins) {
            $scope.shose_show = 1;
            $scope.add_type = i;
            $scope.index = index;
            $scope.max_score = Number(index.score);
            $scope.ins = ins;
        }
        $scope.ti_score = function (score) {
            angular.forEach($scope.exercises_info.test_subject[0].text_msg, function (data, index) {
                if (data.id == $scope.index.id) {
                    data.do_score = score;
                    return;
                }
            })
            if ($scope.add_type == 4) {
                $scope.chu[$scope.ins].do_score = score;
                $scope.chu_right_score = 0;
                angular.forEach($scope.chu, function (data, index) {
                    $scope.chu_right_score += Number(data.do_score);
                })
            } else if ($scope.add_type == 5) {
                $scope.yong[$scope.ins].do_score = score;
                $scope.yong_right_score = 0;
                angular.forEach($scope.yong, function (data, index) {
                    console.log(data);
                    $scope.yong_right_score += Number(data.do_score);
                })
            } else if ($scope.add_type == 6) {
                $scope.wen[$scope.ins].do_score = score;
                $scope.wen_right_score = 0;
                angular.forEach($scope.wen, function (data, index) {
                    $scope.wen_right_score += Number(data.do_score);
                })
            }
            $scope.total_score = Number($scope.exercises_info.test_subject[0].score) + $scope.chu_right_score + $scope.yong_right_score + $scope.wen_right_score;
            $scope.close_proup();
        }


        //提交打分
        $scope.end_ti = function () {
            $http
                .post('/au/making.json', {
                    id: $stateParams.id,
                    test_subject: $scope.exercises_info.test_subject,
                    four_score: $scope.chu_right_score,
                    five_score: $scope.yong_right_score,
                    six_score: $scope.wen_right_score
                })
                .success(function (data) {
                    if (data.status) {
                        swal("药学工具网提醒您", "您的打分已提交", "success");
                        $scope.close_back();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }


        $scope.close_back = function () {
            history.back();
        }


    });
