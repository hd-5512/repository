## 创建实例

每个vue页面都需要建立一个 vue 实例
```
    var vm = new Vue({
        // 选项
    })
```

从下面可以观察到 window下的data 和 vm 是一致的
```
    var data = { a: 1 }
    var vm = new Vue({
        data: data
    })
    vm.a == data.a // => true
    
    
     // 设置属性也会影响到原始数据
    vm.a = 2
    data.a // => 2

    // ……反之亦然
    data.a = 3
    vm.a // => 3
```
当这些数据改变时，视图会进行重渲染。

值得注意的是只有当实例被创建时 data 中 **存在的属性 ** 才是响应式的。也就是说如果你添加一个新的属性,页面不会变化

Vue提供一些$符号的API来区分外部和实例内的对象
```
var data = { a: 1 }
var vm = new Vue({
  el: '#example',
  data: data
})

vm.$data === data // => true
vm.$el === document.getElementById('example') // => true

// $watch 是一个实例方法
vm.$watch('a', function (newValue, oldValue) {
  // 这个回调将在 `vm.a` 改变后调用
})
```

## 生命周期以及选项属性
methods:  封装的方法
created:   创建实例
mounted:  DOM加载完成开始渲染
computed:   计算属性  建议多使用它

```
var vm = new Vue({
    el: '#app',
    data: {
            message: 'Hello',   // 只有这里有的才是监听绑定的字段   设置默认值
            a:123
    },
    created: function () {
        console.log('a is: ' + this.a)  // a  is 123   钩子中使用的 this 都是指代 vm 这个实例
        this.init()                                //执行 methods 中的 init 方法
        console.log('a is: ' + this.a)  // a  is  456
    }，
    methods: {
        init:function(){
            this.a = 456
        },
        reversedMessage: function () {
            return this.message.split('').reverse().join('')
        }
    }，
    computed: {
        reversedMessage: function () {                              // 计算属性的 getter  另外还有setter(即传入参数来手动修改数据的形式)
          return this.message.split('').reverse().join('')      // computed 对比 methods 中的优势就是 methods 需要调用  而计算属性会自动触发                                                                              
        }                                                                                // 并且它自己具有缓存的能力  只有在里面 this.data 中的数据发生变化后才会重新计算
    }
})
```

mixins: 混入    合并数据到 data以及methods还有组建中   可以当做是全局变量和方法的设置地点

watch:  对于计算属性中非常复杂 甚至有 异步请求过程时使用

props:  子组件的接受 父组件传入的值

<component   ref="" >   & $ref  父组件通过它 查看子组件的数据

<solt>: 插槽  用于灵活替换组建中的内容

<keep-alive> 


