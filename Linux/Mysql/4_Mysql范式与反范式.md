## 范式的概念

   范式的标准是列的唯一性，关联查找是它的实际用例

#### 范式的优点

- 范式化的更新操作通常比反范式化要快

- 当数据较好地范式化，就只有很少或者没有重复数据，所以业务上只需要修改更少的数据

- 范式化的表通常更小，可以更好地放在内存里，所以执行操作更快

- 很少有多余的数据意味着检索列表数据时更少需要DISTINCT或GROUP BY语句

#### 范式化的缺点

- 范式化设计最大的缺点就是通常需要关联，代价昂贵，页可能使一些索引策略失效

- 比如范式化将列存放在不同的表中，而这些列如果在一个表中可是设计与同一个索引


#### 反范式化

- 优点是不需要关联表，如果不需要关联，对大部分查询最差的情况，即使没有使用索引进行全表扫描。
    
    当数据比内存大(这样表已经很大）时这可能比关联要快得多，因为这样避免了随机I/O

- 单独的表能够更有效得使用索引策略
    
        范式设计 将两个表关联查询 最近10条vip用户得message信息
    
        SELECT message_text,user_name FROM message 
            INNER JOIN user ON message.user_id = user.id 
        WHERE user.accout_type = 'vip' 
        ORDER BY message.published DESC LIMIT 10
        
        1.Mysql先扫描message表中的published字段索引获取倒序得消息列表 
                => 然后对每一条再去user表中检查这个用户是不是 vip 
         如果只有一小部分是付费账户 这么做的效率就很低
        
        2.另一种可能是从user表开始，找到所有的付费用户，再获取他们的所有信息并且排序，但这可能更加糟糕
        
        
        -----------
        
        非范式设计在 message 表中直接存放用户信息
        
        SELECT message_text,user_name FROM user_messages
        WHERE account_type = 'vip' 
        ORDER BY published DESC 
        LIMIT 10 
        
        当添加一个索引 (account_type,published) => Mysql通过索引找到 vip 区域的列并直接根据索引倒序获取前10published的id 再去获取列表中的内容
        
#### 混用政策

- 可以范式化的schema,反范式化的缓存或者复制 使用触发器更新缓存值或使用Nosql

- 从业务角度考虑 反范式化的使用 需要场景是 冗余的字段优化了查询 但更新的代价会很高

- 避免过度设计，小和简单依然是第一宗旨，关联时使用相同的数据类型 
    
- 如果要修改表 注意 ALTER TABLE 多数情况下 都会锁表 然后重建整张表  MODIFY / CHANGE / ALTER COLUMN 三中操作的方式都是不同的， ALTER COLUMN 设置默认值 可以只修改.frm文件 而不用重建表


