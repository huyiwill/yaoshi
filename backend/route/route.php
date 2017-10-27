<?php
// PSR-7
use \backend\middleware\LoginMiddleware;

/**
 * set route
 */
$container = $app->getContainer();

/* / */
$app->get('/', 'backend\api\SiteController:actionDefault');
$login_middleware = new LoginMiddleware($container);

/* SiteController 登录,退出 */
$app->post('/login.json', 'backend\api\SiteController:actionLogin');
$app->post('/account.json', 'backend\api\SiteController:actionAccount');
$app->post('/logout.json', 'backend\api\SiteController:actionLogout');

/* AdminController */
$app->post('/admin/add.json', 'backend\api\AdminController:actionAdd')->add($login_middleware);
$app->post('/admin/status.json', 'backend\api\AdminController:actionStatus')->add($login_middleware);
$app->post('/admin/del.json', 'backend\api\AdminController:actionDel')->add($login_middleware);
$app->post('/admin/update.json', 'backend\api\AdminController:actionUpdate')->add($login_middleware);
$app->get('/admin/list.json', 'backend\api\AdminController:actionList')->add($login_middleware);
$app->get('/admin/info.json', 'backend\api\AdminController:actionInfo')->add($login_middleware);

/* HospitalController */
$app->post('/hospital/add.json', 'backend\api\HospitalController:actionAdd')->add($login_middleware);
$app->post('/hospital/status.json', 'backend\api\HospitalController:actionStatus')->add($login_middleware);
$app->post('/hospital/del.json', 'backend\api\HospitalController:actionDel')->add($login_middleware);
$app->post('/hospital/update.json', 'backend\api\HospitalController:actionUpdate')->add($login_middleware);
$app->get('/hospital/list.json', 'backend\api\HospitalController:actionList')->add($login_middleware);
$app->get('/hospital/info.json', 'backend\api\HospitalController:actionInfo')->add($login_middleware);
$app->get('/hospital/drop.json', 'backend\api\HospitalController:actionDrop')->add($login_middleware);

/* CompanyController */
$app->post('/company/add.json', 'backend\api\CompanyController:actionAdd')->add($login_middleware);
$app->post('/company/status.json', 'backend\api\CompanyController:actionStatus')->add($login_middleware);
$app->post('/company/del.json', 'backend\api\CompanyController:actionDel')->add($login_middleware);
$app->post('/company/update.json', 'backend\api\CompanyController:actionUpdate')->add($login_middleware);
$app->get('/company/list.json', 'backend\api\CompanyController:actionList')->add($login_middleware);
$app->get('/company/info.json', 'backend\api\CompanyController:actionInfo')->add($login_middleware);
$app->get('/company/drop.json', 'backend\api\CompanyController:actionDrop')->add($login_middleware);

/* RegionController */
$app->get('/region/province/drop.json', 'backend\api\RegionController:actionProvinceDrop')->add($login_middleware);
$app->get('/region/city/drop.json', 'backend\api\RegionController:actionCityDrop')->add($login_middleware);

/* DepartmentController */
$app->get('/department/list.json', 'backend\api\DepartmentController:actionList')->add($login_middleware);
$app->post('/department/add.json', 'backend\api\DepartmentController:actionAdd')->add($login_middleware);
$app->post('/department/update.json', 'backend\api\DepartmentController:actionUpdate')->add($login_middleware);
$app->post('/department/status.json', 'backend\api\DepartmentController:actionStatus')->add($login_middleware);
$app->post('/department/del.json', 'backend\api\DepartmentController:actionDel')->add($login_middleware);
$app->get('/department/info.json', 'backend\api\DepartmentController:actionInfo')->add($login_middleware);
$app->get('/department/drop.json', 'backend\api\DepartmentController:actionDrop')->add($login_middleware);
$app->get('/department/options.json', 'backend\api\DepartmentController:actionOptions')->add($login_middleware);

/* SchoolMajorController */
$app->get('/major/list.json', 'backend\api\SchoolMajorController:actionList')->add($login_middleware);
$app->post('/major/add.json', 'backend\api\SchoolMajorController:actionAdd')->add($login_middleware);
$app->post('/major/update.json', 'backend\api\SchoolMajorController:actionUpdate')->add($login_middleware);
$app->post('/major/status.json', 'backend\api\SchoolMajorController:actionStatus')->add($login_middleware);
$app->post('/major/del.json', 'backend\api\SchoolMajorController:actionDel')->add($login_middleware);
$app->get('/major/info.json', 'backend\api\SchoolMajorController:actionInfo')->add($login_middleware);
$app->get('/major/drop.json', 'backend\api\SchoolMajorController:actionDrop')->add($login_middleware);
$app->get('/major/options.json', 'backend\api\SchoolMajorController:actionOptions')->add($login_middleware);

/* RoleController */
$app->post('/role/add.json', 'backend\api\RoleController:actionAdd')->add($login_middleware);
$app->post('/role/status.json', 'backend\api\RoleController:actionStatus')->add($login_middleware);
$app->post('/role/del.json', 'backend\api\RoleController:actionDel')->add($login_middleware);
$app->post('/role/update.json', 'backend\api\RoleController:actionUpdate')->add($login_middleware);
$app->get('/role/list.json', 'backend\api\RoleController:actionList')->add($login_middleware);
$app->get('/role/info.json', 'backend\api\RoleController:actionInfo')->add($login_middleware);
$app->get('/role/drop.json', 'backend\api\RoleController:actionDrop')->add($login_middleware);

/* UserController */
$app->post('/user/add.json', 'backend\api\UserController:actionAdd')->add($login_middleware);
$app->post('/user/status.json', 'backend\api\UserController:actionStatus')->add($login_middleware);
$app->post('/user/del.json', 'backend\api\UserController:actionDel')->add($login_middleware);
$app->post('/user/update.json', 'backend\api\UserController:actionUpdate')->add($login_middleware);
$app->get('/user/list.json', 'backend\api\UserController:actionList')->add($login_middleware);
$app->get('/user/info.json', 'backend\api\UserController:actionInfo')->add($login_middleware);

/* GroupController */
$app->post('/group/add.json', 'backend\api\GroupController:actionAdd')->add($login_middleware);
$app->get('/group/list.json', 'backend\api\GroupController:actionList')->add($login_middleware);
$app->post('/group/status.json', 'backend\api\GroupController:actionStatus')->add($login_middleware);
$app->post('/group/del.json', 'backend\api\GroupController:actionDel')->add($login_middleware);
$app->post('/group/update.json', 'backend\api\GroupController:actionUpdate')->add($login_middleware);
$app->get('/group/info.json', 'backend\api\GroupController:actionInfo')->add($login_middleware);
$app->get('/group/drop.json', 'backend\api\GroupController:actionDrop')->add($login_middleware);

/* CategoryController */
$app->get('/category/list.json', 'backend\api\CategoryController:actionList')->add($login_middleware);
$app->post('/category/add.json', 'backend\api\CategoryController:actionAdd')->add($login_middleware);
$app->post('/category/update.json', 'backend\api\CategoryController:actionUpdate')->add($login_middleware);
$app->post('/category/status.json', 'backend\api\CategoryController:actionStatus')->add($login_middleware);
$app->post('/category/del.json', 'backend\api\CategoryController:actionDel')->add($login_middleware);
$app->get('/category/info.json', 'backend\api\CategoryController:actionInfo')->add($login_middleware);
$app->get('/category/drop.json', 'backend\api\CategoryController:actionDrop')->add($login_middleware);
$app->get('/category/options.json', 'backend\api\CategoryController:actionOptions')->add($login_middleware);

/* QuestionsController */
$app->get('/questions/list.json', 'backend\api\QuestionsController:actionList')->add($login_middleware);
$app->post('/questions/add.json', 'backend\api\QuestionsController:actionAdd')->add($login_middleware);
$app->post('/questions/update.json', 'backend\api\QuestionsController:actionUpdate')->add($login_middleware);
$app->post('/questions/status.json', 'backend\api\QuestionsController:actionStatus')->add($login_middleware);
$app->post('/questions/del.json', 'backend\api\QuestionsController:actionDel')->add($login_middleware);
$app->get('/questions/info.json', 'backend\api\QuestionsController:actionInfo')->add($login_middleware);
$app->get('/questions/drop.json', 'backend\api\QuestionsController:actionDrop')->add($login_middleware);

/* SubjectController */
$app->get('/subject/list.json', 'backend\api\SubjectController:actionList')->add($login_middleware);
$app->post('/subject/add.json', 'backend\api\SubjectController:actionAdd')->add($login_middleware);
$app->post('/subject/update.json', 'backend\api\SubjectController:actionUpdate')->add($login_middleware);
$app->post('/subject/status.json', 'backend\api\SubjectController:actionStatus')->add($login_middleware);
$app->post('/subject/del.json', 'backend\api\SubjectController:actionDel')->add($login_middleware);
$app->get('/subject/info.json', 'backend\api\SubjectController:actionInfo')->add($login_middleware);
/* OnlineController */
$app->get('/online/list.json', 'backend\api\OnlineController:actionList')->add($login_middleware);
$app->get('/online/info.json', 'backend\api\OnlineController:actionDetail')->add($login_middleware);
$app->get('/online/answer/list.json', 'backend\api\OnlineController:actionAnswerList')->add($login_middleware);
$app->post('/online/answer/status.json', 'backend\api\OnlineController:actionAnswerStatus')->add($login_middleware);
$app->post('/online/answer/del.json', 'backend\api\OnlineController:actionAnswerDel')->add($login_middleware);
$app->post('/online/comment/add.json', 'backend\api\OnlineController:actionCommentAdd')->add($login_middleware);
$app->post('/online/comment/status.json', 'backend\api\OnlineController:actionCommentStatus')->add($login_middleware);
$app->post('/online/comment/del.json', 'backend\api\OnlineController:actionCommentDel')->add($login_middleware);

/* TestGroupController */
$app->post('/tg/add.json', 'backend\api\TestGroupController:actionAdd')->add($login_middleware);
$app->post('/tg/status.json', 'backend\api\TestGroupController:actionStatus')->add($login_middleware);
$app->post('/tg/del.json', 'backend\api\TestGroupController:actionDel')->add($login_middleware);
$app->post('/tg/update.json', 'backend\api\TestGroupController:actionUpdate')->add($login_middleware);
$app->get('/tg/list.json', 'backend\api\TestGroupController:actionList')->add($login_middleware);
$app->get('/tg/info.json', 'backend\api\TestGroupController:actionInfo')->add($login_middleware);
$app->get('/tg/drop.json', 'backend\api\TestGroupController:actionDrop')->add($login_middleware);

/* ExercisesController */
$app->post('/exercises/add.json', 'backend\api\ExercisesController:actionAdd')->add($login_middleware);
$app->post('/exercises/status.json', 'backend\api\ExercisesController:actionStatus')->add($login_middleware);
$app->post('/exercises/type.json', 'backend\api\ExercisesController:actionType')->add($login_middleware);
$app->post('/exercises/del.json', 'backend\api\ExercisesController:actionDel')->add($login_middleware);
$app->post('/exercises/update.json', 'backend\api\ExercisesController:actionUpdate')->add($login_middleware);
$app->get('/exercises/list.json', 'backend\api\ExercisesController:actionList')->add($login_middleware);
$app->get('/exercises/info.json', 'backend\api\ExercisesController:actionInfo')->add($login_middleware);
$app->get('/exercises/preview.json', 'backend\api\ExercisesController:actionPreview')->add($login_middleware);

/* ExercisesSubjectController */
$app->post('/es/add.json', 'backend\api\ExercisesSubjectController:actionAdd')->add($login_middleware);
$app->post('/es/choice.json', 'backend\api\ExercisesSubjectController:actionChoice')->add($login_middleware);
$app->post('/es/status.json', 'backend\api\ExercisesSubjectController:actionStatus')->add($login_middleware);
$app->post('/es/del.json', 'backend\api\ExercisesSubjectController:actionDel')->add($login_middleware);
$app->post('/es/update.json', 'backend\api\ExercisesSubjectController:actionUpdate')->add($login_middleware);
$app->post('/es/order.json', 'backend\api\ExercisesSubjectController:actionOrder')->add($login_middleware);
$app->get('/es/list.json', 'backend\api\ExercisesSubjectController:actionList')->add($login_middleware);
$app->get('/es/info.json', 'backend\api\ExercisesSubjectController:actionInfo')->add($login_middleware);

/* TestController */
$app->post('/test/add.json', 'backend\api\TestController:actionAdd')->add($login_middleware);
$app->post('/test/status.json', 'backend\api\TestController:actionStatus')->add($login_middleware);
$app->post('/test/del.json', 'backend\api\TestController:actionDel')->add($login_middleware);
$app->post('/test/update.json', 'backend\api\TestController:actionUpdate')->add($login_middleware);
$app->get('/test/list.json', 'backend\api\TestController:actionList')->add($login_middleware);
$app->get('/test/info.json', 'backend\api\TestController:actionInfo')->add($login_middleware);
$app->get('/test/preview.json', 'backend\api\TestController:actionPreview')->add($login_middleware);

/* TestSubjectController */
$app->post('/ts/add.json', 'backend\api\TestSubjectController:actionAdd')->add($login_middleware);
$app->post('/ts/choice.json', 'backend\api\TestSubjectController:actionChoice')->add($login_middleware);
$app->post('/ts/status.json', 'backend\api\TestSubjectController:actionStatus')->add($login_middleware);
$app->post('/ts/del.json', 'backend\api\TestSubjectController:actionDel')->add($login_middleware);
$app->post('/ts/update.json', 'backend\api\TestSubjectController:actionUpdate')->add($login_middleware);
$app->post('/ts/order.json', 'backend\api\TestSubjectController:actionOrder')->add($login_middleware);
$app->get('/ts/list.json', 'backend\api\TestSubjectController:actionList')->add($login_middleware);
$app->get('/ts/info.json', 'backend\api\TestSubjectController:actionInfo')->add($login_middleware);

/* AssessController */
$app->get('/assess/list.json', 'backend\api\AssessController:actionList')->add($login_middleware);

/* MeetingController */
$app->post('/meeting/add.json', 'backend\api\MeetingController:actionAdd')->add($login_middleware);
$app->post('/meeting/release.json', 'backend\api\MeetingController:actionRelease')->add($login_middleware);
$app->post('/meeting/enroll.json', 'backend\api\MeetingController:actionEnroll')->add($login_middleware);
$app->post('/meeting/state.json', 'backend\api\MeetingController:actionState')->add($login_middleware);
$app->post('/meeting/del.json', 'backend\api\MeetingController:actionDel')->add($login_middleware);
$app->post('/meeting/update.json', 'backend\api\MeetingController:actionUpdate')->add($login_middleware);
$app->get('/meeting/list.json', 'backend\api\MeetingController:actionList')->add($login_middleware);
$app->get('/meeting/info.json', 'backend\api\MeetingController:actionInfo')->add($login_middleware);
$app->post('/meeting/data.json', 'backend\api\MeetingController:actionData')->add($login_middleware);
$app->post('/meeting/detail/add.json', 'backend\api\MeetingController:actionDetailAdd')->add($login_middleware);
$app->get('/meeting/detail/info.json', 'backend\api\MeetingController:actionDetailInfo')->add($login_middleware);
$app->post('/meeting/detail/update.json', 'backend\api\MeetingController:actionDetailUpdate')->add($login_middleware);

/* MemberTypeController */
$app->post('/mt/add.json', 'backend\api\MemberTypeController:actionAdd')->add($login_middleware);
$app->post('/mt/status.json', 'backend\api\MemberTypeController:actionStatus')->add($login_middleware);
$app->post('/mt/del.json', 'backend\api\MemberTypeController:actionDel')->add($login_middleware);
$app->post('/mt/update.json', 'backend\api\MemberTypeController:actionUpdate')->add($login_middleware);
$app->get('/mt/list.json', 'backend\api\MemberTypeController:actionList')->add($login_middleware);
$app->get('/mt/info.json', 'backend\api\MemberTypeController:actionInfo')->add($login_middleware);
$app->get('/mt/drop.json', 'backend\api\MemberTypeController:actionDrop')->add($login_middleware);

/* MemberController */
$app->post('/member/add.json', 'backend\api\MemberController:actionAdd')->add($login_middleware);
$app->post('/member/status.json', 'backend\api\MemberController:actionStatus')->add($login_middleware);
$app->post('/member/del.json', 'backend\api\MemberController:actionDel')->add($login_middleware);
$app->get('/member/list.json', 'backend\api\MemberController:actionList')->add($login_middleware);
$app->get('/member/info.json', 'backend\api\MemberController:actionInfo')->add($login_middleware);
$app->get('/member/drop.json', 'backend\api\MemberController:actionDrop')->add($login_middleware);

/* OrderController */
$app->get('/order/list.json', 'backend\api\OrderController:actionList')->add($login_middleware);
$app->get('/order/info.json', 'backend\api\OrderController:actionInfo')->add($login_middleware);