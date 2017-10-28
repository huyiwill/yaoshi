angular
    .module('ohapp')
    .controller('assessStartCtrl', function assessStartCtrl($scope, $injector, $rootScope, $stateParams, $interval) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $scope.datas_id = $stateParams.id;
        $scope.is_not = 0;
        //路由跳转提示消息
        $scope.$on('$stateChangeStart',
            function (evt, toState, toParams, fromState, fromParams) {
                // 如果需要阻止事件的完成
                if (!$scope.is_not) {
                    $interval.cancel($scope.timmer);
                    $interval.cancel($scope.timmers);
                    return;
                }
                if (!confirm("您正在考核中，离开后将不能再次答题！确定离开此页面吗？")) {
                    evt.preventDefault();
                } else {
                    $interval.cancel($scope.timmer);
                }
            });
        //浏览器关闭页面提示消息
        window.onbeforeunload = function (event) {
            if (!$scope.is_not) {
                return;
            }
            return confirm("您正在考核中，离开后将不能再次答题！确定离开此页面吗？")
        }

        $scope.role_arr = ['未知', '药师', '医生', '护士', '学生']
        $scope.ex_name = $stateParams.name;

        $http
            .post('/account.json')
            .success(function (data) {
                if (data.status) {
                    if (data.data.length == 0) {
                        return;
                    }
                    $scope.sign_msg = data.data;
                } else {
                    swal("OMG!", data.message, "error");
                }
            });
        $scope.times = 30;
        $scope.timmers = $interval(toDos, 1000);
        function toDos() {
            $scope.times = $scope.times - 1;
            if ($scope.times == 0) {
                $scope.chose_in();
            }
        }

        //进入考场
        $scope.chose_in = function () {
            $interval.cancel($scope.timmers);
            $scope.do();
        }

        //获取试卷
        $scope.do = function () {
            $scope.do = 1;
            $http
                .post('/front/assess/start.json', {id: $stateParams.id, assess_id: $stateParams.assess_id})
                .success(function (data) {
                    if (data.status) {
                        $scope.exam_msg = data.data;
                        $scope.is_not = 1;
                        $scope.again();
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

        //数据解析区域
        $scope.again = function () {
            $scope.is_not = 1;

            $scope.achievement = 0;
            $scope.end_score = 0;
            $scope.use_time = 0;
            $scope.exam = [];
            for (var i = 0; i < $scope.exam_msg.subject_one.length; i++) {
                $scope.exam_msg.subject_one[i].do = '';
                $scope.exam_msg.subject_one[i].topic_type = 1;
                $scope.exam_msg.subject_one[i].total = $scope.exam_msg.subject_one.length;
                $scope.exam_msg.subject_one[i].score = $scope.exam_msg.total_one;
                $scope.exam.push($scope.exam_msg.subject_one[i]);
            }
            for (var i = 0; i < $scope.exam_msg.subject_two.length; i++) {
                $scope.exam_msg.subject_two[i].do = '';
                $scope.exam_msg.subject_two[i].topic_type = 2;
                $scope.exam_msg.subject_two[i].total = $scope.exam_msg.subject_two.length;
                $scope.exam_msg.subject_two[i].score = $scope.exam_msg.total_two;
                $scope.exam.push($scope.exam_msg.subject_two[i]);
            }
            for (var i = 0; i < $scope.exam_msg.subject_four.length; i++) {
                $scope.exam_msg.subject_four[i].do = '';
                $scope.exam_msg.subject_four[i].topic_type = 4;
                $scope.exam_msg.subject_four[i].total = $scope.exam_msg.subject_four.length;
                $scope.exam_msg.subject_four[i].score = $scope.exam_msg.total_four;
                $scope.exam.push($scope.exam_msg.subject_four[i]);
            }
            for (var i = 0; i < $scope.exam_msg.subject_five.length; i++) {
                $scope.exam_msg.subject_five[i].do = '';
                $scope.exam_msg.subject_five[i].topic_type = 5;
                $scope.exam_msg.subject_five[i].total = $scope.exam_msg.subject_five.length;
                $scope.exam_msg.subject_five[i].score = $scope.exam_msg.total_five;
                $scope.exam.push($scope.exam_msg.subject_five[i]);
            }
            for (var i = 0; i < $scope.exam_msg.subject_six.length; i++) {
                $scope.exam_msg.subject_six[i].do = '';
                $scope.exam_msg.subject_six[i].topic_type = 6;
                $scope.exam_msg.subject_six[i].total = $scope.exam_msg.subject_six.length;
                $scope.exam_msg.subject_six[i].score = $scope.exam_msg.total_six;
                $scope.exam.push($scope.exam_msg.subject_six[i]);
            }

            //倒计时
            $scope.time = $scope.exam_msg.assess_info.time * 60;
            $scope.show_time = $scope.exam_msg.assess_info.time + "分"
            $scope.timmer = $interval(toDo, 1000);
            function toDo() {
                $scope.time = $scope.time - 1;
                $scope.show_time = Math.floor($scope.time / 60) + "分" + ($scope.time % 60) + "秒";
                if ($scope.time == 0) {
                    $interval.cancel($scope.timmer);
                    swal({
                        title: "时间到了",
                        text: "您不能再答题了！",
                        type: "warning",
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定交卷！",
                        closeOnConfirm: false
                    }, function () {
                        swal.close();
                        $scope.ass_end();
                    });
                }
            }

            $scope.lead(0)
        }


        $scope.lead = function (i) {

            //初始化数据
            $scope.click_this = '';
            $scope.chose_duo = [];
            $scope.now_number = i;
            $scope.exam_now = $scope.exam[i];
            $scope.fen();
        }


        //点击单选题
        $scope.click_end = function (key, index) {
            $scope.click_this = key;
            $scope.exam[$scope.now_number].do = key;
        }
        //点击多选题
        //多选获取
        //封装数去remove方法
        Array.prototype.indexOf = function (val) {
            for (var i = 0; i < this.length; i++) {
                if (this[i] == val) {
                    return i;
                }
            }
            return -1;
        }
        Array.prototype.remove = function (val) {
            var index = this.indexOf(val);
            if (index > -1) {
                this.splice(index, 1);
            }
        }
        $scope.click_chose = function ($event, key) {
            if ($event.delegateTarget.classList.length == 5) {
                $scope.chose_duo.push(key);
            } else {
                $scope.chose_duo.remove(key);
            }
            function mySorter(a, b) {
                if (/^\d/.test(a) ^ /^\D/.test(b)) return a > b ? 1 : (a == b ? 0 : -1);
                return a > b ? -1 : (a == b ? 0 : 1);
            }

            $scope.chose_duo = $scope.chose_duo.sort(mySorter);
            var j = 0;
            $scope.click_this = '';
            for (var i = 0; i < $scope.chose_duo.length; i++) {
                if (j == 0) {
                    $scope.click_this = $scope.chose_duo[i];
                } else {
                    $scope.click_this = $scope.click_this + "," + $scope.chose_duo[i];
                }
                j++;
            }
            $scope.exam[$scope.now_number].do = $scope.click_this;
        }


        //切换题目
        $scope.next = function (i) {
            $scope.lead(i);
        }

        //点击提交按钮
        $scope.assignment = function () {
            var isNot = false;
            angular.forEach($scope.exam, function (data, index) {
                if (data.do == '') {
                    isNot = true;
                    return false;
                }
            });
            if (isNot) {
                swal({
                    title: "确定交卷吗？",
                    text: "系统检测您答完题目！",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确定交卷！",
                    closeOnConfirm: false
                }, function () {
                    swal.close();
                    $scope.ass_end();
                });
            } else {
                $scope.ass_end();
            }
        }

        //提交试卷中。。。
        $scope.ass_end = function () {
            // //计算相关参数
            // $scope.end_score = 0;
            // angular.forEach($scope.exam,function(data,index){
            //     if(data.type==1||data.type==2){
            //         if(data.right_key==data.do){
            //             $scope.end_score += Number(data.score);
            //             data.do_right = true;
            //         }else{
            //             data.do_right = false;
            //         }
            //     }
            // })
            $scope.use_time = Math.ceil($scope.exam_msg.assess_info.time - $scope.time / 60);
            $scope.test_subject = [{
                info: $scope.exam_msg.test_info,
                text_msg: $scope.exam,
                count: $scope.exam_msg.count
            }]
            $http
                .post('/front/assess/end.json', {
                    id: $stateParams.id,
                    assess_id: $stateParams.assess_id,
                    test_subject: $scope.test_subject,
                    answer_time: $scope.use_time,
                    complete_status: 1
                })
                .success(function (data) {
                    $scope.correction_list = 0;
                    if (data.status) {
                        $scope.data_id = data.data;
                        $scope.achievement = 1;
                        $scope.is_not = 0;

                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        $scope.again_now = function () {
            $scope.again();
        }

        //答题分析
        $scope.fen = function () {
            console.log($scope.exam)
            $scope.use_time = Math.ceil($scope.exam_msg.assess_info.time - $scope.time / 60);
            $scope.test_subject = [{
                info: $scope.exam_msg.test_info,
                text_msg: $scope.exam,
                count: $scope.exam_msg.count
            }]
            $http
                .post('/front/assess/end.json', {
                    id: $stateParams.id,
                    assess_id: $stateParams.assess_id,
                    test_subject: $scope.test_subject,
                    answer_time: $scope.use_time
                })
                .success(function (data) {
                });
        }


    });

