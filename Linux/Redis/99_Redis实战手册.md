### 配置开机启动
```
vi /etc/rc.local 最后加入 /root/4setup/redis-2.2.12/src/redis-server
```
### 配置文件 redis.conf 常用基础
```
daemonize: yes 后台运行 默认为 no
port:监听端口 默认 6379

pidfile: 一台机器配置多个redis 需要配置执行不同的pid文件和端口  默认 /var/run/redis.pid
bind: 只接受来自某ip的请求 生产设置后更加安全

timeout:设置客户端的超时时间 如果时间段内没有再发出任何指令则断开连接

slaveof 指定为其他库的从库

masterauth 密码验证
requirepass 由于一个客户端一秒钟可以进行150次密码尝试 需要一个比较强力的密码来防止暴力破解

loglevel: debug, verbose, notice, 和warning 生产一般开启notice
logfile:日志文件位置
```
### 默认的镜像备份或AOF同步
```
dbfilename 镜像备份的文件名
dir 镜像备份路径
save
    设置Redis 进行数据库镜像的频率。
    if(在60 秒之内有10000 个keys 发生变化时){
    进行镜像备份
    }else if(在300 秒之内有10 个keys 发生了变化){
    进行镜像备份
    }else if(在900 秒之内有1 个keys 发生了变化){
    进行镜像备份
    }


appendonly 默认关闭 开启后 会把每次昔日的操作命令写成文件 appendonly.aof 重启时不容易丢失数据
但是相比镜像文件会大很多 需要在低压力状态通过 BGREWRITEAOF 进行整理
线上一般关闭镜像备份 而使用AOF 定时重写整理

appendfsync 设置对appendonly.aof 文件进行同步的频率。always 表示每次有写操作都进行同步，
everysec 表示对写操作进行累积，每秒同步一次。这个需要根据实际业务场景进行配置
```
### 性能与连接
```
maxclients 限制同时连接的客户数量
maxmemory 限制最大内存
```

## 数据类型
### hashes
```
特别适合存储对象(key 为string value 也为string的数据)
它的添加和删除操作都是O(1) 也方便直接存取整个对象
且占用更少的内存(原因是一开始使用了一个zip来替代通用的元数据) 
```

## 事务与乐观锁机制
```
事务对一个client有效 multi+exec  / discard 取消

乐观锁 watch KEY 可以避免在一个事务过程中 因为其他数据先行修改后 导致本身修改误差 覆盖他人修改

先watch 后 开启mutil事务 执行exec 如果返回 错误nil 则说明执行失败 需要重新操作

exec,discard,unwatch 都会清除监视

需要注意的是  Redis事务和关系型事务目前有一个较大缺陷

一连串的命令在exec提交时 如果中间一个失败 则依然其他命令会提交成功

也是为什么Redis事务使用较少的原因
```

### PUB/SUB 发布与订阅 配置方便 但是PUB/SUB机器就不会接收其他命令了











