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

## 生命周期

