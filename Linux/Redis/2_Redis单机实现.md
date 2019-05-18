## Redis结构
Redis有 RedisSever 和 RedisClient

#### redisServer结构
- redisDb 数据库 是一个数组 默认16个 Db\[0\] Db\[1\] ...
- redisClient可以通过SELECT指令来修改redisDb的指针进行切换数据库 比如 SELECT 2 => redis\[2\]> ... 
- dbnum 数据库的数量 默认16

#### redisDb结构
- dict 字典区域或键空间 就是实际存放数据的地方 里面存放采用的就是hashtable
- expires 过期时间列表 也是字典结构  key => long 精确到毫秒的过期时间戳 (底层)


#### 过期KEY的清理方案
- 惰性删除 在几乎所有的命令中 redis都会去检查对应的有效期 如果过期了就删除并返回null
- 定期删除 通过记录一个db或位置 随机进行阶段性的清理来避免造成浪费太多内存的内存泄露和CPU占用过多的问题


#### RDB持久化方案
- redis db 因为存储在内存里 系统关闭重启就可能会造成数据丢失

- 于是可以通过SAVE 或 BGSAVE 去创建一个 RDB文件 将数据存储在文件中 启动时读取

- SAVE 是一个阻塞的命令 一旦执行 将不接受任何client端的其他命令 

- 于是多数情况下推荐 BGSAVE 而存储和载入是都会忽略已过期的key

- 但是如果是主从复制情况 slave机不会过滤过期key 而是等待 master 发出del指令后同步

#### AOF持久化
- 因为AOF类似mysql数据库的命令记录  它把所有的修改数据指令都存在文件中

- AOF重写 因为过期key等问题 可能会出现多次 生成和删除指令 一段时间需要检查合并 来减少AOF的记录数


#### 数据库通知
- 根据配置 当redis服务器修改某一个配置的时候 会向客户端发送数据库通知





 