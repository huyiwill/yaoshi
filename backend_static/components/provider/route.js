angular
.module( 'ohRoutes', [] )
.provider( '$routes', function $routesProvider()
{
    this.routes = {};

    this.routes['main'] = {
        url: '/',
        abstract: true,
        templateUrl: 'backend_static/views/main.html',
        controller: 'mainCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"mainCtrl",
                     files:["/backend_static/components/controller/main-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.home' ] =
    {
        url: '^/index.html',
        title: '首页',
        templateUrl: 'backend_static/views/home.html',
        controller: 'homeCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"HomeCtrl",
                     files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/home-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'signin' ] =
        {
            url: '/signin.html',
            title: '登录',
            templateUrl: 'backend_static/views/signin.html',
            controller: 'signinCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"signinCtrl",
                        files:["/backend_static/components/controller/signin-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.admin' ] =
        {
            url: '^/admin.html',
            title: '角色管理',
            templateUrl: 'backend_static/views/admin.html',
            controller: 'adminCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"adminCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/admin-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.role' ] =
        {
            url: '^/role.html',
            title: '角色设置',
            templateUrl: 'backend_static/views/role.html',
            controller: 'roleCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"roleCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/role-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.user' ] =
        {
            url: '^/user.html',
            title: '用户设置',
            templateUrl: 'backend_static/views/user.html',
            controller: 'userCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"userCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/user-ctrls.js","/backend_static/components/controller/user/pharmacist-ctrls.js","/backend_static/components/controller/user/doctor-ctrls.js","/backend_static/components/controller/user/other-ctrls.js","/backend_static/components/controller/user/student-ctrls.js","/backend_static/components/controller/user/nurse-ctrls.js"]
                    })
                }]
            }
        };

        this.routes[ 'main.group' ] =
        {
            url: '^/group.html',
            title: '用户分组',
            templateUrl: 'backend_static/views/group.html',
            controller: 'groupCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"groupCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/group-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.category' ] =
        {
            url: '^/category.html',
            title: '药学分类',
            templateUrl: 'backend_static/views/category.html',
            controller: 'categoryCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"categoryCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/category-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.questions' ] =
        {
            url: '^/questions.html',
            title: '用户分组',
            templateUrl: 'backend_static/views/questions.html',
            controller: 'questionsCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"questionsCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/questions-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.subject' ] =
        {
            url: '^/subject.html',
            title: '题目管理',
            templateUrl: 'backend_static/views/subject/subject.html',
            controller: 'subjectCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"subjectCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/subject/subject-ctrls.js","/backend_static/components/controller/subject/chose_cai-ctrls.js","/backend_static/components/controller/subject/chose_dan-ctrls.js","/backend_static/components/controller/subject/chose_shen-ctrls.js","/backend_static/components/controller/subject/chose_tian-ctrls.js",
 "/backend_static/components/controller/subject/chose_two-ctrls.js",
 "/backend_static/components/controller/subject/chose_wen-ctrls.js",
 "/backend_static/components/controller/subject/chose_yong-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.add_dan' ] =
        {
            url: '^/add_dan.html?id',
            title: '题目管理',
            templateUrl: 'backend_static/views/subject/add_dan.html',
            controller: 'add_danCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"add_danCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/subject/add_dan-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.update_dan' ] =
        {
            url: '^/update_dan.html?id,chose_type',
            title: '题目管理',
            templateUrl: 'backend_static/views/subject/update_dan.html',
            controller: 'update_danCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"update_danCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/subject/update_dan-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.tg' ] =
        {
            url: '^/tg.html',
            title: '试卷分类',
            templateUrl: 'backend_static/views/tg.html',
            controller: 'tgCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"tgCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/tg-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.test' ] =
        {
            url: '^/test.html',
            title: '试卷列表',
            templateUrl: 'backend_static/views/test.html',
            controller: 'testCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"testCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/test-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.addTest' ] =
        {
            url: '^/addTest.html?id',
            title: '新增试卷',
            templateUrl: 'backend_static/views/addTest.html',
            controller: 'addTestCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"addTestCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/addTest-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.test_common' ] =
        {
            url: '^/test_common.html',
            title: '试卷列表',
            templateUrl: 'backend_static/views/test_common.html',
            controller: 'test_commonCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"test_commonCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/test_common-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.testList' ] =
        {
            url: '^/testList.html?id,name',
            title: '试卷详情',
            templateUrl: 'backend_static/views/testList.html',
            controller: 'testListCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"testListCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/testList-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.testYear' ] =
        {
            url: '^/test_year.html?id,name',
            title: '试卷详情',
            templateUrl: 'backend_static/views/test_year.html',
            controller: 'test_yearCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"test_yearCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/test_year-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.addTestList' ] =
        {
            url: '^/addTestList.html?id,chose_type',
            title: '新增试题',
            templateUrl: 'backend_static/views/addTestList.html',
            controller: 'addTestListCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"addTestListCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/addTestList-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.addTset_year' ] =
        {
            url: '^/addTset_year.html?id,chose_type',
            title: '新增试题',
            templateUrl: 'backend_static/views/addTset_year.html',
            controller: 'addTset_yearCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"addTset_yearCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/addTset_year-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.addTestChose' ] =
        {
            url: '^/addTestChose.html?id,chose_type',
            title: '试题检索',
            templateUrl: 'backend_static/views/addTestChose.html',
            controller: 'addTestChoseCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"addTestChoseCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/addTestChose-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.addYearChose' ] =
        {
            url: '^/addYearChose.html?id,chose_type',
            title: '历年真题检索',
            templateUrl: 'backend_static/views/addYearChose.html',
            controller: 'addYearChoseCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"addYearChoseCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/addYearChose-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.hospital' ] =
        {
            url: '^/hospital.html',
            title: '医院管理',
            templateUrl: 'backend_static/views/hospital.html',
            controller: 'hospitalCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"hospitalCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/hospital-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.company' ] =
        {
            url: '^/company.html',
            title: '单位管理',
            templateUrl: 'backend_static/views/company.html',
            controller: 'companyCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"companyCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/company-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.department' ] =
        {
            url: '^/department.html',
            title: '科室管理',
            templateUrl: 'backend_static/views/department.html',
            controller: 'departmentCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"departmentCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/department-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.major' ] =
        {
            url: '^/major.html',
            title: '科室管理',
            templateUrl: 'backend_static/views/major.html',
            controller: 'majorCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"majorCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/major-ctrls.js"]
                    })
                }]
            }
        };

    this.routes[ 'main.assessment_list' ] =
        {
            url: '^/assessment_list.html',
            title: '考核列表',
            templateUrl: 'backend_static/views/assessment_list.html',
            controller: 'assessment_listCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"assessment_listCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/assessment_list-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.examination_management' ] =
        {
            url: '^/examination_management.html',
            title: '考卷管理',
            templateUrl: 'backend_static/views/examination_management.html',
            controller: 'examination_managementCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examination_managementCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/examination_management-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.achievement_management' ] =
        {
            url: '^/achievement_management.html',
            title: '成绩管理',
            templateUrl: 'backend_static/views/achievement_management.html',
            controller: 'achievement_managementCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"achievement_managementCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/achievement_management-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.meeting' ] =
        {
            url: '^/meeting.html',
            title: '会议管理',
            templateUrl: 'backend_static/views/meeting.html',
            controller: 'meetingCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meetingCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/meeting-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.meeting_add' ] =
        {
            url: '^/meetingAdd.html',
            title: '会议新增',
            templateUrl: 'backend_static/views/meetingAdd.html',
            controller: 'meeting_addCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meeting_addCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/meeting_add-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.meeting_more' ] =
        {
            url: '^/meetingMore.html?id',
            title: '会议新增-更多',
            templateUrl: 'backend_static/views/meeting_more.html',
            controller: 'meeting_moreCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meeting_moreCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/meeting_more-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.meeting_update' ] =
        {
            url: '^/meetingUpdate.html?id',
            title: '会议新增-更多',
            templateUrl: 'backend_static/views/meeting_update.html',
            controller: 'meeting_updateCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meeting_updateCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/meeting_update-ctrls.js"]
                    })
                }]
            }
        };
    //运营管理
    this.routes[ 'main.online_list' ] =
        {
            url: '^/onlineList.html',
            title: '',
            templateUrl: 'backend_static/views/operation/online_list.html',
            controller: 'online_listCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"online_listCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/operation/online_list-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.online_info' ] =
        {
            url: '^/onlineInfo.html?id',
            title: '线上实战-更多',
            templateUrl: 'backend_static/views/operation/online_info.html',
            controller: 'online_infoCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"online_infoCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/operation/online_info-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.membership' ] =
        {
            url: '^/membership.html',
            title: '会员卡类型管理',
            templateUrl: 'backend_static/views/operation/membership.html',
            controller: 'membershipCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"membershipCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/operation/membership-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.member' ] =
        {
            url: '^/member.html',
            title: '会员卡管理',
            templateUrl: 'backend_static/views/operation/member.html',
            controller: 'memberCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"memberCtrl",
                        files:["/backend_static/components/controller/main-ctrls.js","/backend_static/components/controller/operation/member-ctrls.js"]
                    })
                }]
            }
        };



    this.routes[ '404' ] =
        {
            url: '/404.html',
            title: '404',
            templateUrl: 'backend_static/views/404.html',
        };

    this.$get = function()
    {
        return( this.routes );
    };
}
);
