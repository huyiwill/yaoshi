## 用户模块
* 注册接口 /register.json
* 验证码获取 /verification.json
* 登录接口 /login.json
* 重置密码 /reset.json
* 退出接口 /logout.json
* 判断登录/获取用户信息接口 /account.json
* 用户信息完善 /user/perfect.json
* 检测绑定 /check.json
* 绑定 /bind.json

## 分类模块
* 获取专项分类 /cate/special.json
* 下拉 /cate/options.json

## 题目模块
* 专项题目 /subject/special.json
* 专项纠错 /subject/error.json
* 是否已收藏 /purpose/check/one.json
* 收藏 /purpose/one.json
* 取消收藏 /purpose/cancel/one.json
* 查看解析 /purpose/check/three.json
* 积分购买解析 todo
* 新增错题 /purpose/two.json
* 错题练习 /purpose/list/two.json
* 收藏列表 /purpose/list/one.json
* 删除错题 /purpose/cancel/two.json
#### 线上实战
* 列表 /online/list.json
* 详情 /online/info.json
* 提交答案 /online/add.json
* 答案列表 /online/answer/list.json
* 评论添加 /online/comment/add.json
* 点赞 /online/thumbs/up.json

## 历年真题
* 试卷列表 /exercises/list.json
* 开始答题 /exercises/start.json
* 交卷 /exercises/end.json
* 查看答案 /exercises/record/info.json

## 支付模块
* 订单生成 /order/add.json
* 订单信息 /order/info.json
* 支付 /pay/pay.json

## 考核模块
*开始考核 /front/assess/start.json
*用户考核列表 /front/assess/list.json
*用户考核列表 /front/assess/info.json

## 多对多关联表
* 题库<--多对多-->题目

## 系统地区模块
* 省下拉列表 /region/province/drop.json
* 市下拉列表 /region/city/drop.json

## 会议模块(前台)
* 下载资料 /meeting/front/upload.json
* 报名 /meeting/front/enroll.json
* 详细信息 /meeting/front/detail.json
* 列表 /meeting/front/list.json
* 基础信息 /meeting/front/info.json

##会员模块
* 开通会员 /member/add.json

##下拉模块
* 题库下拉 /drop/questions.json

#用户后台(前台的后台)

## 试卷模块
* 增 /test/add.json
* 删 /test/del.json
* 改 /test/update.json
* 查 /test/list.json
* 单条信息 /test/info.json
* 预览 /test/preview.json

##### 分组模块
* 增 /tg/add.json
* 删 /tg/del.json
* 改 /tg/update.json
* 查 /tg/list.json
* 单条信息 /tg/info.json
* 下拉列表 /tg/drop.json


## 考核模块

#### 考核成员分组
* 增 /ag/add.json
* 删 /ag/del.json
* 改 /ag/update.json
* 查 /ag/list.json
* 单条信息 /ag/info.json
* 下拉列表 /ag/drop.json

#### 考核成员
* 增 /am/add.json
* 删 /am/del.json
* 改 /am/update.json
* 查 /am/list.json
* 搜索 /am/search.json
* 单条信息 /am/info.json
* 下拉列表 /am/drop.json

#### 考核
* 增 /assess/add.json
* 删 /assess/del.json
* 改 /assess/update.json
* 查 /assess/list.json
* 单条信息 /assess/info.json
* 关联成员 /assess/relation.json

#### 用户考核记录
* 修改成绩 /au/update.json
* 批阅/重新批阅 /au/making.json
* 发布成绩 /au/state.json
* 查 /au/list.json
* 单条信息 /au/info.json

## 会议模块
##### 基础信息
* 增 /meeting/add.json
* 删 /meeting/del.json
* 改 /meeting/update.json
* 查 /meeting/list.json
* 发布 /meeting/release.json
* 报名开关 /meeting/enroll.json
* 会议开关 /meeting/state.json
* 单条信息 /meeting/info.json
#### 详细信息
*增 /meeting/detail/add.json
*信息 /meeting/detail/info.json
*改 /meeting/detail/update.json

## 试卷题目模块
* 增 /ts/add.json
* 删 /ts/del.json
* 改 /ts/update.json
* 查 /ts/list.json
* 禁用/启用 /ts/status.json
* 单条信息 /ts/info.json
* 题目选择 /ts/choice.json
* 题目排序 /ts/order.json