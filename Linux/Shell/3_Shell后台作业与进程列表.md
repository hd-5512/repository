#### 1.父子Shell
    用于登录某个虚拟控制器终端或在GUI中运行终端仿真器时所启动的默认的交互shell，是一个父shell。
    父shell提供CLI提示符$，然后等待命令输入。
    
    在CLI提示符后输入/bin/bash命令或其他等效的bash命令时，会创建一个新的shell程序,这个shell程序被称为子shell（child shell）。
    子shell也拥有CLI提示符，同样会等待命令输入。


- 使用 ps --forest 可以直观查看到 他们的父子关系    
```
$ ps -f 
UID PID PPID C STIME TTY TIME CMD 
501 1841 1840 0 11:50 pts/0 00:00:00 -bash 
501 2532 1841 1 14:22 pts/0 00:00:00 ps -f 
$ 
$ bash 
$ 
$ bash 
$ 
$ bash 
$ 
$ ps --forest 
PID TTY TIME CMD 
1841 pts/0 00:00:00 bash 
2533 pts/0 00:00:00    \_ bash 
2546 pts/0 00:00:00      \_ bash 
2562 pts/0 00:00:00        \_ bash 
2576 pts/0 00:00:00          \_ ps 
$
```


- 使用 ps -f 也可以通过 PID PPID 来找到对应的parentPID 和 PID 看到他们的父子关系
```
$ ps -f 
UID PID PPID C STIME TTY TIME CMD 
501 1841 1840 0 11:50 pts/0 00:00:00 -bash 
501 2533 1841 0 14:22 pts/0 00:00:00 bash 
501 2546 2533 0 14:22 pts/0 00:00:00 bash 
501 2562 2546 0 14:24 pts/0 00:00:00 bash 
501 2585 2562 1 14:29 pts/0 00:00:00 ps -f 
$ 
``` 

- 利用exit命令有条不紊地退出子shell
```
当然cli上的父shell执行 exit 了之后 自然就等于关闭cli窗口
```
借助环境变量 $BASH_SUBSHELL 可以查看当前运行的 shell id  
```
$ echo $BASH_SUBSHELL    //大于0的都是子shell
0
$
$ bash                  //当手动执行开启一个bash  cli界面并不会有什么特别的表现 但是实际上已经是开启了一个子shell
$
$ echo $BASH_SUBSHELL
1
$
$ bash
$
$ echo $BASH_SUBSHELL
2
```
- 就算是不使用bash shell命令也可以生成子shell，就是使用进
程列表和运行shell脚本。


#### 2.进程列表
- 使用分号可以让一行命令在cli上依次运行一系列命令 可以使被叫做 命令列表
```
$ pwd ; ls ; cd /etc ; pwd ; cd ; pwd ; ls 
/home/Christine 
Desktop Downloads Music Public Videos 
Documents junk.dat Pictures Templates 
/etc 
/home/Christine 
Desktop Downloads Music Public Videos 
Documents junk.dat Pictures Templates 
$
```
- 使用括号 可以让一串命令(命令列表)变成一个进程列表
```
$ (pwd ; ls ; cd /etc ; pwd ; cd ; pwd ; ls) 
/home/Christine 
Desktop Downloads Music Public Videos 
Documents junk.dat Pictures Templates 
/etc 
/home/Christine 
Desktop Downloads Music Public Videos 
Documents junk.dat Pictures Templates 
$
```
尽管多出来的括号看起来没有什么太大的不同，但起到的效果确是非同寻常。括号的加入使命令列表变成了进程列表，生成了一个子shell来执行对应的命令。

进程列表是一种命令分组（command grouping）。

- 另一种命令分组是将命令放入花括号中，并在命令列表尾部加上分号（;）。
    语法为{ command; }。但使用花括号进行命令分组并不会像进程列表那样创建出子shell。

#### 3.后台模式

    在后台模式中运行命令可以在处理命令的同时让出CLI，以供他用。演示后台模式的一个经典命令就是sleep。
    
    要想将命令置入后台模式，可以在命令末尾加上字符&。

- 开启一个后台作业 
    使用& 可以让 **一个命令** 或者 **一个进程列表** 都变成一个后台作业

```
$ (sleep 2 ; echo $BASH_SUBSHELL ; sleep 2)& 
[2] 2401 
$ 1 
[2]+ Done ( sleep 2; echo $BASH_SUBSHELL; sleep 2 ) 
$
```

- 查看后台作业  
    可以使用 jobs 或者 jobs -l
```
$ jobs -l 
[1]+ 2396 Running sleep 3000  // [1] 代表后台作业的task序号   -l 可以让我们看到进程的PID[2396]  然后是状态和执行的命令行
$ 
...
[1]+ Done sleep 3000 &           //一旦后台作业完成，就会显示出结束状态, 所以如果当前cli有一个后台作业 突然冷不丁冒出一个Done也就代表作业完成了
$
```



#### 4.协程
- 使用 coproc 命令就可以创建一个协程
```
$ coproc sleep 10 
[1] 2544 
$ 
//除了会创建子shell之外，协程基本上就是将命令置入后台模式。
//当输入coproc命令及其参数之后，你会发现启用了一个后台作业。
//屏幕上会显示出后台作业号（1）以及进程ID（2544）。
//jobs命令能够显示出协程的处理状态。
$ jobs 
[1]+ Running coproc COPROC sleep 10 & 
$
//
```

- 多个协程
```
//默认一个 coproc 会为自己取一个名字 COPROC 用来作为识别 而进行通信
//你可以这样定义自己的协程

$ coproc My_Job { sleep 10; } 
[1] 2570 
$ 
$ jobs 
[1]+ Running coproc My_Job { sleep 10; } & 
$

!!! 
    1.必须确保在第一个花括号（{）和命令名之间有一个空格。
    2.还必须保证命令以分号（;）结尾。
    3.分号和闭花括号（}）之间也得有一个空格。 
!!!
```


#### 5.Shell的内建命令和外部命令
- 外部命令 有时候也被称为文件系统命令 并不是shell程序的一部分
    比如 于/bin、/usr/bin、/sbin或/usr/sbin  可以通过 which ps 或者 type -a ps  ls -l /bin/ps 来看到
```
    外部命令相比内建命令会有一定的代价 
    因为执行外部命令 就会创建出一个子进程 (这个过程叫做 forking 衍生 它需要花费精力去设置新子进程的环境)
```

- 内建命令 他们不需要使用子进程因为他们已经和shell编译成了一体
    比如 cd exit ...
    同样 使用 type cd  可以了解是否是内建的
 ```
 $ type cd 
cd is a shell builtin
```





