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

