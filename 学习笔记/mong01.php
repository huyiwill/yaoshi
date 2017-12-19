<?php

/**
 * mongodb  01  更新文档方法
 */

//update

//update  用于更新已存在的文档.  语法格式如下:
/**
 * db.collection.update(
 * <query>,
 * <update>,
 *  {
 *      upsert : <boolean>,
 *      multi  : <boolean>,
 *      writeConcern : <document>
 *  }
 * )
 * query  : update的查询条件，类似sql update查询内where后面的。
 * update : update的对象和一些更新的操作符（如$,$inc...）等，也可以理解为sql update查询内set后面的
 *
 * upsert : 可选，这个参数的意思是，如果不存在update的记录，是否插入objNew,true为插入，默认是false，不插入。
 * multi  : 可选，mongodb 默认是false,只更新找到的第一条记录，如果这个参数为true,就把按条件查出来多条记录全部更新。
 * writeConcern :可选，抛出异常的级别。
 */

//实例  在集合col中插入数据

/*

  db.col.insert({
       title: 'mongo',
       description : 'mongodb is a nice',
       by : 'huyi',
       url : 'http://www.baidu.com',
       tags : ['mongodb', 'database', 'NoSQL'],
       likes: 100
  })

 */

//  接着通过update()方法更新标题(title)

/*
 * db.col.update({'title':'mongo'},{$set:{'title':'php'}});
 *
 * db.col.find().pretty();
 */

//save
//save() 方法通过传入的文档来替换已有文档。语法格式如下：
/**
 * db.collection.save(
 *      <document>,
 *      {
 *          writeConcern :  <document>
 *          }
 * )
 * 参数说明：
 * document : 文档数据。
 * writeConcern :可选，抛出异常的级别。
 */

//实例:
/*

 db.col.save({
    "_id" : ObjectId("56064f89ade2f21f36b03136"),
    "title" : "MongoDB",
    "description" : "MongoDB 是一个 Nosql 数据库",
    "by" : "Runoob",
    "url" : "http://www.runoob.com",
    "tags" : [
            "mongodb",
            "NoSQL"
    ],
    "likes" : 110
})

*/

/*
 *
 *
 更多实例
只更新第一条记录：
db.col.update( { "count" : { $gt : 1 } } , { $set : { "test2" : "OK"} } );
全部更新：
db.col.update( { "count" : { $gt : 3 } } , { $set : { "test2" : "OK"} },false,true );
只添加第一条：
db.col.update( { "count" : { $gt : 4 } } , { $set : { "test5" : "OK"} },true,false );
全部添加加进去:
db.col.update( { "count" : { $gt : 5 } } , { $set : { "test5" : "OK"} },true,true );
全部更新：
db.col.update( { "count" : { $gt : 15 } } , { $inc : { "count" : 1} },false,true );
只更新第一条记录：
db.col.update( { "count" : { $gt : 10 } } , { $inc : { "count" : 1} },false,false );


 */

/**
 * 笔记列表     3.2版本开始             3.2版本开始             3.2版本开始
 * 在3.2版本开始，MongoDB提供以下更新集合文档的方法：
 *
 * db.collection.updateOne() 向指定集合更新单个文档
 * db.collection.updateMany() 向指定集合更新多个文档
 *
 * 首先我们在test集合里插入测试数据

   use test
   db.test_collection.insert( [
   {"name":"abc","age":"25","status":"zxc"},
   {"name":"dec","age":"19","status":"qwe"},
   {"name":"asd","age":"30","status":"nmn"},
   ] )

 *
 */

/**
 *
        更新单个文档
    db.test_collection.updateOne({"name":"abc"},{$set:{"age":"28"}})
    { "acknowledged" : true, "matchedCount" : 1, "modifiedCount" : 1 }
    db.test_collection.find()
    { "_id" : ObjectId("59c8ba673b92ae498a5716af"), "name" : "abc", "age" : "28", "status" : "zxc" }
    { "_id" : ObjectId("59c8ba673b92ae498a5716b0"), "name" : "dec", "age" : "19", "status" : "qwe" }
    { "_id" : ObjectId("59c8ba673b92ae498a5716b1"), "name" : "asd", "age" : "30", "status" : "nmn" }



 */


/**
        更新多个文档
 db.test_collection.updateMany({"age":{$gt:"10"}},{$set:{"status":"xyz"}})
{ "acknowledged" : true, "matchedCount" : 3, "modifiedCount" : 3 }
 db.test_collection.find()
{ "_id" : ObjectId("59c8ba673b92ae498a5716af"), "name" : "abc", "age" : "28", "status" : "xyz" }
{ "_id" : ObjectId("59c8ba673b92ae498a5716b0"), "name" : "dec", "age" : "19", "status" : "xyz" }
{ "_id" : ObjectId("59c8ba673b92ae498a5716b1"), "name" : "asd", "age" : "30", "status" : "xyz" }


 */


/*
 *
 WriteConcern.NONE:没有异常抛出
 WriteConcern.NORMAL:仅抛出网络错误异常，没有服务器错误异常
 WriteConcern.SAFE:抛出网络错误异常、服务器错误异常；并等待服务器完成写操作。
 WriteConcern.MAJORITY: 抛出网络错误异常、服务器错误异常；并等待一个主服务器完成写操作。
 WriteConcern.FSYNC_SAFE: 抛出网络错误异常、服务器错误异常；写操作等待服务器将数据刷新到磁盘。
 WriteConcern.JOURNAL_SAFE:抛出网络错误异常、服务器错误异常；写操作等待服务器提交到磁盘的日志文件。
 WriteConcern.REPLICAS_SAFE:抛出网络错误异常、服务器错误异常；等待至少2台服务器完成写操作。

 *
 */



/**
 * mongodb  02  删除文档方法
 */

/*
 * MongoDB remove()函数是用来移除集合中的数据。
 * MongoDB数据更新可以使用update()函数。在执行remove()函数前先执行find()命令来判断执行的条件是否正确，这是一个比较好的习惯。
 *
 * 语法
 * remove() 方法的基本语法格式如下所示：
 *
 *
 *  db.collection.remove(
 *    <query>,
 *    <justOne>
 *  )
 *
 *
 * 如果你的 MongoDB 是 2.6 版本以后的，语法格式如下：
 *
 * db.collection.remove(
 *  <query>,
 *  {
 *   justOne: <boolean>,
 *   writeConcern: <document>
 * }
 *  )
 *
 *
 *
 *
 * 参数说明：
 * query :（可选）删除的文档的条件。
 * justOne : （可选）如果设为 true 或 1，则只删除一个文档。
 * writeConcern :（可选）抛出异常的级别。
 */

/**
 * 以下文档我们执行两次插入操作：
    db.col.insert({title: 'MongoDB 教程',
    description: 'MongoDB 是一个 Nosql 数据库',
    by: '菜鸟教程',
    url: 'http://www.runoob.com',
    tags: ['mongodb', 'database', 'NoSQL'],
    likes: 100
 })
 *
 *
 *
 *  移除文档
 *
 *
 * db.col.remove({"title" : "MongoDb"})
 *
 *
 * 如果只想删除第一条找到的记录可以设置  justone = 1
 *
 * 如果你想删除所有数据，可以使用以下方式（类似常规 SQL 的 truncate 命令）：
 * db.col.remove({})
 *
 *
 *
 * remove() 方法已经过时了，现在官方推荐使用 deleteOne() 和 deleteMany() 方法。
 *
 * 如删除集合下全部文档：
 *
 * db.inventory.deleteMany({})
 * 删除 status 等于 A 的全部文档：
 * db.inventory.deleteMany({ status : "A" })
 * 删除 status 等于 D 的一个文档：
 * db.inventory.deleteOne( { status: "D" } )
 */

