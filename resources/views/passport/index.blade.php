<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 - 团队协作工具</title>
    <script src="https://cdn.bootcss.com/vue/2.5.8/vue.min.js"></script>
    <script src="https://cdn.bootcss.com/element-ui/2.0.5/index.js"></script>
    <link href="https://cdn.bootcss.com/element-ui/2.0.5/theme-chalk/index.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/axios/0.17.1/axios.min.js"></script>
    <title>Laravel</title>
    <style>
        [v-cloak] {
            display: none;
        }

        .text-center {
            text-align: center;
        }

        #app {
            width: 345px;
            margin: 80px auto 0;
        }

        header {
            text-align: center;
        }

        .btn-login {
            width: 100%;
        }
    </style>
</head>
<body>
<div id="app" v-cloak>
    <header>
        <h1>Blade Works</h1>
    </header>
    <el-form v-model="form" @submit.native.prevent="login">
        <el-form-item>
            <el-input type="email" v-model="form.email" placeholder="您的邮箱"></el-input>
        </el-form-item>
        <el-input type="password" v-model="form.password" placeholder="您的密码"></el-input>
        <el-row>
            <el-col :span="5" :offset="19">
                <el-button type="text">忘记密码？</el-button>
            </el-col>
        </el-row>
        <el-form-item>
            <el-button type="primary" native-type="submit" class="btn-login" :loading="isLoading">@{{btnLogin}}
            </el-button>
        </el-form-item>
    </el-form>
    <hr>
    <div class="text-center">
        <el-button type="text">第三方账号登录</el-button>
    </div>
    <div class="text-center">
        <span style="color:#a6a6a6;font-size: 14px;">还没有账号？</span>
        <el-button type="text">创建新账号</el-button>
    </div>
</div>
</body>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                form: {
                    email: "", // 邮箱
                    password: "" // 密码
                },
                isLoading: false, // 加载中
                btnLogin: "登录"
            }
        },
        methods: {
            login() {
                let that = this;
                that.btnLogin = "正在登录...";
                that.isLoading = true;
                axios.post("{:url('index/Passport/login')}", that.form)
                  .then(function (response) {
                      that.isLoading = false;
                      that.btnLogin = "登录";
                      console.log(response.data);
                      if (response.data.status === 1) {
                          window.location.href = response.data.data.redirect_url;
                      } else {
                          that.$message.error(response.data.info);
                      }
                  })
                  .catch(function (error) {
                      that.isLoading = false;
                      console.log(error);
                  });
            }
        }
    })
</script>
</html>
