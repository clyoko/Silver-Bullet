<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>设置 - {{ \App\Http\Middleware\ViewTempleteVal::$projectName }} - 团队协作平台</title>
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
                <setting-item :setting-item-data="settingItemData"></setting-item>
            </section>
        </main>
        <footer-component></footer-component>
    </div>
</div>
</body>
<script src="{{ url(mix('js/app.js')) }}"></script>
@include('public.vue_value')
<script>
    let myapp = new Vue({
        el: '#app',
        data() {
            return {
                // 导航条数据
                headerData: headerData,
                // 二级导航数据
                secondNavData: secondNavData,
                // 项目设置数据
                settingItemData: {
                    project_id: "{{ \App\Http\Middleware\ViewTempleteVal::$projectId }}",
                    project_name: "{{ \App\Http\Middleware\ViewTempleteVal::$projectName }}",
                    project_comment: "{{ $project->project_comment }}",
                    project_githuburl: "{{ $project->githuburl }}",
                    project_thumb: "{{ asset('app/' . $project->project_thumb) }}",
                    projectIndexUrl: "{{ url('project') }}",
                    getMemberListUrl: "{{ route('member/index', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    getInviteCodeUrl: "{{ route('member/genInviteCode', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    updateNameAndCommentUrl: "{{ route('project/updateNameAndCommentUrl', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    updateThumbUrl: "{{ route('project/updateThumb', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    githubUrl: "{{ route('project/github', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    removeMemberUrl: "{{ route('member/remove', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                    removeProjUrl: "{{ route('project/delete', \App\Http\Middleware\ViewTempleteVal::$projectId) }}",
                },
            }
        },
        mounted() {
            this.secondNavData.defaultActive = "设置";
        }
    })
</script>
</html>