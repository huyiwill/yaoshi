<?php
/**
 *  mongo03    查询文档
 * MongoDB 查询文档使用 find() 方法。
 * find() 方法以非结构化的方式来显示所有文档。
 */

/** 语法
 * MongoDB 查询数据的语法格式如下：
 * db.collection.find(query, projection)
 * query ：可选，使用查询操作符指定查询条件
 * projection ：可选，使用投影操作符指定返回的键。查询时返回文档中所有键值， 只需省略该参数即可（默认省略）。
 */

/**
 * 如果你需要以易读的方式来读取数据，可以使用 pretty() 方法，语法格式如下：
 * >db.col.find().pretty()
 * pretty() 方法以格式化的方式来显示所有文档。
 * 除了 find() 方法之外，还有一个 findOne() 方法，它只返回一个文档。
 */

/**
 * MongoDB 与 RDBMS Where 语句比较
 * 如果你熟悉常规的 SQL 数据，通过下表可以更好的理解 MongoDB 的条件语句查询：
 * 操作                格式                         范例                                          RDBMS中的类似语句
 * 等于             {<key>:<value>}           db.col.find({"by":"菜鸟教程"}).pretty()                where by = '菜鸟教程'
 * 小于           {<key>:{$lt:<value>}}       db.col.find({"likes":{$lt:50}}).pretty()              where likes < 50
 * 小于或等于      {<key>:{$lte:<value>}}     db.col.find({"likes":{$lte:50}}).pretty()              where likes <= 50
 * 大于           {<key>:{$gt:<value>}}       db.col.find({"likes":{$gt:50}}).pretty()               where likes > 50
 * 大于或等于      {<key>:{$gte:<value>}}     db.col.find({"likes":{$gte:50}}).pretty()              where likes >= 50
 * 不等于         {<key>:{$ne:<value>}}       db.col.find({"likes":{$ne:50}}).pretty()                where likes != 50
 */

/**
 * MongoDB AND 条件
 * MongoDB 的 find() 方法可以传入多个键(key)，每个键(key)以逗号隔开，即常规 SQL 的 AND 条件。
 * 语法格式如下：
 * >db.col.find({key1:value1, key2:value2}).pretty()
 */

/**
 * MongoDB OR 条件      OR 条件
 *MongoDB OR 条件语句使用了关键字 $or,语法格式如下：
 * >db.col.find(
 *  {
 * $or: [
 * {key1: value1}, {key2:value2}
 *    ]
 *  }
 * ).pretty()
 */

/**
 * AND 和 OR 联合使用
 * 以下实例演示了 AND 和 OR 联合使用，类似常规 SQL 语句为： 'where likes>50 AND (by = '菜鸟教程' OR title = 'MongoDB 教程')'
 * >db.col.find({
 * "likes": {$gt:50},
 * $or: [{"by": "菜鸟教程"},{"title": "MongoDB 教程"}]
 * }).pretty()
 * {
 * "_id" : ObjectId("56063f17ade2f21f36b03133"),
 * "title" : "MongoDB 教程",
 * "description" : "MongoDB 是一个 Nosql 数据库",
 * "by" : "菜鸟教程",
 * "url" : "http://www.runoob.com",
 * "tags" : [
 * "mongodb",
 * "database",
 * "NoSQL"
 * ],
 * "likes" : 100
 * }
 */

/**
 * 笔记列表
 * 补充一下 projection 参数的使用方法
 * db.collection.find(query, projection)
 * 若不指定 projection，则默认返回所有键，指定 projection 格式如下，有两种模式
 * db.collection.find(query, {title: 1, by: 1}) // inclusion模式 指定返回的键，不返回其他键
 * db.collection.find(query, {title: 0, by: 0}) // exclusion模式 指定不返回的键,返回其他键
 * _id 键默认返回，需要主动指定 _id:0 才会隐藏
 * 两种模式不可混用（因为这样的话无法推断其他键是否应返回）
 * db.collection.find(query, {title: 1, by: 0}) // 错误
 * 只能全1或全0，除了在inclusion模式时可以指定_id为0
 * db.collection.find(query, {_id:0, title: 1, by: 1}) // 正确l
 */