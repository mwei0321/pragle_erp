## 容器仓库 (最新:latest)

> registry.cn-hongkong.aliyuncs.com/domedeahk/pragle_erp_api

## 暴露端口

| port | 说明          |
|------|-------------|
| 8080 | api         |

### 开发

开发时可以先编译runtime镜像然后在编译erp镜像，里面配置好环境了，外挂自己电脑的目录就能开发
```make build-runtime && make build```

运行（参数根据实际情况更改）

```shell
docker run -d -v "${PWD}":/var/www/html -p 80:8080 registry.cn-hongkong.aliyuncs.com/domedeahk/pragle_erp_api:latest
```

### 编译docker

```shell
make build
```

如果没有runtime的话先去```make build-runtime```编译好运行环境 然后再```make build```。它会把当前项目打包进镜像内。完了直接推上即可

### 环境变量

| name | 说明        | 例子                                              |
|------|-----------|-------------------------------------------------|
|   MYSQL_DB_DSN   | erp库      | mysql:host=127.0.0.1;port=3306;dbname=erp       |
|   MYSQL_DB_DATA_DSN   | data库     | mysql:host=127.0.0.1;port=3306;dbname=db_data   |
|   MYSQL_USERNAME                   | 数据库用户名    | root                                            |
|   MYSQL_PASSWORD                   | 数据库密码     | led20131111                                     |

### 定时任务

需要定时任务的可以把定时任务写在根目录的cron文件里。容器启动会自动运行
