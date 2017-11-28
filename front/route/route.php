<?php
// PSR-7
use \front\middleware\LoginMiddleware;
use \front\middleware\TestMiddleware;

/**
 * set route
 */
$container = $app->getContainer();
/* / */
$app->get('/', 'front\api\SiteController:actionDefault'); //默认路由
$login_middleware = new LoginMiddleware($container);

/* SiteController 登录,退出... */
$app->post('/login.json', 'front\api\SiteController:actionLogin');
$app->post('/register.json', 'front\api\SiteController:actionRegister');
$app->post('/reset.json', 'front\api\SiteController:actionReset'); //重置密码
$app->post('/account.json', 'front\api\SiteController:actionAccount');
$app->post('/logout.json', 'front\api\SiteController:actionLogout');
$app->post('/check.json', 'front\api\SiteController:actionCheckBind');
$app->post('/bind.json', 'front\api\SiteController:actionBind');
$app->post('/verification.json', 'front\api\SiteController:actionVerification');

/* UserController */
$app->post('/user/perfect.json', 'front\api\UserController:actionPerfect')->add($login_middleware);

/* CategoryController */
$app->post('/cate/special.json', 'front\api\CategoryController:actionSpecial')->add($login_middleware);
$app->get('/cate/options.json', 'front\api\CategoryController:actionOptions')->add($login_middleware);

/* SubjectController */
$app->post('/subject/special.json', 'front\api\SubjectController:actionListByCategory')->add($login_middleware);
$app->post('/subject/error.json', 'front\api\SubjectController:actionCheckError')->add($login_middleware);
$app->post('/purpose/check/one.json', 'front\api\SubjectController:actionCheckPurposeOne')->add($login_middleware);
$app->post('/purpose/one.json', 'front\api\SubjectController:actionPurposeOne')->add($login_middleware);
$app->post('/purpose/cancel/one.json', 'front\api\SubjectController:actionCancelPurposeOne')->add($login_middleware);
$app->post('/purpose/check/three.json', 'front\api\SubjectController:actionPurposeThree')->add($login_middleware);
$app->post('/purpose/two.json', 'front\api\SubjectController:actionPurposeTwo')->add($login_middleware);
$app->post('/purpose/list/two.json', 'front\api\SubjectController:actionPurposeTwoList')->add($login_middleware);
$app->post('/purpose/list/one.json', 'front\api\SubjectController:actionPurposeOneList')->add($login_middleware);
$app->post('/purpose/cancel/two.json', 'front\api\SubjectController:actionCancelPurposeTwo')->add($login_middleware);

/* OnlineController */
$app->get('/online/list.json', 'front\api\OnlineController:actionList')->add($login_middleware);
$app->get('/online/info.json', 'front\api\OnlineController:actionDetail')->add($login_middleware);
$app->post('/online/add.json', 'front\api\OnlineController:actionAdd')->add($login_middleware);
$app->get('/online/answer/list.json', 'front\api\OnlineController:actionAnswerList')->add($login_middleware);
$app->post('/online/comment/add.json', 'front\api\OnlineController:actionCommentAdd')->add($login_middleware);
$app->post('/online/thumbs/up.json', 'front\api\OnlineController:actionThumbsUp')->add($login_middleware);

/* ExercisesController */
$app->get('/exercises/list.json', 'front\api\ExercisesController:actionList')->add($login_middleware);
$app->get('/exercises/info.json', 'front\api\ExercisesController:actionInfo')->add($login_middleware);
$app->post('/exercises/start.json', 'front\api\ExercisesController:actionStart')->add($login_middleware);
$app->post('/exercises/end.json', 'front\api\ExercisesController:actionEnd')->add($login_middleware);

/* ExercisesRecordController */
$app->get('/exercises/record/info.json', 'front\api\ExercisesRecordController:actionInfo')->add($login_middleware);

/* OrderController */
$app->post('/order/add.json', 'front\api\OrderController:actionAdd')->add($login_middleware);
$app->get('/order/info.json', 'front\api\OrderController:actionInfo')->add($login_middleware);
$app->get('/order/list.json', 'front\api\OrderController:actionList')->add($login_middleware);
$app->post('/order/del.json', 'front\api\OrderController:actionDel')->add($login_middleware);

/* PayController */
$app->post('/pay/pay.json', 'front\api\PayController:actionPay')->add($login_middleware);
$app->any('/notify', 'front\api\PayController:actionNotify');
$app->any('/wxNotify', 'front\api\PayController:actionWxNotify');

/* QrController */
$app->get('/qr/code/{url}.json', 'front\api\QrController:actionUrlCode')->add($login_middleware);

/* FrontAssessController */
$app->post('/front/assess/start.json', 'front\api\FrontAssessController:actionStart')->add($login_middleware);
$app->post('/front/assess/end.json', 'front\api\FrontAssessController:actionEnd')->add($login_middleware);
$app->get('/front/assess/list.json', 'front\api\FrontAssessController:actionList')->add($login_middleware);
$app->get('/front/assess/info.json', 'front\api\FrontAssessController:actionInfo')->add($login_middleware);

/* FrontMeetingController */
$app->get('/meeting/front/download', 'front\api\FrontMeetingController:actionDownload')->add($login_middleware);
$app->post('/meeting/front/enroll.json', 'front\api\FrontMeetingController:actionEnroll')->add($login_middleware);
$app->get('/meeting/front/detail.json', 'front\api\FrontMeetingController:actionDetailInfo')->add($login_middleware);
$app->get('/meeting/front/list.json', 'front\api\FrontMeetingController:actionList')->add($login_middleware);
$app->get('/meeting/front/info.json', 'front\api\FrontMeetingController:actionInfo')->add($login_middleware);
$app->get('/meeting/front/guest.json', 'front\api\FrontMeetingController:actionGuestInfo')->add($login_middleware);

/* RegionController */
$app->get('/region/province/drop.json', 'front\api\RegionController:actionProvinceDrop')->add($login_middleware);
$app->get('/region/city/drop.json', 'front\api\RegionController:actionCityDrop')->add($login_middleware);

/* MemberController */
$app->post('/member/add.json', 'front\api\MemberController:actionAdd')->add($login_middleware);
$app->get('/member/list.json', 'front\api\MemberController:actionList')->add($login_middleware);

/* ScoreDetailsController */
$app->post('/score/list.json', 'front\api\ScoreDetailsController:actionList')->add($login_middleware);

/* UserCodeController */
$app->get('/code/list.json', 'front\api\UserCodeController:actionList')->add($login_middleware);
$app->post('/code/add.json', 'front\api\UserCodeController:actionAdd')->add($login_middleware);

/* MyController */
$app->get('/my/info.json', 'front\api\MyController:actionInfo')->add($login_middleware);
$app->post('/my/password.json', 'front\api\MyController:actionPassword')->add($login_middleware);
$app->post('/my/mobile.json', 'front\api\MyController:actionMobile')->add($login_middleware);
$app->post('/my/wechat.json', 'front\api\MyController:actionWechat')->add($login_middleware);
$app->post('/my/wechat/cancel.json', 'front\api\MyController:actionWechatCancel')->add($login_middleware);
$app->get('/my/examine/list.json', 'front\api\MyController:actionExamineList')->add($login_middleware);
$app->get('/my/examine/info.json', 'front\api\MyController:actionExamineInfo')->add($login_middleware);
$app->post('/my/examine/add.json', 'front\api\MyController:actionExamineAdd')->add($login_middleware);
$app->post('/my/examine/update.json', 'front\api\MyController:actionExamineUpdate')->add($login_middleware);
$app->get('/my/examine/do.json', 'front\api\MyController:actionExercisesDo')->add($login_middleware);
$app->get('/my/meeting.json', 'front\api\MyController:actionMeeting')->add($login_middleware);

/* DropController */
$app->get('/drop/questions.json', 'front\api\DropController:actionQuestions')->add($login_middleware);

/* TestController */
//$app->get('/test/test.json', 'front\api\TestController:actionTest')->setName('test')->add($login_middleware)->add(new TestMiddleware($container)); //访问控制器::方法
//$app->get('/test/years/list.json', 'front\api\TestController:actionYearsList')->add($login_middleware);

/******************************** 用户后台 ***************************************/
/* TestGroupController */
$app->post('/tg/add.json', 'front\api\TestGroupController:actionAdd')->setName('test.group.add')->add($login_middleware);
$app->post('/tg/del.json', 'front\api\TestGroupController:actionDel')->setName('test.group.del')->add($login_middleware);
$app->post('/tg/update.json', 'front\api\TestGroupController:actionUpdate')->setName('test.group.update')->add($login_middleware);
$app->get('/tg/list.json', 'front\api\TestGroupController:actionList')->setName('test.group.list')->add($login_middleware);
$app->get('/tg/info.json', 'front\api\TestGroupController:actionInfo')->setName('test.group.info')->add($login_middleware);
$app->get('/tg/drop.json', 'front\api\TestGroupController:actionDrop')->setName('test.group.drop')->add($login_middleware);

/* TestController */
$app->post('/test/add.json', 'front\api\TestController:actionAdd')->setName('test.add')->add($login_middleware);
$app->post('/test/del.json', 'front\api\TestController:actionDel')->setName('test.del')->add($login_middleware);
$app->post('/test/update.json', 'front\api\TestController:actionUpdate')->setName('test.update')->add($login_middleware);
$app->get('/test/list.json', 'front\api\TestController:actionList')->setName('test.list')->add($login_middleware);
$app->get('/test/info.json', 'front\api\TestController:actionInfo')->setName('test.info')->add($login_middleware);
$app->get('/test/preview.json', 'front\api\TestController:actionPreview')->setName('test.preview')->add($login_middleware);

/* TestSubjectController */
$app->post('/ts/add.json', 'front\api\TestSubjectController:actionAdd')->setName('test.ts.add')->add($login_middleware);
$app->post('/ts/choice.json', 'front\api\TestSubjectController:actionChoice')->setName('test.ts.choice')->add($login_middleware);
$app->post('/ts/status.json', 'front\api\TestSubjectController:actionStatus')->setName('test.ts.status')->add($login_middleware);
$app->post('/ts/del.json', 'front\api\TestSubjectController:actionDel')->setName('test.ts.del')->add($login_middleware);
$app->post('/ts/update.json', 'front\api\TestSubjectController:actionUpdate')->setName('test.ts.update')->add($login_middleware);
$app->post('/ts/order.json', 'front\api\TestSubjectController:actionOrder')->setName('test.ts.order')->add($login_middleware);
$app->get('/ts/list.json', 'front\api\TestSubjectController:actionList')->setName('test.ts.list')->add($login_middleware);
$app->get('/ts/info.json', 'front\api\TestSubjectController:actionInfo')->setName('test.ts.info')->add($login_middleware);

/* AssessGroupController */
$app->post('/ag/add.json', 'front\api\AssessGroupController:actionAdd')->setName('assess.group.add')->add($login_middleware);
$app->post('/ag/del.json', 'front\api\AssessGroupController:actionDel')->setName('assess.group.del')->add($login_middleware);
$app->post('/ag/update.json', 'front\api\AssessGroupController:actionUpdate')->setName('assess.group.update')->add($login_middleware);
$app->get('/ag/list.json', 'front\api\AssessGroupController:actionList')->setName('assess.group.list')->add($login_middleware);
$app->get('/ag/info.json', 'front\api\AssessGroupController:actionInfo')->setName('assess.group.info')->add($login_middleware);
$app->get('/ag/drop.json', 'front\api\AssessGroupController:actionDrop')->setName('assess.group.drop')->add($login_middleware);

/* AssessMembersController */
$app->get('/am/search.json', 'front\api\AssessMembersController:actionSearch')->setName('assess.members.search')->add($login_middleware);
$app->post('/am/add.json', 'front\api\AssessMembersController:actionAdd')->setName('assess.members.add')->add($login_middleware);
$app->post('/am/del.json', 'front\api\AssessMembersController:actionDel')->setName('assess.members.del')->add($login_middleware);
$app->post('/am/update.json', 'front\api\AssessMembersController:actionUpdate')->setName('assess.members.update')->add($login_middleware);
$app->get('/am/list.json', 'front\api\AssessMembersController:actionList')->setName('assess.members.list')->add($login_middleware);
$app->get('/am/info.json', 'front\api\AssessMembersController:actionInfo')->setName('assess.members.info')->add($login_middleware);
$app->get('/am/drop.json', 'front\api\AssessMembersController:actionDrop')->setName('assess.members.info')->add($login_middleware);

/* AssessController */
$app->post('/assess/add.json', 'front\api\AssessController:actionAdd')->setName('assess.add')->add($login_middleware);
$app->post('/assess/del.json', 'front\api\AssessController:actionDel')->setName('assess.del')->add($login_middleware);
$app->post('/assess/update.json', 'front\api\AssessController:actionUpdate')->setName('assess.update')->add($login_middleware);
$app->get('/assess/list.json', 'front\api\AssessController:actionList')->setName('assess.list')->add($login_middleware);
$app->get('/assess/info.json', 'front\api\AssessController:actionInfo')->setName('assess.info')->add($login_middleware);
$app->post('/assess/relation.json', 'front\api\AssessController:actionUserAssess')->setName('assess.info')->add($login_middleware);

/* AssessUserController 用户考核记录 */
$app->post('/au/update.json', 'front\api\AssessUserController:actionUpdate')->setName('assess.user.update')->add($login_middleware);
$app->post('/au/making.json', 'front\api\AssessUserController:actionMaking')->setName('assess.user.update')->add($login_middleware);
$app->post('/au/state.json', 'front\api\AssessUserController:actionState')->setName('assess.user.update')->add($login_middleware);
$app->get('/au/list.json', 'front\api\AssessUserController:actionList')->setName('assess.user.list')->add($login_middleware);
$app->get('/au/info.json', 'front\api\AssessUserController:actionInfo')->setName('assess.user.info')->add($login_middleware);

/* MeetingController */
$app->post('/meeting/add.json', 'front\api\MeetingController:actionAdd')->setName('meeting.add')->add($login_middleware);
$app->post('/meeting/release.json', 'front\api\MeetingController:actionRelease')->setName('meeting.release')->add($login_middleware);
$app->post('/meeting/enroll.json', 'front\api\MeetingController:actionEnroll')->setName('meeting.enroll')->add($login_middleware);
$app->post('/meeting/state.json', 'front\api\MeetingController:actionState')->setName('meeting.state')->add($login_middleware);
$app->post('/meeting/del.json', 'front\api\MeetingController:actionDel')->setName('meeting.del')->add($login_middleware);
$app->post('/meeting/update.json', 'front\api\MeetingController:actionUpdate')->setName('meeting.update')->add($login_middleware);
$app->get('/meeting/list.json', 'front\api\MeetingController:actionList')->setName('meeting.list')->add($login_middleware);
$app->get('/meeting/info.json', 'front\api\MeetingController:actionInfo')->setName('meeting.info')->add($login_middleware);
$app->post('/meeting/data.json', 'front\api\MeetingController:actionData')->setName('meeting.data')->add($login_middleware);
$app->post('/meeting/detail/add.json', 'front\api\MeetingController:actionDetailAdd')->setName('meeting.detail.add')->add($login_middleware);
$app->get('/meeting/detail/info.json', 'front\api\MeetingController:actionDetailInfo')->setName('meeting.detail.info')->add($login_middleware);
$app->post('/meeting/detail/update.json', 'front\api\MeetingController:actionDetailUpdate')->setName('meeting.detail.update')->add($login_middleware);

/* MeetingDataController  */
$app->any('/meetingdata/list.json', 'front\api\MeetingDataController:actionMeetingDataList')->setName('meetingdata.list')->add($login_middleware);
$app->any('/meetingdata/status.json', 'front\api\MeetingDataController:actionMeetDatajin')->setName('meetingdata.jin')->add($login_middleware);
$app->post('/meetingdata/del.json', 'front\api\MeetingDataController:actionDel')->setName('meetingdata.del')->add($login_middleware);

/* Meeting*/

//$app->any('/test/', function () {
//    echo 'test';
//    exit;
//});