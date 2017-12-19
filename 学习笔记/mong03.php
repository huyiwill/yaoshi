<?php
/**
 * MongoDB 条件操作符
 * 描述:
 * 条件操作符用于比较两个表达式并从mongoDB集合中获取数据。
 */

/**
 * 笔记列表
 * $gt --------     greater than      >
 * $gte ---------   gt equal         >=
 * $lt --------     less than        <
 * $lte ---------   lt equal         <=
 * $ne -----------  not equal        !=
 * $eq  --------    equal             =
 */

/**
 * MongoDB中条件操作符有：
 * (>) 大于 - $gt
 * (<) 小于 - $lt
 * (>=) 大于等于 - $gte
 * (<= ) 小于等于 - $lte
 */

/**
 *MongoDB (>) 大于操作符 - $gt
 * 如果你想获取 "col" 集合中 "likes" 大于 100 的数据，你可以使用以下命令：
 * db.col.find({"likes" : {$gt : 100}})
 * 类似于SQL语句：
 * Select * from col where likes > 100;
 */

/**
 * MongoDB（>=）大于等于操作符 - $gte
 * 如果你想获取"col"集合中 "likes" 大于等于 100 的数据，你可以使用以下命令：
 * db.col.find({likes : {$gte : 100}})
 * 类似于SQL语句：
 * Select * from col where likes >=100;
 */

/**
 * MongoDB (<) 小于操作符 - $lt
 * 如果你想获取"col"集合中 "likes" 小于 150 的数据，你可以使用以下命令：
 * db.col.find({likes : {$lt : 150}})
 * 类似于SQL语句：
 * Select * from col where likes < 150;
 */

/**
 * MongoDB (<=) 小于操作符 - $lte
 * 如果你想获取"col"集合中 "likes" 小于等于 150 的数据，你可以使用以下命令：
 * db.col.find({likes : {$lte : 150}})
 * 类似于SQL语句：
 * Select * from col where likes <= 150;
 */

/**
 * MongoDB 使用 (<) 和 (>) 查询 - $lt 和 $gt
 * 如果你想获取"col"集合中 "likes" 大于100，小于 200 的数据，你可以使用以下命令：
 * db.col.find({likes : {$lt :200, $gt : 100}})
 * 类似于SQL语句：
 * Select * from col where likes>100 AND  likes<200;
 */