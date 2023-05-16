# kibana-keycloak

## 运行elk

> 基于 [docker-elk](https://github.com/deviantony/docker-elk.git) 搭建elk环境

### 启动elk服务

1. 关闭oidc  
   打开`elasticsearch/config/elasticsearch.yml`文件  
   修改`xpack.security.authc.realms.oidc.oidc1.enabled`为`false`
2. 安装配置并启动服务
    ```bash
    docker compose up setup
    docker compose up -d
    ```
3. 配置client_secret
    ```bash
    # 进入容器
    docker compose exec elasticsearch bash
    #在容器内部执行
    bin/elasticsearch-keystore add xpack.security.authc.realms.oidc.oidc1.rp.client_secret
    ```
4. 开启oidc  
   打开`elasticsearch/config/elasticsearch.yml`文件  
   修改`xpack.security.authc.realms.oidc.oidc1.enabled`为`true`
5. 重启服务
    ```bash
    docker compose restart
    ```
6. 访问
    访问 http://127.0.0.1:5601/ 即可看到kibana页面
    > 用户名: elastic  
    密码: changeme
