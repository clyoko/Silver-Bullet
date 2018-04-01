<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>任务 - {{ \App\Http\Middleware\ViewTempleteVal::$projectName }} - 团队协作平台</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <style>
        html, body {
            background: #e7eaf1;
            font-family: "Helvetica Neue", Helvetica, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "微软雅黑", Arial, sans-serif;
        }

        [v-cloak] {
            display: none;
        }

        /*整页采用flex纵向布局，实现页脚自适应*/
        .page {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 100vh;
        }

        /*页面主体样式*/
        main {
            display: flex;
            flex: 1;
            padding: 15px 20px 0;
        }

        /*页面主题各部分内容*/
        section {
            flex: 1;
            width: 1000px;
            margin: 0 auto;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #fff;
            box-sizing: border-box;
        }

        /*section之间的间距*/
        section + section {
            margin-top: 20px;
        }

        section .title {
            display: flex;
            justify-content: space-between;

        }

        section .title span {
            display: block;
            font-size: 18px;
            padding: 12px 0;
        }
    </style>
</head>
<body>
<div id="app" v-cloak>
    <div class="page">
        <header-nav :header-data="headerData"></header-nav>
        <second-nav :second-nav-data="secondNavData"></second-nav>
        <main>
            <section>
                <task-item :task-item-data="taskItemData"></task-item>
            </section>
        </main>
        <footer-component></footer-component>
    </div>
</div>
</body>
<script src="{{ url(mix('js/task.js')) }}"></script>
@include('public.vue_value')
<script>
    let myapp = new Vue({
        el: '#app',
        data() {
            return {
                // 传给子组件数据
                // 导航条数据
                headerData: headerData,
                // 二级导航数据
                secondNavData: secondNavData,
                taskItemData: {
                    project_id: "{{ \App\Http\Middleware\ViewTempleteVal::$projectId }}",
                    myTaskUrl: "{{ route('task/my', ['protect_id'=>\App\Http\Middleware\ViewTempleteVal::$projectId]) }}",
                    listTaskUrl: "{{ route('task/index', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    createTaskUrl: "{{ route('task/save', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    finishTaskUrl:"{{ route('task/finish', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    deleteTaskUrl:"{{ route('task/delete', \App\Http\Middleware\ViewTempleteVal::$projectId) }}"
                },
            }
        },
        methods: {},
        // vue生命周期
        mounted: function () {
            this.secondNavData.defaultActive = "任务";
        }
    })
</script>
</html>
