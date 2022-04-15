> 此仓库为学习用例，不建议用于生产环境

## 执行方法

### 软件/工具依赖
- php-7.4
- swoole-4.8.5
- php ext:protobuf-3.19.1
- protoc-3.6.1

### 执行步骤
1. 根据 `hello.proto` 生成 php 代码
```shell
protoc --php_out=./ hello.proto
```

2. 启动服务端
```shell
php7.4 server.php
```

3. 启动客户端
```shell
php7.4 client.php
```

4. 查看结果
> 如果执行客户端能看到 Hello xxx Fri, 15 Apr 2022 10:37:23 +0800，说明成功通信了

### 排错
如果出现类找不到的情况，需要手动执行一下 `composer dump-autoload`