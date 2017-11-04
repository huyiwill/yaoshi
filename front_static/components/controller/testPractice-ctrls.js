angular
    .module('ohapp')
    .controller('testPracticeCtrl', function testPracticeCtrl($scope, $injector, $rootScope, $stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');

        //获取用户的基本信息
        $http
            .post('/account.json')
            .success(function (data) {
                if (data.status) {
                    if (data.data.length == 0) {
                        $state.go("main.logo", {"signin": 1});
                    }
                    $scope.id = data.data.id;
                } else {
                    $state.go("main.logo", {"signin": 1});
                }
            });
        //初始化变量
        $scope.answer_right = 0;
        $scope.answer_error = 0;
        $scope.is_dan = 1;
        $scope.automatic = 0;


        //获取session信息，判断是否有历史记录。。
        $scope.isHave = false;
        for (var i in $session.get("userMsg" + $stateParams.id)) {
            $scope.isHave = true;
        }
        if ($scope.isHave) {
            $scope.hosity_popup = 1;
            $scope.userMsg = $session.get("userMsg" + $stateParams.id);
        }
        //获取题目
        $http
            .post('/subject/special.json', {pharmacy_id: $stateParams.id})
            .success(function (data) {
                if (data.status) {
                    $scope.exercise_all_one = data.data.one_result;
                    $scope.exercise_all_two = data.data.two_result;
                    $scope.one_count = data.data.one_count;
                    $scope.two_count = data.data.two_count;
                    $scope.get_exercise(0);

                    $scope.all_ti = $scope.one_count;
                    $scope.correct_rate();
                } else {
                    swal("OMG!", data.message, "error");
                }
            });


        //选择相应的题目
        $scope.get_exercise = function (num) {
            if ($scope.is_dan) {
                $scope.exercise_all = $scope.exercise_all_one;
                $scope.all_ti = $scope.one_count;
                $scope.exercise_length = Number($scope.one_count);
                $scope.ex_length = [];
                for (var i = 0; i < $scope.exercise_length; i++) {
                    $scope.ex_length.push({index: (i + 1)})
                }
            } else {
                $scope.exercise_all = $scope.exercise_all_two;
                $scope.all_ti = $scope.two_count;
                $scope.exercise_length = Number($scope.two_count);
                $scope.ex_length = [];
                for (var i = 0; i < $scope.exercise_length; i++) {
                    $scope.ex_length.push({index: (i + 1)})
                }
            }
            angular.forEach($scope.exercise_all, function (data, index) {
                if (index == num) {
                    $scope.see_anw = 0;
                    $scope.right_anw = '';
                    $scope.your_anw = '';
                    $scope.analy = 0;
                    $scope.chose_duo = [];

                    $scope.click_this = undefined;
                    $scope.exercise_text = data;
                    $scope.ex_num = num + 1;
                    $scope.is_collection();
                    return;
                }
            });
        }

        //判断是否收藏
        $scope.is_collection = function () {
            $http
                .post('/purpose/check/one.json', {subject_id: $scope.exercise_text.id})
                .success(function (data) {
                    if (data.status) {
                        $scope.collection_add = 1;
                    } else {
                        $scope.collection_add = 0;
                    }
                });
        }
//点击收藏
        $scope.collection = function () {
            $http
                .post('/purpose/one.json', {subject_id: $scope.exercise_text.id})
                .success(function (data) {
                    if (data.status) {
                        swal({
                            title: "药学工具网提醒您",
                            text: "收藏成功",
                            timer: 2000,
                            allowOutsideClick:true
                        });
                        $scope.collection_add = 1;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
//取消收藏
        $scope.not_collection = function () {
            $http
                .post('/purpose/cancel/one.json', {subject_id: $scope.exercise_text.id})
                .success(function (data) {
                    if (data.status) {
                        swal({
                            title: "药学工具网提醒您",
                            text: "取消收藏成功",
                            timer: 2000,
                            allowOutsideClick:true
                        });
                        $scope.collection_add = 0;
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }

//点击进入下一题/切换题目
        $scope.next_exercise = function (e) {
            $scope.get_exercise(e);
        }

//查看解析
        $scope.analysis = function () {

            // //是否购买
            // $http
            //     .post('todo', {subject_id: $scope.exercise_text.id})
            //     .success(function (data) {
            //         if (data.status) {
            //             console.log(data);
            //         } else {
            //             swal("OMG!", data.message, "error");
            //         }
            //     });

            $scope.agree = 1;
            //同意查看解析，并且积分余额充足
            if ($scope.agree) {
                $http
                    .post('/purpose/check/three.json', {subject_id: $scope.exercise_text.id})
                    .success(function (data) {
                        if (data.status) {
                            $scope.analy = data.data.analysis.analysis;
                        } else {
                            swal({
                                title: "药学工具网提醒您",
                                text: data.message,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "确定购买！",
                                cancelButtonText: "取消购买！",
                                closeOnConfirm: false,
                                closeOnCancel: false
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    $state.go('main.me.my_points');
                                    swal.close();
                                } else {
                                    swal.close();
                                }
                            });
                        }
                    });
            }
        }

//纠错
        $scope.correction = function () {
            if ($("#text_error").val() == '') {
                swal({
                    title: "药学工具网提醒您",
                    text: "请先填写纠错信息",
                    timer: 2000,
                    allowOutsideClick:true
                });
                return;
            }
            $http
                .post('/subject/error.json', {subject_id: $scope.exercise_text.id, error: $("#text_error").val()})
                .success(function (data) {
                    $scope.correction_list = 0;
                    if (data.status) {
                        swal({
                            title: "药学工具网提醒您",
                            text: "您的纠错已提交",
                            timer: 2000,
                            allowOutsideClick:true
                        });
                    } else {
                        swal("OMG!", data.message, "error");
                    }
                });
        }
        //关闭纠错弹框
        $scope.correction_list_close = function () {
            $scope.correction_list = 0;
        }

//切换单选、多选
        $scope.chose_type = function (type) {
            if ($scope.is_dan != type) {
                $scope.is_dan = type;
                $scope.get_exercise(0);
                $scope.old_id = '';
                $scope.automatic = 0;
                $scope.answer_right = 0;
                $scope.answer_error = 0;
                $scope.correct_rate();

            }
        }
//计算正确率
        $scope.correct_rate = function () {
            $scope.correct = Math.round(($scope.all_ti - $scope.answer_error) / $scope.all_ti * 10000) / 100 + "%";
        }
//判断题目的正确性
        $scope.click_end = function (key, id, s) {
            if ($scope.old_id == id) {
                swal({
                    title: "药学工具网提醒您",
                    text: "不能重复提交答案",
                    timer: 2000,
                    allowOutsideClick:true
                });
            } else {
                $scope.click_this = key;
                $scope.see_anw = 1;
                $scope.right_anw = $scope.exercise_text.right_key;
                $scope.your_anw = key;

                //判断题目是否正确
                if ($scope.exercise_text.right_key == key) {
                    $scope.old_id = id;
                    $scope.answer_right = $scope.answer_right + 1;
                    $scope.correct_rate();

                    // //判断是是否为自动下一题
                    // if ($scope.automatic) {
                    //     $scope.get_exercise(s);
                    // }
                } else {
                    $scope.old_id = id;
                    $scope.answer_error = $scope.answer_error + 1;
                    $scope.correct_rate();
                    //提交错题
                    $http
                        .post('/purpose/two.json', {subject_id: $scope.exercise_text.id})
                        .success(function (data) {

                        });
                }

                //判断用户是否做完
                if ($scope.ex_num == $scope.exercise_all.length) {
                    swal("药学工具网提醒您", "您已经做完了", "success");
                    $session.purge('userMsg');
                } else {
                    //记录用户的答题相关信息
                    $session.set('userMsg' + $stateParams.id, {
                        chose_dan: $scope.is_dan,
                        num: Number($scope.ex_num) + 1,
                        answer_right: $scope.answer_right,
                        answer_error: $scope.answer_error,
                        correct: $scope.correct
                    });
                    $session.save();
                }
                //判断是是否为自动下一题
                if ($scope.automatic&&$scope.exercise_text.right_key == key) {
                    $scope.get_exercise(s);
                }
            }
        }

//判断多选题目的正确性
        $scope.click_end_more = function (id, s) {
            $scope.click_end($scope.chose_duo, id, s);
        }

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
        $scope.chose_duo = [];
        $scope.click_chose = function ($event, key, num) {
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
        }

        //切换题目
        $scope.change_exs = function () {
            $scope.get_exercise($scope.ex_chose_this_item - 1);
            $scope.change_exs_list = 0;
            $scope.ex_chose_this_item = undefined;
        }
        //取消切换
        $scope.close_change_exs_list = function () {
            $scope.change_exs_list = 0;
        }

        //选择题目
        $scope.ex_chose_this = function (i) {
            $scope.ex_chose_this_item = i;

            $scope.answer_right = 0;
            $scope.answer_error = 0;
            $scope.old_id = undefined;
            $scope.correct_rate();
        }

        //用户信息模块
        $scope.chose_over = function () {
            $scope.hosity_popup = 0;
            $scope.get_exercise(0);
            $session.purge('userMsg' + $stateParams.id);
        }
        $scope.chose_again = function () {
            $scope.is_dan = $scope.userMsg.chose_dan;
            $scope.hosity_popup = 0;
            $scope.answer_right = $scope.userMsg.answer_right;
            $scope.answer_error = $scope.userMsg.answer_error;
            $scope.correct = $scope.userMsg.correct;
            $scope.get_exercise($scope.userMsg.num - 1);
        }
    })
;
