<?php
/**
 * MongoDB $type 操作符
 */

/**
 * $type操作符是基于BSON类型来检索集合中匹配的数据类型，并返回结果。
 * MongoDB 中可以使用的类型如下表所示：
 * 类型                       数字      备注
 * Double                     1
 * String                     2
 * Object                     3
 * Array                      4
 * Binary data                5
 * Undefined                  6     已废弃。
 * Object id                  7
 * Boolean                    8
 * Date                       9
 * Null                       10
 * Regular Expression         11
 * JavaScript                 13
 * Symbol                     14
 * JavaScript (with scope)    15
 * 32-bit integer             16
 * Timestamp                  17
 * 64-bit integer             18
 * Min key                    255    Query with -1.
 * Max key                    127
 */

/**
 * MongoDB 操作符 - $type 实例
 * 如果想获取 "col" 集合中 title 为 String 的数据，你可以使用以下命令：
 * db.col.find({"title" : {$type : 2}})
 */

/****************************************************************************************************************/
/***********************************************************************************************************************/

/**
 * MongoDB Limit与Skip方法
 */

/**
 * Limit
 */

/**
 * MongoDB Limit() 方法
 * 如果你需要在MongoDB中读取指定数量的数据记录，可以使用MongoDB的Limit方法，
 * limit()方法接受一个数字参数，该参数指定从MongoDB中读取的记录条数。
 * 语法
 * limit()方法基本语法如下所示：
 * >db.COLLECTION_NAME.find().limit(NUMBER)
 */

/**
 * Skip()
 */

/**
 * MongoDB Skip() 方法
 * 我们除了可以使用limit()方法来读取指定数量的数据外，还可以使用skip()方法来跳过指定数量的数据，
 * skip方法同样接受一个数字参数作为跳过的记录条数。
 */

/**
 * 笔记列表
 */

/**
 * db.col.find({},{"title":1,_id:0}).limit(2)
 * 补充说明：
 * 第一个 {} 放 where 条件，为空表示返回集合中所有文档。
 * 第二个 {} 指定那些列显示和不显示 （0表示不显示 1表示显示)。
 * 想要读取从 10 条记录后 100 条记录，相当于 sql 中limit (10,100)。
 * > db.COLLECTION_NAME.find().skip(10).limit(100)
 * 以上实例在集合中跳过前面 10 条返回 100 条数据。
 * skip 和 limit 结合就能实现分页。
 * 当查询时同时使用sort,skip,limit，无论位置先后，最先执行顺序 sort再skip再limit。
 */

/**
 * 补充说明
 */

/**
 * 补充说明skip和limit方法只适合小数据量分页，如果是百万级效率就会非常低，因为skip方法是一条条数据数过去的，建议使用where_limit
 * 在查看了一些资料之后，发现所有的资料都是这样说的：
 * 不要轻易使用Skip来做查询，否则数据量大了就会导致性能急剧下降，这是因为Skip是一条一条的数过来的，多了自然就慢了。
 * 这么说Skip就要避免使用了，那么如何避免呢？首先来回顾SQL分页的后一种时间戳分页方案，这种利用字段的有序性质，利用查询来取数据的方式，
 * 可以直接避免掉了大量的数数。也就是说，如果能附带上这样的条件那查询效率就会提高，事实上是这样的么？我们来验证一下：
 */
/**
 * b.test.sort({"amount":1}).skip(100000).limit(10)                      //183ms
 * db.test.find({amount:{$gt:2399927}}).sort({"amount":1}).limit(10)    //53ms
 */

/************************************************************************************************************************/
/***********************************************************************************************************************/

/**
 * MongoDB 排序
 * MongoDB sort()方法
 */

/**
 * 在MongoDB中使用使用sort()方法对数据进行排序，sort()方法可以通过参数指定排序的字段，
 * 并使用 1 和 -1 来指定排序的方式，
 * 其中 1 为升序排列，
 * 而-1是用于降序排列。
 * 语法
 * sort()方法基本语法如下所示：
 * >db.COLLECTION_NAME.find().sort({KEY:1})
 */

/**
 * 备注:
 * skip(), limilt(), sort()三个放在一起执行的时候，执行的顺序是先 sort(), 然后是 skip()，最后是显示的 limit()。
 */

/***********************************************************************************************************************/
/***********************************************************************************************************************/

/**
 *MongoDB 索引
 */

/**
 * 简介:
 * 索引通常能够极大的提高查询的效率，如果没有索引，MongoDB在读取数据时必须扫描集合中的每个文件并选取那些符合查询条件的记录。
 * 这种扫描全集合的查询效率是非常低的，特别在处理大量的数据时，查询可以要花费几十秒甚至几分钟，这对网站的性能是非常致命的。
 * 索引是特殊的数据结构，索引存储在一个易于遍历读取的数据集合中，索引是对数据库表中一列或多列的值进行排序的一种结构
 */

/**
 * ensureIndex() 方法
 * MongoDB使用 ensureIndex() 方法来创建索引。
 */

/**
 * 语法
 * ensureIndex()方法基本语法格式如下所示：
 * >db.COLLECTION_NAME.ensureIndex({KEY:1})
 * 语法中 Key 值为你要创建的索引字段，1为指定按升序创建索引，如果你想按降序来创建索引指定为-1即可。
 */

/**
 * 实例
 * >db.col.ensureIndex({"title":1})
 * >
 * 复合索引
 * ensureIndex() 方法中你也可以设置使用多个字段创建索引（关系型数据库中称作复合索引）。
 * >db.col.ensureIndex({"title":1,"description":-1})
 * >
 */

/**
 * ensureIndex() 接收可选参数，可选参数列表如下：
 * Parameter                Type                 Description
 * background                Boolean         建索引过程会阻塞其它数据库操作，background可指定以后台方式创建索引，即增加 "background" 可选参数。 "background" 默认值为false。
 * unique                   Boolean          建立的索引是否唯一。指定为true创建唯一索引。默认值为false.
 * name                     string           索引的名称。如果未指定，MongoDB的通过连接索引的字段名和排序顺序生成一个索引名称。
 * dropDups                  Boolean          在建立唯一索引时是否删除重复记录,指定 true 创建唯一索引。默认值为 false.
 * sparse                    Boolean            对文档中不存在的字段数据不启用索引；这个参数需要特别注意，如果设置为true的话，在索引字段中不会查询出不包含对应字段的文档.。默认值为 false.
 * expireAfterSeconds        integer          指定一个以秒为单位的数值，完成 TTL设定，设定集合的生存时间。
 * v                        index version       索引的版本号。默认的索引版本取决于mongod创建索引时运行的版本。
 * weights                  document             索引权重值，数值在 1 到 99,999 之间，表示该索引相对于其他索引字段的得分权重。
 * default_language           string            对于文本索引，该参数决定了停用词及词干和词器的规则的列表。 默认为英语
 * language_override         string             对于文本索引，该参数指定了包含在文档中的字段名，语言覆盖默认的language，默认值为 language.
 */

/**
 * 实例
 * 在后台创建索引：
 * db.values.ensureIndex({open: 1, close: 1}, {background: true})
 * 通过在创建索引时加background:true 的选项，让创建工作在后台执行
 */

/***********************************************************************************************************************/
/***********************************************************************************************************************/

/**
 * MongoDB 聚合
 */

/**
 * MongoDB中聚合(aggregate)主要用于处理数据(诸如统计平均值,求和等)，并返回计算后的数据结果。有点类似sql语句中的 count(*)。
 */

/**
 * aggregate() 方法
 */

/**
 * MongoDB中聚合的方法使用aggregate()。
 * 语法
 * aggregate() 方法的基本语法格式如下所示：
 * >db.COLLECTION_NAME.aggregate(AGGREGATE_OPERATION)
 */

/**
 * 现在我们通过以上集合计算每个作者所写的文章数，使用aggregate()计算结果如下：
 * > db.mycol.aggregate([{$group : {_id : "$by_user", num_tutorial : {$sum : 1}}}])
 * {
 * "result" : [
 * {
 * "_id" : "runoob.com",
 * "num_tutorial" : 2
 * },
 * {
 * "_id" : "Neo4j",
 * "num_tutorial" : 1
 * }
 * ],
 * "ok" : 1
 * }
 * >
 * 以上实例类似sql语句： select by_user, count(*) from mycol group by by_user
 */

/**
 * 下表展示了一些聚合的表达式:
 * 表达式         描述                                                    实例
 * $sum         计算总和。                                             db.mycol.aggregate([{$group : {_id : "$by_user", num_tutorial : {$sum : "$likes"}}}])
 * $avg        计算平均值                                             db.mycol.aggregate([{$group : {_id : "$by_user", num_tutorial : {$avg : "$likes"}}}])
 * $min        获取集合中所有文档对应值得最小值。                       db.mycol.aggregate([{$group : {_id : "$by_user", num_tutorial : {$min : "$likes"}}}])
 * $max        获取集合中所有文档对应值得最大值。                       db.mycol.aggregate([{$group : {_id : "$by_user", num_tutorial : {$max : "$likes"}}}])
 * $push       在结果文档中插入值到一个数组中。                        db.mycol.aggregate([{$group : {_id : "$by_user", url : {$push: "$url"}}}])
 * $addToSet    在结果文档中插入值到一个数组中，但不创建副本。       db.mycol.aggregate([{$group : {_id : "$by_user", url : {$addToSet : "$url"}}}])
 * $first      根据资源文档的排序获取第一个文档数据。                 db.mycol.aggregate([{$group : {_id : "$by_user", first_url : {$first : "$url"}}}])
 * $last      根据资源文档的排序获取最后一个文档数据                  db.mycol.aggregate([{$group : {_id : "$by_user", last_url : {$last : "$url"}}}])
 */

/**
 * 管道的概念
 * 管道在Unix和Linux中一般用于将当前命令的输出结果作为下一个命令的参数。
 */

/**
 * MongoDB的聚合管道将MongoDB文档在一个管道处理完毕后将结果传递给下一个管道处理。管道操作是可以重复的。
 */

/**
 * 这里我们介绍一下聚合框架中常用的几个操作：
 * $project：修改输入文档的结构。可以用来重命名、增加或删除域，也可以用于创建计算结果以及嵌套文档。
 * $match：用于过滤数据，只输出符合条件的文档。$match使用MongoDB的标准查询操作。
 * $limit：用来限制MongoDB聚合管道返回的文档数。
 * $skip：在聚合管道中跳过指定数量的文档，并返回余下的文档。
 * $unwind：将文档中的某一个数组类型字段拆分成多条，每条包含数组中的一个值。
 * $group：将集合中的文档分组，可用于统计结果。
 * $sort：将输入文档排序后输出。
 * $geoNear：输出接近某一地理位置的有序文档。
 */

/**
 * 管道操作符实例
 */

/**
 * 1、$project实例
 * db.article.aggregate(
 * { $project : {
 * title : 1 ,                                  #  1的意思是升序   -1  意思是降序的意思
 * author : 1 ,
 * }}
 * );
 * ************************
 * ************************
 * ************************
 * 这样的话结果中就只还有_id,tilte和author三个字段了，默认情况下_id字段是被包含的，如果要想不包含_id话可以这样:
 * db.article.aggregate(
 * { $project : {
 * _id : 0 ,
 * title : 1 ,
 * author : 1
 * }});
 */

/**
 * 2.$match实例
 * ************************
 * ***********************
 * db.articles.aggregate( [
 * { $match : { score : { $gt : 70, $lte : 90 } } },
 * { $group: { _id: null, count: { $sum: 1 } } }
 * ] );
 * *********
 * $match用于获取分数大于70小于或等于90记录，然后将符合条件的记录送到下一阶段$group管道操作符进行处理。
 */

/**
 * 3.$skip实例
 * db.article.aggregate(
 * { $skip : 5 });
 * 经过$skip管道操作符处理后，前五个文档被"过滤"掉。
 */

/**
 * 笔记列表
 */

/**
 * db.mycol.aggregate([{$group : {_id : "$by_user", num_tutorial : {$sum : 1}}}])
 * 以上实例类似sql语句：
 * select by_user as _id, count(*) as num_tutorial from mycol group by by_user
 */

/**
 * 按日、按月、按年、按周、按小时、按分钟聚合操作如下：
 */

/**
 *db.getCollection('m_msg_tb').aggregate(
 * [
 * {$match:{m_id:10001,mark_time:{$gt:new Date(2017,8,0)}}},
 * {$group: {
 *      _id: {$dayOfMonth:'$mark_time'},
 *      pv: {$sum: 1}
 * }
 *   },
 * {$sort: {"_id": 1}}
 * ])
 */

/**
 * 时间关键字如下：
 * $dayOfYear: 返回该日期是这一年的第几天（全年 366 天）。
 * $dayOfMonth: 返回该日期是这一个月的第几天（1到31）。
 * $dayOfWeek: 返回的是这个周的星期几（1：星期日，7：星期六）。
 * $year: 返回该日期的年份部分。
 * $month： 返回该日期的月份部分（ 1 到 12）。
 * $week： 返回该日期是所在年的第几个星期（ 0 到 53）。
 * $hour： 返回该日期的小时部分。
 * $minute: 返回该日期的分钟部分。
 * $second: 返回该日期的秒部分（以0到59之间的数字形式返回日期的第二部分，但可以是60来计算闰秒）。
 * $millisecond：返回该日期的毫秒部分（ 0 到 999）。
 * $dateToString： { $dateToString: { format: , date: } }。
 */