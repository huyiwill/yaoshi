angular
    .module('ohapp')
    .controller('meeting_updateCtrl', function meeting_updateCtrl($scope, $injector, $rootScope, ngDialog, $timeout, $stateParams) {
        var $http = $injector.get('$http');
        var $location = $injector.get('$location');
        var $state = $injector.get('$state');
        var $timeout = $injector.get('$timeout');
        var $config = $injector.get('$config');
        var $session = $injector.get('$session');
        $scope.submitted = false;
        $scope.updates = {};
        $scope.updates.is_credit = 2;
        var days_number;


        $scope.leadMsg = function (s) {
            if (s == 0) {
                $http
                    .get('/meeting/info.json', {params: {id: $stateParams.id}})
                    .success(function (data) {
                        if (data.status) {
                            $scope.updates = data.data;
                            $scope.up_detail_info_1 = $scope.updates.detail_info[0].id;
                            $scope.up_detail_info_2 = $scope.updates.detail_info[1].id;
                            $scope.up_detail_info_3 = $scope.updates.detail_info[2].id;
                            $scope.up_detail_info_4 = $scope.updates.detail_info[3].id;
                            $scope.chose_this_p($scope.updates.province_id);
                            $scope.updates.enroll_start = timeStamp2String($scope.updates.enroll_start*1000);
                            $scope.updates.enroll_end = timeStamp2String($scope.updates.enroll_end*1000);
                            $scope.updates.time_start = timeStamp2String($scope.updates.time_start*1000);
                            $scope.updates.time_end = timeStamp2String($scope.updates.time_end*1000);
                            $scope.updates.attend_time = timeStamp2String($scope.updates.attend_time*1000);
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
                return;
            }else if(s==1){
                $scope.updates_0 = $scope.updates.detail_info[0];
                $('#analysis1').find($(".froala-element")).eq(0)[0].innerHTML = $scope.updates_0.content;
            }else if(s==2){
                $scope.updates_1 = $scope.updates.detail_info[1];
                $http
                    .get('/meeting/detail/info.json', {params: {meeting_id: $stateParams.id,type:2}})
                    .success(function (data) {
                        if (data.status) {
                            $scope.day_list = [];
                            // angular.forEach(data.data,function (data,index) {
                            //     $scope.day_list.push({days: data.days, photo: data.photo})
                            // })
                            // days_number = $scope.day_list.length;
                            days_number = 0;
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });

            }else if(s==3){
                $scope.updates_2 = $scope.updates.detail_info[2];
                $http
                    .get('/meeting/detail/info.json', {params: {meeting_id: $stateParams.id,type:3}})
                    .success(function (data) {
                        if (data.status) {
                            $scope.images_list = [];
                            // angular.forEach(data.data,function (data,index) {
                            //     $scope.images_list.push({
                            //         head: data.head,
                            //         name: data.name,
                            //         remark: data.remark
                            //     });
                            // })

                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });

            }else if(s==4){
                $scope.updates_3 = $scope.updates.detail_info[3];

            }
        }
        function timeStamp2String(time){
            var datetime = new Date();
            datetime.setTime(time);
            var year = datetime.getFullYear();
            var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
            var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
            var hour = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();
            var minute = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
            var second = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();
            return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;
        }

        $scope.chose_this_type = function (i) {
            if ($scope.chose_type == i) {
                return;
            } else {
                $scope.chose_type = i;
                $scope.leadMsg(i);
                $scope.submitted = false;
            }
        }
        $scope.leadMsg(0)


        $scope.next = function (form) {
            if (form.$valid) {
                if ($scope.updates.is_credit == 2) {
                    $scope.updates.credis = 0;
                }
                $http
                    .post('/meeting/update.json', {
                        id: $stateParams.id,
                        name: $scope.updates.name,
                        name_english: $scope.updates.name_english,
                        contacts: $scope.updates.contacts,
                        mobile: $scope.updates.mobile,
                        banner: $scope.updates.banner,
                        icon: $scope.updates.icon,
                        enroll_start: $scope.updates.enroll_start,
                        enroll_end: $scope.updates.enroll_end,
                        time_start: $scope.updates.time_start,
                        time_end: $scope.updates.time_end,
                        attend_time: $scope.updates.attend_time,
                        province_id: $scope.updates.province_id,
                        city_id: $scope.updates.city_id,
                        address: $scope.updates.address,
                        venue_name: $scope.updates.venue_name,
                        is_credit: $scope.updates.is_credit,
                        credis: $scope.updates.credis,
                        type: $scope.updates.type,
                        subject: $scope.updates.subject
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.back();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }
        $timeout(function () {
            $('#enroll_start').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function (data) {
                    $scope.updates.enroll_start = $('#enroll_start').val();
                    $scope.$apply();
                }
            });
            $('#enroll_end').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.enroll_end = $('#enroll_end').val();
                    $scope.$apply();
                }
            });
            $('#attend_time').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.attend_time = $('#attend_time').val();
                    $scope.$apply();
                }
            });
            $('#time_start').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.time_start = $('#time_start').val();
                    $scope.$apply();
                }
            });
            $('#time_end').jHsDate({
                maxDate: '2025-12-31',
                format: 'yyyy-MM-dd hh时mm分',
                callBack: function () {
                    $scope.updates.time_end = $('#time_end').val();
                    $scope.$apply();
                }
            });
        }, 1000)

        //上传图片
        function ajaxupload_banner(data) {
            photoMsg = data;
            $scope.updates.banner = data;
            $scope.$apply();
        }

        function ajaxupload_icon(data) {
            photoMsg = data;
            $scope.updates.icon = data;
            $scope.$apply();
        }

        $scope.banner = function () {
            $('#banner').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_banner(img.src);
                }
            });
        }
        $scope.icon = function () {
            $('#icon').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_icon(img.src);
                }
            });
        }

        //取消
        $scope.back = function () {
            history.back();
        }
        //获取下拉省份信息
        $http
            .get('/region/province/drop.json')
            .success(function (data) {
                $scope.provinces = data.data;
            });

        $scope.chose_this_p = function (i) {
            $http
                .get('/region/city/drop.json', {params: {province_id: i}})
                .success(function (data) {
                    $scope.cities = data.data;
                });
        }


        $scope.chose_type = 0;


        $scope.id_an = true;
        $scope.chose_this = function (i) {
            if ($scope.chose_type == i) {
                return;
            } else {
                $scope.chose_type = i;
                $('#analysis1').find($(".froala-element")).eq(0)[0].innerHTML = '<p><br></p>';
                $('#analysis2').find($(".froala-element")).eq(0)[0].innerHTML = '<p><br></p>';
                // $('#analysis3').find($(".froala-element")).eq(0)[0].innerHTML='<p><br></p>';
            }
        }


        $('div[name="analysis"]').on('editable.contentChanged', function (e, editor) {

            if ($(this).find($(".froala-element")).eq(0)[0].innerHTML != '<p><br></p>') {
                $scope.id_an = false;
            } else {
                $scope.id_an = true;
            }
            $scope.$apply();
        });

        //会议介绍
        $scope.next1 = function (form) {
            console.log(form.$valid)
            if (form.$valid && !$scope.id_an) {
                $http
                    .post('/meeting/detail/update.json', {
                        id:$scope.up_detail_info_1,
                        meeting_id: $stateParams.id,
                        content: $("#analysis1 .froala-element").eq(0)[0].innerHTML,
                        sort: $scope.updates_0.sort,
                        type: 1,
                        state: $scope.updates_0.state
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.back();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }

        //日程管理
        // var days_number = $scope.day_list.length;
        $scope.add_day = function () {
            $('#add_day').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_add_day(img.src);
                }
            });
        }
        //上传图片
        function ajaxupload_add_day(data) {
            photoMsg = data;
            $scope.updates.banner = data;
            days_number++;
            $scope.day_list.push({days: days_number, photo: data})
            $scope.$apply();
        }

        $scope.next2 = function (form) {
            if (form.$valid) {
                $http
                    .post('/meeting/detail/update.json', {
                        id:$scope.up_detail_info_2,
                        meeting_id: $stateParams.id,
                        schedule: $scope.day_list,
                        sort: $scope.updates_1.sort,
                        type: 2,
                        state: $scope.updates_1.state
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.back();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }

        //上传会议嘉宾
        $scope.images_list = [];
        $scope.add_person = function () {
            $('#add_person').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_add_person(img.src);
                }
            });
        }
        //上传图片
        function ajaxupload_add_person(data) {
            photoMsg = data;
            $scope.updates.banner = data;
            $scope.images_list_img = data;
            $scope.$apply();
        }

        //确定添加
        $scope.add_end_per = function () {
            $scope.images_list.push({
                head: $scope.images_list_img,
                name: $scope.per_name,
                remark: $("#analysis2 .froala-element").eq(0)[0].innerHTML
            });
            $scope.images_list_img = undefined;
            $scope.name = undefined;
            $('#analysis2').find($(".froala-element")).eq(0)[0].innerHTML = '<p><br></p>';
            $scope.add_pers = 0;
        }
        $scope.next3 = function (form) {
            if (form.$valid) {
                $http
                    .post('/meeting/detail/update.json', {
                        id:$scope.up_detail_info_3,
                        meeting_id: $stateParams.id,
                        guest: $scope.images_list,
                        sort: $scope.updates_2.sort,
                        type: 3,
                        state: $scope.updates_2.state
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.back();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }

        //添加邀请函
        $scope.add_invitation = function () {
            $('#add_invitation').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_add_invitation(img.src);
                }
            });
        }
        //上传图片
        function ajaxupload_add_invitation(data) {
            photoMsg = data;
            $scope.updates.banner = data;
            $scope.updates_3.photo = data;
            $scope.$apply();
        }

        $scope.next4 = function (form) {
            if (form.$valid) {
                $http
                    .post('/meeting/detail/update.json', {
                        id:$scope.up_detail_info_4,
                        meeting_id: $stateParams.id,
                        photo: $scope.updates_3.photo,
                        sort: $scope.updates_3.sort,
                        type: 4,
                        state: $scope.updates_3.state
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "修改成功！", "success");
                            $scope.back();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }


        //上传图片
        function ajaxupload_banner(data) {
            photoMsg = data;
            $scope.updates.banner = data;
            $scope.$apply();
        }

        function ajaxupload_icon(data) {
            photoMsg = data;
            $scope.updates.icon = data;
            $scope.$apply();
        }

        $scope.banner = function () {
            $('#banner').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_banner(img.src);
                }
            });
        }
        $scope.icon = function () {
            $('#icon').localResizeIMG({
                width: 200,
                height: 200,
                quality: 1,
                success: function (result) {
                    img = new Image;
                    img.src = result.base64;
                    ajaxupload_icon(img.src);
                }
            });
        }

        //取消
        $scope.back = function () {
            history.back();
        }

        //提交表单
        $scope.sendFile = function () {
            var url = "/meeting/data.json",
                file = $scope.fileToUpload;
            if (!file) return;
            fileUpload.uploadFileToUrl(file, url);
        }


    });
