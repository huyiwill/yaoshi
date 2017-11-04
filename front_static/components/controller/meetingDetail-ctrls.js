angular
    .module( 'ohapp' )
    .controller( 'meetingDetailCtrl', function meetingDetailCtrl( $scope, $injector, $rootScope,$stateParams) {
        var $http = $injector.get( '$http' );
        var $location = $injector.get('$location');
        var $state = $injector.get( '$state' );
        var $timeout = $injector.get( '$timeout' );
        var $config = $injector.get( '$config' );
        var $session = $injector.get('$session');
        $scope.data_type=['','会议介绍','会议日程','会议嘉宾','会议邀请函'];
        $scope.chose_type = 1;
        $scope.chose_this = function (i) {
            if(i==$scope.chose_type){return}else{
                $scope.chose_type = i;
            }
            if($scope.chose_type == 2){
                $scope.lead_two();
            }
            if($scope.chose_type == 3){
                $scope.lead_three();
            }
        }

        //获取会议信息
        $scope.lead = function(){
            $http
                .get('/meeting/front/info.json',{params:{id:$stateParams.id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.meeting_msg = data.data;
                        $scope.meeting_info();
                    } else {
                        swal("OMG!", data.message, "error");
                    }

                });
        }
        $scope.lead();


        $scope.meeting_info = function(){
            angular.forEach($scope.meeting_msg.detail_info,function (data) {
                data.type_name = $scope.data_type[data.type];
            })
        }



        $scope.lead_two = function(){
            $http
                .get('/meeting/front/detail.json',{params:{meeting_id:$stateParams.id,type:2,id:$scope.meeting_msg.detail_info[1].id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.meeting_two = data.data;
                    } else {
                        swal("OMG!", data.message, "error");
                    }

                });
        }
        $scope.lead_three = function(){
            $http
                .get('/meeting/front/detail.json',{params:{meeting_id:$stateParams.id,type:3,id:$scope.meeting_msg.detail_info[2].id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.meeting_three = data.data;
                    } else {
                        swal("OMG!", data.message, "error");
                    }

                });
        }

        //查看嘉宾详情
        $scope.see_detail = function(i){
            $scope.meeting_ten={};
            $scope.chose_type = 10;
            // $scope.shose_show = 1;
            $http
                .get('/meeting/front/guest.json',{params:{id:i,meeting_id:$stateParams.id}})
                .success(function (data) {
                    if (data.status) {
                        $scope.meeting_ten = data.data;
                    } else {
                        swal("OMG!", data.message, "error");
                    }

                });
        }

        //关闭弹窗
        $scope.close_proup = function () {
            $scope.shose_show = 0;
        }


        //报名
        $scope.baoming = function(){
            $http
                .post('/meeting/front/enroll.json',{meeting_id:$stateParams.id})
                .success(function (data) {
                    if (data.status) {
                        swal("药学工具网提醒您", "报名成功", "success");
                    } else {
                        swal("OMG!", data.message, "error");
                    }

                });
        }
        $scope.back_three = function(){
            $scope.chose_type = 3;
        }










    });
