angular
    .module( 'ohapp' )
    .controller( 'meetMoreCtrl', function meetMoreCtrl( $scope, $injector, $rootScope,$stateParams) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');

        $scope.submitted = false;
        $scope.updates = {};
        $scope.updates.state1 = 1;
        $scope.updates.state2 = 1;
        $scope.updates.state3 = 1;
        $scope.updates.state4 = 1;

        $scope.updates.sort1 = 1;
        $scope.updates.sort2 = 2;
        $scope.updates.sort3 = 3;
        $scope.updates.sort4 = 4;

        $scope.chose_type = 1;


        $scope.id_an = true;
        $scope.chose_this = function (i) {
            if($scope.chose_type == i){
                return;
            }else{
                $scope.chose_type = i;
                $('#analysis1').find($(".froala-element")).eq(0)[0].innerHTML='<p><br></p>';
                $('#analysis2').find($(".froala-element")).eq(0)[0].innerHTML='<p><br></p>';
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
            if (form.$valid&&!$scope.id_an) {
                $http
                    .post('/meeting/detail/add.json', {
                        meeting_id:$stateParams.id,
                        content:$("#analysis1 .froala-element").eq(0)[0].innerHTML,
                        sort:$scope.updates.sort1,
                        type:1,
                        state:$scope.updates.state1
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "编辑成功！", "success");
                            $scope.chose_this(2);
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }

        //日程管理
        var days_number = 0;
        $scope.day_list = [];
        $scope.add_day = function () {
            console.log(111)
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
            $scope.day_list.push({days:days_number,photo:data})
            $scope.$apply();
        }
        $scope.next2 = function (form) {
            if (form.$valid) {
                $http
                    .post('/meeting/detail/add.json', {
                        meeting_id:$stateParams.id,
                        schedule:$scope.day_list,
                        sort:$scope.updates.sort2,
                        type:2,
                        state:$scope.updates.state2
                    })
                    .success(function (data) {
                        if (data.status) {
                                swal("干得漂亮！", "编辑成功！", "success");
                            $scope.chose_this(3)
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
            $scope.images_list_img=data;
            $scope.$apply();
        }

        //确定添加
        $scope.add_end_per = function () {
            $scope.images_list.push({head:$scope.images_list_img,name:$scope.per_name,remark:$("#analysis2 .froala-element").eq(0)[0].innerHTML});
            console.log($scope.images_list);
            $scope.images_list_img = undefined;
            $scope.name = undefined;
            $('#analysis2').find($(".froala-element")).eq(0)[0].innerHTML='<p><br></p>';
            $scope.add_pers = 0;
        }
        $scope.next3 = function (form) {
            if (form.$valid) {
                $http
                    .post('/meeting/detail/add.json', {
                        meeting_id:$stateParams.id,
                        guest:$scope.images_list,
                        sort:$scope.updates.sort3,
                        type:3,
                        state:$scope.updates.state3
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "编辑成功！", "success");
                            $scope.chose_this(4);
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
            $scope.invitation=data;
            $scope.$apply();
        }
        $scope.next4 = function (form) {
            if (form.$valid) {
                $http
                    .post('/meeting/detail/add.json', {
                        meeting_id:$stateParams.id,
                        photo:$scope.invitation,
                        sort:$scope.updates.sort4,
                        type:4,
                        state:$scope.updates.state4
                    })
                    .success(function (data) {
                        if (data.status) {
                            swal("干得漂亮！", "编辑成功！", "success");
                            $scope.back();
                        } else {
                            swal("OMG!", data.message, "error");
                        }
                    });
            } else {
                $scope.submitted = true;
            }
        }


        // $scope.next = function (form) {
        //     if (form.$valid) {
        //         if($scope.updates.is_credit==2){
        //             $scope.updates.credis = 0;
        //         }
        //         $http
        //             .post('/meeting/add.json', {
        //                 name: $scope.updates.name,
        //                 name_english: $scope.updates.name_english,
        //                 contacts: $scope.updates.contacts,
        //                 mobile: $scope.updates.mobile,
        //                 banner: $scope.updates.banner,
        //                 icon: $scope.updates.icon,
        //                 enroll_start: $scope.updates.enroll_start,
        //                 enroll_end: $scope.updates.enroll_end,
        //                 time_start: $scope.updates.time_start,
        //                 time_end: $scope.updates.time_end,
        //                 attend_time: $scope.updates.attend_time,
        //                 address: $scope.updates.address,
        //                 venue_name: $scope.updates.venue_name,
        //                 is_credit: $scope.updates.is_credit,
        //                 credis: $scope.updates.credis,
        //                 type: $scope.updates.type,
        //                 subject: $scope.updates.subject
        //             })
        //             .success(function (data) {
        //                 if (data.status) {
        //                     swal("干得漂亮！", "新增成功！", "success");
        //                     $state.go('main.meeting_more',{id:data.data})
        //                 } else {
        //                     swal("OMG!", data.message, "error");
        //                 }
        //             });
        //     } else {
        //         $scope.submitted = true;
        //     }
        // }

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
        $scope.sendFile = function(){
            var url = "/meeting/data.json",
                file = $scope.fileToUpload;
            if ( !file ) return;
            fileUpload.uploadFileToUrl( file, url );
        }









    });
