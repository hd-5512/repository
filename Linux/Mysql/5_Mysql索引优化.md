## 索引基础
- 索引key是存储引擎用于快速找到记录的一种数据结构。它对良好的性能非常关键，数据量越大，对性能的优化越明显，『最优』索引比一个『好的』索引能要好两个数量级，而使用索引本就能轻易提高数个量级。

- 索引的使用，就类似书本的索引，查找对应内容时 先根据索引=>页码=>内容
    
- 索引可以包含一个或多个列的值，如果是多个列，那么顺序也十分重要，因为Mysql只能高效地使用索引最左前列。

#### 索引的类型

- B-Tree索引 Mysql多数存储引擎使用该策略,但是细节又会有不同
  1. MyISAM使用前缀压缩技术使得索引更小，但InnoDB则按照元数据格式进行存储
  2. Mysql索引通过数据的物理位置引用索引，InnoDB则是根据主键引用被索引的行
  3. 虽然InnoDB使用的是B+Tree 但是核心基本一样，BTREE意味着所有值都是按顺序排序的，并且每个叶子页到根的距离相同。
  
- BTREE
  1. BTREE的树 每层仿佛是一个横向的表格 每一节 叫做 节点  一层可以叫做 节点页
  2. 根节点和某个节点页之间可能又有很多层，深度和表的大小直接相关
  3. 每个节点的两边都有 另外节点的地址， 叶子节点指向对应跨度的数据
  4. 可使用BTREE的查询方式
        全值匹配:对某一列，多列按照索引顺序精确匹配
        最左原则:最左的索引列，最左的内容
        匹配范围值:只能对第一列使用
        精确匹配一列+后面范围值
  5. 索引同样用于Order By 中，排序时也需要按照索引查询的使用方式来进行
  6. 索引中某一列如果使用模糊查询 则后面的列就无法被使用了


- 哈希索引  基于哈希表，只有精确匹配索引所有列的查询才能生效
  1. MYSQL中只有 Memory引擎有用  这里暂时跳过
  
- R-tree

- 全文索引 同一个列上可以同时加全文索引和BTREE 用于做搜索引擎业务使用  
  1. 这个需要单独学习    


#### 索引的优点      
    
  1. 索引大大减少了服务器需要扫描的数量
  2. 索引可以帮助服务器避免排序和临时表
  3. 索引可以将随机I/O变为顺序I/O
  4. 索引在中大数据量下对查询的提升十分明显 


#### 如何使用索引

  1. 独立的列  左侧原则 where a + 1 = 2 是一个反面教材
  2. 前缀索引 对长文案设置前缀索引 需要找到合适的“基数” 即对应建索引的前缀位数
     前缀索引更小更快 但却不能用于 ORDER BY 和 GROUP BY
  3. 多列索引


#### 合适的索引列顺序

     假设我们广泛使用的是BTREE索引
     
     1.当不用考虑排序和分组时，将选择性最高的列放在前面是很好的，索引此时的作用只是用于优化WHERE条件的查找
     
       这样的目的是最快的过滤出需要的行
       
     2.实际查询中性能不仅依赖于索引列的选择性(整体基数)
       
       还与数据本身也就是值的分布有关(毕竟实际业务中不是所有数据列都是平均的)
     
       SELECT * FROM payment where payment_id = 2 and user_id = 584;
       
       a.查看一下各个WHERE条件的分支对应的数据基数有多大
       SELECT SUM(payment_id = 2),SUM(user_id = 584) FROM order_info\G
       -SUM(payment_id = 2):7992
       -SUM(user_id = 584):30
       这样来看 如果要执行这个SQL 建立索引时应该就是 user_id在前,payment_id在后的一个多列索引
       
       b.再来看看 对于 user_id 这个条件值，对应的payment_id选择性
       SELECT SUM(payment_id = 2) FROM order_info WHERE user_id = 584\G
       - SUM(payment_id = 2):17
       
       SELECT COUNT(DISTINCT payment_id)/COUNT(*) as payment_selectivity,
              COUNT(DISTINCT user_id)/COUNT(*) as user_selectivity,
              COUNT(*) FROM order_info\G
       - payment_selectivity: 0.0001
       - user_selectivity : 0.3733
       - COUNT(*): 16049
       
       user的选择性更高，所以 最后还是 user_id在前payment_id在后
       ALTER TABLE order_Info ADD KEY(user_id,payment_id)
       
       而上述的都是针对特定数据 比如某个支付 某个用户的查询
       
       c.对于guest用户 网站order_info user_id = 0 是一个特殊值
       那使用上面索引时 拉取大量guest用户信息将会直接出现服务器的性能问题
       通常面对这样的问题  应该做的就是 视为不同的业务需求 使用不同的查询语句和索引
       比如 email
       
       
#### MyISAM 与 InnoBD 数据分布
    
     MyISAM 对于col1,col2 各自建立一颗树
            假设col1是主键，col2是一个索引
     col1树 将索引列的value作为排序分页依据作为 树的Key
            而每插入一条新数据时 MyISAM都给了一个 行号
            通过行号 可以找到对应数据
     col2树 其实和主键一样 自己就能找到对应的ROW          
                

     而InnoDB支持聚簇索引 所以是以完全不同的存储方式去存储同样的数据
     
     虽然也是索引树 单缺将全部数据一同存储在一起的树
     
     主键树每一个叶子结点 都包含了主键的值，事务ID，用于事务回滚的指针以及剩下其他的列的值
     
     即使是前缀索引，存储索引时也是完整主键列
     
     最大的不同就是 它以 主键值 而不是 行指针，这样行移动/数据页重新分页时，索引并不需要进行维护
     
     但是缺点就是占用很多空间
     
     
     而二级索引只是把自己的值作为头部KEY，接着放主键值。
     也就是说 二级索引先找到了主键 然后 再用主键去找 ROW
     
     
     
     
#### 实践

对于一个数量庞大字段复杂的结构表建立索引

1. 排序
   最先考虑的是排序，是使用索引排序，还是先检索数据再排序。
   使用索引排序会严格限制索引和查询的设计，
   因为如果MYsql使用索引进行范围查询，就无法再使用另一个索引，即使是该索引的后续字段
   比如 用户表希望查询时按照分数排序，而WHERE子句的条件里 age BETWEEN 18 AND 25

2. 支持多种过滤条件
   a.检查哪些列具有很多不同的值 
     可选择度高 创建索引后效果明显 可以更有效过滤掉不需要的行
     对于类似time,age等主要使用范围插叙的列 建立字段时默认 我们放在最后
   b.哪些列在WHERE子句用会频繁使用
     比如国家，货币，语言，性别等他们的选择性很低，但是查询时往往都会使用到
     这时可以考虑 创建以他们为前缀的的组合索引，建树多一层，而查询时过滤得更多，在服务层处理就会更少
     对于想要绕过这个索引的场景 比如 性别 还能时用 枚举 sex in ('m','f') 来绕过
     上面这样既可以使用索引 基本还不会降低查询时间
     但是对于国家等很多值 这个绕过方式就不是很可取了
   c.
   
        
     
     
     
     
     
     
       
  
    
  
  
  
