angular
    .module('ohapp')
    .controller('meetingCtrl', function meetingCtrl($scope, $injector, $rootScope) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');


        $scope.lead = function (current, p, time) {

            $http
                .get('/meeting/front/list.json', {
                    params: {
                        page: current,
                        province_id: $scope.province,
                        start: $scope.times_start,
                        end: $scope.times_end
                    }
                })
                .success(function (data) {
                    if (data.status) {
                        $scope.meeting = data.data;
                        $scope.total_page = Number(data.total)
                        $(".tcdPageCode").createPage({
                            pageCount: Number(data.total),
                            current: Number(data.current),
                            backFn: function (current) {
                                next(current)
                            }
                        });
                    } else {
                        swal("OMG!", data.message, "error");
                    }

                });
        }
        $scope.lead();

        //翻页
        function next(p) {
            $('#page').val(p);
            $scope.lead(p);
        };

        //获取省份信息
        $http
            .get('/region/province/drop.json')
            .success(function (data) {
                if (data.status) {
                    $scope.province_list = data.data;
                } else {
                    swal("OMG!", data.message, "error");
                }

            });


        $scope.chose_time = function (day) {
            $scope.c_times = day;
            if(day==undefined){
                $scope.times_start = undefined;
                $scope.times_end = undefined;
            }else if (day == 0 || day == 1) {
                var d = new Date();
                d.setDate(d.getDate() + day);
                var m = d.getMonth() + 1;
                $scope.times_start = d.getFullYear() + '-' + m + '-' + d.getDate();
                $scope.times_end = d.getFullYear() + '-' + m + '-' + d.getDate();
            } else if (day == 7) {
                var d = new Date();
                d.setDate(d.getDate() + day);
                var m = d.getMonth() + 1;
                $scope.times_end = d.getFullYear() + '-' + m + '-' + d.getDate();
                var c = new Date();
                var n = c.getMonth() + 1;
                $scope.times_start = c.getFullYear() + '-' + m + '-' + c.getDate();
            } else if (day = 30) {
                var d = new Date();
                var m = d.getMonth() + 1;
                $scope.times_start = d.getFullYear() + '-' + m + '-' + '01';
                $scope.times_end = d.getFullYear() + '-' + m + '-' + '31';
            }
            $('.times_start').val('');
            $('.times_end').val('');
            $scope.lead();
        }

        $scope.chose_province = function (i) {
            $scope.c_prives = i;
            $scope.province = i;
            $scope.lead();
        }

        $('.times_start').datepicker({
            language: 'zh-CN',
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd",
            clearBtn: true
        }).on('changeDate', function (ev) {
            if (ev.date) {
                var s = new Date(ev.date.valueOf());
                var m = s.getMonth() + 1;
                $scope.set_start = s.getFullYear() + '-' + m + '-' + s.getDate();
            } else {
                // $(endSelector).datepicker('setStartDate',null);
                $scope.set_start = undefined;
            }
        })
        $('.times_end').datepicker({
            language: 'zh-CN',
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd",
            clearBtn: true
        }).on('changeDate', function (ev) {
            if (ev.date) {
                var s = new Date(ev.date.valueOf());
                var m = s.getMonth() + 1;
                $scope.set_end = s.getFullYear() + '-' + m + '-' + s.getDate();
            } else {
                $scope.set_end = undefined;
            }
        })
        $scope.choseTimes = function () {
            $scope.c_times = 15;
            $scope.times_start = $scope.set_start;
            $scope.times_end = $scope.set_end;
            $scope.lead();
        }


    });
