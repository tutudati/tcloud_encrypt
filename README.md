## 仓库说明

该仓库是基于PHP封装的一个项目域名验证类库。

## 如何生成ssl证书

前提是保证你本地安装了openssl命令行工具，使用如下的命令就能生成对应的公钥和私钥证书。

```shell
# 生成私钥
openssl genrsa -out private_key.pem 2048

# 从私钥提取公钥
openssl rsa -in private_key.pem -pubout -out public_key.pem
```

