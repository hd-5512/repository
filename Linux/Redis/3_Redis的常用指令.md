- Redis命令不分大小写 但实际操作中和Mysql一样 习惯使用大写来表示命令
##### CONFIG @OPTION @key @value  动态修改配置信息

CONFIG GET @key 

CONFIG SET loglevel warning

## 字符串指令

##### KEYS @pattern  @return list or empty

查询数据库中有哪些键

这个在生产环境用得比较少  因为会遍历所有的键 当键数量较多时会影响性能

##### EXISTS @key    @return integer 1/0

判断一个键是否存在

##### DEL @key   @return integer 1/0

删除一个键

内部已经有检查和容错  如果已删除 同样返回 0 

DEL 本身不支持通配符  但是可以利用Linux管道结合xargs来执行比如

`redis-lic KEYS "user:*" | xargs redis-cli DEL`

##### TYPE @key

查询数据的类型 返回 string / list / hash / set / zset


##### SET @key @value / GET @key   字符串操作获取和赋值

MSET/MGET 就是多项操作

##### INCR @key @return intger 返回递增后的结果  递增数字 increase

不存在时 默认生成 0 然后递增返回 结果就是 1 

当key的类型不是 数值类型 就会报错 返回一个 (ERR)消息


##### DECR @key 递减

递增和递减 都可以再后面继续设置 step 阶数来控制



##### APPEND @key @value 在key的末尾加上value
##### STRLEN @key 获取字符串字节长度  
注意UTF8的中文字可能一个就有3字节

## 散列表指令

#### HSET / HGET @key @field @value

HMSET / HMGET @key @field

HGETALL @key 可以获取散列表中整行数据

HEXISTS @key @field

HLEN @key 获取数据数量

HDEL @key @field 删除数据

## 列表指令
- 列表是一个有序的字符串列表 对两端的操作都是O(1) 边缘化的数据操作比较快
- 因为查询通过节点顺序遍历  所以适合运用在队列场景 或者 类似微博朋友圈这种关心最新动态的场景  

#### LPUSH / RPUSH @key  @value1 .... 依次往左右两端加入数据

返回的是插入后列表中的元素数量

LPOP / RPOP @key 出队列  返回的是 弹出的值

LLEN @key  获取长度


#### LRANGE @key @start @stop

支持 0  -  x  也支持  -2  -1 这样从右边开始




## 集合SET 有序集合ZSET
因为集合的底层利用散列表 所以集合的操作通常为O(1)
#### SADD / SREM @key @values .... 

SMEMBERS @key 查询有哪些集合

SISMEMBER @key @value 查询在某个集合中是否有这个值

#### 集合运算 
- SDIFF 求差集合 

SADD setA 1 2 3 
 
SADD setB 2 3 4 

SDIFF setA setB  =  1

SDIFF setB setA  =  4 

- SINTER 求交集 SUNION 求并集

SINTER setA setB  = {2,3}

SUNION setA setB = {1,2,3,4}


- SCARD 获取集合总数

- SDIFFSTORE @dest @key1 @key2 .... 集合运算后用另外的key进行存储


#### 有序集合

- ZADD @key @score @member  添加的时候通过score进行排序

- ZSCORE 和 ZRANGE 用来设置权和获取权值范围的


























 

 