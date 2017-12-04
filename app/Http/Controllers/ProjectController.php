<?php

namespace App\Http\Controllers;

use App\Http\model\Project;
use Illuminate\Http\Request;

/**
 * 项目控制器
 * @package App\Http\Controllers
 */
class ProjectController
{
    /**
     * 项目首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
//        dump(Project::where(['creator' => $request->session()->get('user_id')])->get());
        return view('project.index');
    }

    /**
     *
     * 为AJAX刷新数据设计的接口
     * @param Request $request
     */
    public function projList(Request $request)
    {
        dump(Project::where(['creator' => $request->session()->get('user_id')])->get());
    }

    /**
     * 创建项目信息
     *
     * POST
     * project_name: 项目名称
     * project_comment: 项目备注
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $project_name = $request->post('project_name');
        $project_comment = $request->post('project_comment');
        $project = new Project();
        $project->setAttribute('project_name', $project_name);
        $project->setAttribute('project_comment', $project_comment);
        $project->setAttribute('creator', $request->session()->get('user_id'));
        $project->setCreatedAt(time());
        $project->setUpdatedAt(time());
        if ($project->save()) {
            return response()->json([
                'info' => '创建成功',
                'status' => 1,
                'redirect_url' => url('project')
            ]);
        }
        return response()->json([
            'info' => '创建失败',
            'status' => 0
        ]);
    }

    /**
     * 获取指定项目信息
     *
     * GET
     * @param Request $request
     */
    public function read(Request $request)
    {
        $project_id = $request->get('project_id');
    }

    /**
     * 编辑项目信息页面
     * @param Request $request
     */
    public function edit(Request $request)
    {
        $project_id = $request->get('project_id');

    }

    /**
     * 更新项目信息
     *
     * PUT
     * @param Request $request
     */
    public function update(Request $request)
    {
        $project_id = $request->input('project_id');
    }

    /**
     * 删除项目
     *
     * DELETE
     * @param Request $request
     */
    public function delete(Request $request)
    {

    }
}
