angular
.module( 'ohRoutes', [] )
.provider( '$routes', function $routesProvider()
{
    this.routes = {};

    this.routes['main'] = {
        url: '/',
        abstract: true,
        templateUrl: 'front_static/views/main.html',
        controller: 'mainCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"mainCtrl",
                     files:["/front_static/components/controller/main-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.home' ] =
    {
        url: '^/index.html',
        title: '首页',
        templateUrl: 'front_static/views/home.html',
        controller: 'homeCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"HomeCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/home-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.abouts' ] =
    {
        url: '^/abouts.html',
        title: '关于我们',
        templateUrl: 'front_static/views/abouts.html',
        controller: 'aboutsCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"aboutsCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/abouts-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.logo' ] =
    {
        url: '^/logo.html?signin,vercode',
        title: '登录注册',
        templateUrl: 'front_static/views/logo.html',
        controller: 'logoCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"logoCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/logo-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.question' ] =
    {
        url: '^/question.html',
        title: '常见问题',
        templateUrl: 'front_static/views/question.html',
        controller: 'questionCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"logoCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/question-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.activity' ] =
    {
        url: '^/activity.html',
        title: '常见问题',
        templateUrl: 'front_static/views/activity.html',
        controller: 'activityCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"activityCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/activity-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.member' ] =
    {
        url: '^/member.html',
        title: '常见问题',
        templateUrl: 'front_static/views/member.html',
        controller: 'memberCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"memberCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/member-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.data' ] =
    {
        url: '^/data.html',
        title: '完善资料 ',
        templateUrl: 'front_static/views/data.html',
        controller: 'dataCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"dataCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/data-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.dataYaoshi' ] =
    {
        url: '^/dataYaoshi.html',
        title: '药师 ',
        templateUrl: 'front_static/views/dataYaoshi.html',
        controller: 'dataYaoshiCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"dataYaoshiCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/dataYaoshi-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.dataDoctor' ] =
    {
        url: '^/dataDoctor.html',
        title: '医生 ',
        templateUrl: 'front_static/views/dataDoctor.html',
        controller: 'dataDoctorCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"dataDoctorCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/dataDoctor-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.dataNurse' ] =
    {
        url: '^/dataNurse.html',
        title: '护士 ',
        templateUrl: 'front_static/views/dataNurse.html',
        controller: 'dataNurseCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"dataNurseCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/dataNurse-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.dataStudent' ] =
    {
        url: '^/dataStudent.html',
        title: '护士 ',
        templateUrl: 'front_static/views/dataStudent.html',
        controller: 'dataStudentCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"dataStudentCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/dataStudent-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.dataOther' ] =
    {
        url: '^/dataOther.html',
        title: '其他 ',
        templateUrl: 'front_static/views/dataOther.html',
        controller: 'dataOtherCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"dataOtherCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/dataOther-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.agreement' ] =
    {
        url: '^/agreement.html',
        title: '用户协议 ',
        templateUrl: 'front_static/views/agreement.html',

    };
     this.routes[ 'main.test' ] =
    {
        url: '^/test.html',
        title: '题库 ',
        templateUrl: 'front_static/views/test.html',
        controller: 'testCtrl',
        resolve:{
            loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                return $ocLazyLoad.load({
                    name:"testCtrl",
                    files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/test-ctrls.js"]
                })
            }]
        }

    };
     this.routes[ 'main.testExam' ] =
    {
        url: '^/testExam.html',
        title: '历年真题 ',
        templateUrl: 'front_static/views/testExam.html',
        controller: 'testExamCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"testExamCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/testExam-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.examBuy' ] =
    {
        url: '^/examBuy.html?id,name',
        title: '购买真题 ',
        templateUrl: 'front_static/views/examBuy.html',
        controller: 'examBuyCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"examBuyCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/examBuy-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.examDo' ] =
    {
        url: '^/examDo.html?id',
        title: '开始考试 ',
        templateUrl: 'front_static/views/examDo.html',
        controller: 'examDoCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"examDoCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/examDo-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.examGrade' ] =
    {
        url: '^/examGrade.html?id',
        title: '考试成绩 ',
        templateUrl: 'front_static/views/examGrade.html',
        controller: 'examGradeCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"examGradeCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/examGrade-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.testPractice' ] =
    {
        url: '^/testPractice.html?id',
        title: '专项练习 ',
        templateUrl: 'front_static/views/testPractice.html',
        controller: 'testPracticeCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"testPracticeCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/testPractice-ctrls.js"]
                 })
             }]
         }
    };
    this.routes[ 'main.testErrors' ] =
    {
        url: '^/testErrors.html?id',
        title: '错题练习 ',
        templateUrl: 'front_static/views/testErrors.html',
        controller: 'testErrorsCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"testErrorsCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/testErrors-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.assess' ] =
    {
        url: '^/assess.html',
        title: '考核 ',
        templateUrl: 'front_static/views/assess.html',
        controller: 'assessCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"assessCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/assess-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.assessStart' ] =
    {
        url: '^/he.html?id,name,assess_id',
        title: '考核须知 ',
        templateUrl: 'front_static/views/assessStart.html',
        controller: 'assessStartCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"assessStartCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/assessStart-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.assessExam' ] =
    {
        url: '^/assessExam.html',
        title: '考核 ',
        templateUrl: 'front_static/views/assessExam.html',
        controller: 'assessExamCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"assessExamCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/assessExam-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.assessGrade' ] =
    {
        url: '^/assessGrade.html?id',
        title: '考核成绩 ',
        templateUrl: 'front_static/views/assessGrade.html',
        controller: 'assessGradeCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"assessGradeCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/assessGrade-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.meeting' ] =
    {
        url: '^/meeting.html',
        title: '会议 ',
        templateUrl: 'front_static/views/meeting.html',
        controller: 'meetingCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"meetingCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/meeting-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.meetingDetail' ] =
    {
        url: '^/meetingDetail.html?id',
        title: '会议详情 ',
        templateUrl: 'front_static/views/meetingDetail.html',
        controller: 'meetingDetailCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"meetingDetailCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/meetingDetail-ctrls.js"]
                 })
             }]
         }
    };
     this.routes[ 'main.online' ] =
    {
        url: '^/online.html',
        title: '线上实战 ',
        templateUrl: 'front_static/views/online.html',
        controller: 'onlineCtrl',
        resolve:{
             loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                 return $ocLazyLoad.load({
                     name:"onlineCtrl",
                     files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/online-ctrls.js"]
                 })
             }]
         }
    };

     /*我的模块*/
    this.routes[ 'main.me' ] =
    {
        url: '^/me.html?type',
        title: '我的',
        templateUrl: 'front_static/views/me.html',
           controller: 'meCtrl',
           resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me-ctrls.js","/front_static/components/controller/me/my_points-ctrls.js"]
                    })
                }]
            }
    };
    this.routes[ 'main.me.my_points'] =
        {
            url: '^/points.html?type',
            title: '积分列表 ',
            templateUrl: 'front_static/views/me/my_points.html',
            controller: 'my_pointsCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"my_pointsCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/my_points-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.person_data'] =
        {
            url: '^/person_data.html',
            title: '个人资料 ',
            templateUrl: 'front_static/views/me/person_data.html',
            controller: 'person_dataCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"person_dataCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/person_data-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.person_account'] =
        {
            url: '^/person_account.html',
            title: '账号设置 ',
            templateUrl: 'front_static/views/me/person_account.html',
            controller: 'person_accountCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"person_accountCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/person_account-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.person_safe'] =
        {
            url: '^/person_safe.html',
            title: '安全设置 ',
            templateUrl: 'front_static/views/me/person_safe.html',
            controller: 'person_safeCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"person_safeCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/person_safe-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.my_account'] =
        {
            url: '^/my_account.html',
            title: '我的账户 ',
            templateUrl: 'front_static/views/me/my_account.html',
            controller: 'my_accountCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"my_accountCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/my_account-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.my_order'] =
        {
            url: '^/my_order.html',
            title: '我的订单 ',
            templateUrl: 'front_static/views/me/my_order.html',
            controller: 'my_orderCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"my_orderCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/my_order-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.me.my_record'] =
        {
            url: '^/my_record.html',
            title: '会员记录 ',
            templateUrl: 'front_static/views/me/my_record.html',
            controller: 'my_recordCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"my_recordCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/my_record-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.me.meeting_list'] =
        {
            url: '^/meeting_list.html',
            title: '我的会议 ',
            templateUrl: 'front_static/views/me/meeting_list.html',
            controller: 'meeting_listCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meeting_listCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/meeting_list-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.me.assess_list'] =
        {
            url: '^/assess_list.html',
            title: '我的考核 ',
            templateUrl: 'front_static/views/me/assess_list.html',
            controller: 'assess_listCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"assess_listCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/assess_list-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.me.practice_error'] =
        {
            url: '^/practice_error.html',
            title: '我的错题 ',
            templateUrl: 'front_static/views/me/practice_error.html',
            controller: 'practice_errorCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"practice_errorCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/practice_error-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.me.practice_collect'] =
        {
            url: '^/practice_collect.html',
            title: '我的收藏 ',
            templateUrl: 'front_static/views/me/practice_collect.html',
            controller: 'practice_collectCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"practice_collectCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/practice_collect-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.practice_exam'] =
        {
            url: '^/practice_exam.html',
            title: '我的试卷 ',
            templateUrl: 'front_static/views/me/practice_exam.html',
            controller: 'practice_examCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"practice_examCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/practice_exam-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.title_list'] =
        {
            url: '^/title_list.html',
            title: '我的题目 ',
            templateUrl: 'front_static/views/me/title_list.html',
            controller: 'title_listCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"title_listCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/title_list-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.me.title_change'] =
        {
            url: '^/title_change.html',
            title: '修改题目 ',
            templateUrl: 'front_static/views/me/title_change.html',
            controller: 'title_changeCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"title_changeCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/title_change-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.me.title_upload'] =
        {
            url: '^/title_upload.html',
            title: '题目上传 ',
            templateUrl: 'front_static/views/me/title_upload.html',
            controller: 'title_uploadCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"title_uploadCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/me/title_upload-ctrls.js"]
                    })
                }]
            }
        };



     /*前台的后台管理区块*/
    this.routes[ 'main.bs' ] =
        {
            url: '^/bs.html',
            title: '后台管理 ',
            templateUrl: 'front_static/views/bs.html',
            controller: 'bsCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"bsCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/bs-ctrls.js"]
                    })
                }]
            }
        };
    this.routes[ 'main.bs.hassess'] =
        {
            url: '^/hassess.html',
            title: '考核列表 ',
            templateUrl: 'front_static/views/hassess/hassess.html',
            controller: 'hassessCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"hassessCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hassess/hassess-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.bs.hassessExam'] =
        {
            url: '^/hassessExam.html',
            title: '考卷管理 ',
            templateUrl: 'front_static/views/hassess/hassessExam.html',
            controller: 'hassessExamCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"hassessExamCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hassess/hassessExam-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.hassessGrade'] =
        {
            url: '^/hassessGrade.html',
            title: '成绩管理 ',
            templateUrl: 'front_static/views/hassess/hassessGrade.html',
            controller: 'hassessGradeCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"hassessGradeCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hassess/hassessGrade-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.addGrade'] =
        {
            url: '^/addGrade.html',
            title: '添加成绩 ',
            templateUrl: 'front_static/views/hassess/addGrade.html',
            controller: 'addGradeCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"addGradeCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hassess/addGrade-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.assessMark'] =
        {
            url: '^/assessMark.html?id',
            title: '考卷批阅 ',
            templateUrl: 'front_static/views/hassess/assessMark.html',
            controller: 'assessMarkCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"assessMarkCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hassess/assessMark-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.seeMark'] =
        {
            url: '^/seeMark.html',
            title: '查看考卷 ',
            templateUrl: 'front_static/views/hassess/seeMark.html',
            controller: 'seeMarkCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"seeMarkCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hassess/seeMark-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.bs.hmember'] =
        {
            url: '^/hmember.html',
            title: '成员管理 ',
            templateUrl: 'front_static/views/hmember/hmember.html',
            controller: 'hmemberCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"hmemberCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hmember/hmember-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.memberGroup'] =
        {
            url: '^/memberGroup.html',
            title: '成员分组 ',
            templateUrl: 'front_static/views/hmember/memberGroup.html',
            controller: 'memberGroupCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"memberGroupCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hmember/memberGroup-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.bs.examGroup'] =
        {
            url: '^/examGroup.html',
            title: '试卷分组 ',
            templateUrl: 'front_static/views/hexam/examGroup.html',
            controller: 'examGroupCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examGroupCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examGroup-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.bs.examLead'] =
        {
            url: '^/examLead.html',
            title: '试卷导入 ',
            templateUrl: 'front_static/views/hexam/examLead.html',
            controller: 'examLeadCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examLeadCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examLead-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.examList'] =
        {
            url: '^/examList.html',
            title: '试卷列表 ',
            templateUrl: 'front_static/views/hexam/examList.html',
            controller: 'examListCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examListCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examList-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.examChoice'] =
        {
            url: '^/examChoice.html?id,chose_type',
            url: '^/examChoice.html?id,chose_type',
            title: '选择题目 ',
            templateUrl: 'front_static/views/hexam/examChoice.html',
            controller: 'examChoiceCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examChoiceCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examChoice-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.examAdd'] =
        {
            url: '^/examAdd.html?id,chose_type',
            title: '添加题目 ',
            templateUrl: 'front_static/views/hexam/examAdd.html',
            controller: 'examAddCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examAddCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examAdd-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.examSee'] =
        {
            url: '^/examSee.html',
            title: '阅览试卷 ',
            templateUrl: 'front_static/views/hexam/examSee.html',
            controller: 'examSeeCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examSeeCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examSee-ctrls.js"]
                    })
                }]
            }
        };
        this.routes[ 'main.bs.examTitle'] =
        {
            url: '^/examTitle.html?id,name',
            title: '新增试卷-有题目 ',
            templateUrl: 'front_static/views/hexam/examTitle.html',
            controller: 'examTitleCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"examTitleCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hexam/examTitle-ctrls.js"]
                    })
                }]
            }
        };
        /*会议模块*/
        this.routes[ 'main.bs.meetList'] =
        {
            url: '^/meetList.html',
            title: '会议列表 ',
            templateUrl: 'front_static/views/hmeet/meetList.html',
            controller: 'meetListCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meetListCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hmeet/meetList-ctrls.js"]
                    })
                }]
            }
        };
         this.routes[ 'main.bs.meetAdd'] =
        {
            url: '^/meetAdd.html',
            title: '会议新增 ',
            templateUrl: 'front_static/views/hmeet/meetAdd.html',
            controller: 'meetAddCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meetAddCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hmeet/meetAdd-ctrls.js"]
                    })
                }]
            }
        };
          this.routes[ 'main.bs.meetMore'] =
        {
            url: '^/meetMore.html?id',
            title: '会议新增 ',
            templateUrl: 'front_static/views/hmeet/meetMore.html',
            controller: 'meetMoreCtrl',
            resolve:{
                loadMyCtrl:['$ocLazyLoad',function ($ocLazyLoad) {
                    return $ocLazyLoad.load({
                        name:"meetMoreCtrl",
                        files:["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hmeet/meetMore-ctrls.js"]
                    })
                }]
            }
        };
        /*会议资料模块*/
          this.routes['main.bs.meetData'] =
          {
              url: '^/meetData.html?id',
              title: '会议资料',
              templateUrl: 'front_static/views/hmeet/meetData.html',
              controller: 'meetDataCtrl',
              resolve:{
                  loadMyCtrl:['$ocLazyLoad',function($ocLazyLoad){
                      return $ocLazyLoad.load({
                          name:"meetDataCtrl",
                          files: ["/front_static/components/controller/main-ctrls.js","/front_static/components/controller/hmeet/meetData-ctrls.js"]
                      })
                  }]
              }
          }
        this.routes['main.bs.meetSignup'] =
        {

        }

    this.routes[ '404' ] =
        {
            url: '/404.html',
            title: '404',
            templateUrl: 'front_static/views/404.html',
        };
    this.$get = function()
    {
        return( this.routes );
    };
}
);
