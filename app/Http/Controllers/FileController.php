<?php

namespace App\Http\Controllers;

use App\Http\model\Account;
use App\Http\model\DirStructure;
use App\Http\model\ProjectFile;
use Illuminate\Http\Request;

/**
 * 文件模块控制器
 * @package App\Http\Controllers
 *
 * 文件系统设计：
 *
 * 本文件系统采用JSON表示文件目录树结构
 * 对于任意一个文件夹均有：
 * path: 文件夹完整路径,如："全部文件/工作室文件/A项目"
 * label: 当前文件夹名，如："A项目"
 * children: 子目录JSON，如：[{"path":"","label":""}]
 *
 * 具体定义：
 * path: 可以唯一地标识目录树中的任意一个文件夹，要求同一个文件夹下不能有相同的文件名
 * label: 仅仅是文件夹名，用于显示，要求同一个文件夹下不能有相同的文件名
 */
class FileController extends Controller
{
    /**
     * 首页
     * @param Request $request
     * @param string $project_id 项目id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $project_id)
    {
        return view('file.index', [
            'project_id' => $project_id
        ]);
    }

    /**
     * 文件上传
     * @param Request $request
     * @param string $project_id 项目id
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, $project_id)
    {
        // 获取文件放置的虚拟目录信息
        $virtual_path = $request->post('virtual_path');
        // 获取文件对象
        $file = $request->file('myfile');
        if (!$file) {
            return response()->json(['info' => '未选择文件', 'status' => 0]);
        }
        // 检查文件上传错误码
        if ($file->isValid()) {
            // 存储文件，返回相对路径，如：myfile/Y8aWLJC5yitb3Ou41Jy9MK8E75OW3yJ1cingsD1K.txt
            $relative_path = $file->store('myfile');
            // 成功移动文件的情况
            if ($relative_path) {
                // 写入文件信息
                $project_file = new ProjectFile();
                $project_file->setAttribute('creator', session('user_id'));
                $project_file->setAttribute('project_id', $project_id);
                $project_file->setAttribute('virtual_path', $virtual_path);
                $project_file->setAttribute('relative_path', $relative_path);
                $project_file->setAttribute('original_name', $file->getClientOriginalName());
                $project_file->setAttribute('hash_name', $file->hashName());
                $project_file->setAttribute('file_size', $file->getSize());
                $project_file->save();
                return response()->json([
                    'info' => '上传成功',
                    'status' => 1,
                    'data' => [
                        'fileUrl' => asset('app/' . $relative_path)
                    ]
                ]);
            }
        }
        return response()->json([
            'info' => '上传失败，错误码：' . $file->getErrorMessage(),
            'status' => 0
        ]);

    }

    /**
     * 预览目录
     *
     * 预览指定项目、指定虚拟路径的目录树
     *
     * POST
     * virtual_path: 虚拟路径，如"全部文件/项目资料"
     *
     * @param Request $request
     * @param string $project_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function previewDir(Request $request, string $project_id)
    {
        $virtual_path = $request->post('virtual_path');
        $project_file_list = ProjectFile::join(Account::table,
            Account::table . '.user_id',
            '=',
            ProjectFile::table . '.creator')
            ->select([
                ProjectFile::table . '.original_name',
                ProjectFile::table . '.updated_at',
                Account::table . '.user_name as creator_name',
                ProjectFile::table . '.file_size'
            ])
            ->where('project_id', $project_id)
            ->where('virtual_path', $virtual_path)
            ->get();
        foreach ($project_file_list as &$val) {
            $short_size = $val['file_size'];
            // 精简文件大小显示，按B、KB、MB、GB的方式显示
            if ($short_size >= 0 && $short_size < 1024) { // B
                $val['file_size'] = floor($short_size) . 'B';
            }
            if ($short_size >= 1024 && $short_size < 1048576) { // KB
                $val['file_size'] = floor($short_size / 1024) . 'KB';
            }
            if ($short_size >= 1048576 && $short_size < 1073741824) { // MB
                $val['file_size'] = floor($short_size / 1024 / 1024) . 'MB';
            }
            if ($short_size >= 1073741824) { // GB
                $val['file_size'] = floor($short_size / 1024 / 1024 / 1024) . 'GB';
            }
        }
        return response()->json([
            'info' => '获取成功',
            'status' => 1,
            'data' => $project_file_list
        ]);

    }

    /**
     * 获取目录树
     *
     * 获取指定项目的文件目录树
     *
     * @param Request $request
     * @param string $project_id 项目id
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree(Request $request, $project_id)
    {
        $dirStructure = DirStructure::where('project_id', intval($project_id))->first();
        return response()->json([
            'info' => '获取成功',
            'status' => 1,
            'data' => json_decode($dirStructure['structure'])
        ]);
    }

    /**
     * 保存新目录
     *
     * 在指定虚拟路径下创建文件。如"全部文件"下创建"项目资料"，最终得到"全部文件/项目资料"
     *
     * POST
     * new_dir: 新文件夹名
     * virtual_path: 虚拟路径
     *
     * @param Request $request
     * @param string $project_id 项目id
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveDir(Request $request, $project_id)
    {
        $new_dir = $request->post('new_dir');
        $virtual_path = $request->post('virtual_path');
        if (empty($new_dir) || empty($virtual_path)) {
            return response()->json(['info' => '新文件夹名和路径不能为空', 'status' => 0]);
        }

        $new_dir_data = [
            'path' => implode('/', [$virtual_path, $new_dir]),
            'label' => $new_dir
        ];

        $dirStructure = DirStructure::where('project_id', $project_id)->first();
        $structure = json_decode($dirStructure['structure'], true);
        try {
            // 插入目录
            $this->addNodeToStructure($structure, $virtual_path, $new_dir_data);
        } catch (\Exception $exception) {
            return response()->json(['info' => '创建失败:' . $exception->getMessage(), 'status' => 0]);
        }
        // 写入记录
        $dirStructure->structure = json_encode($structure);
        if ($dirStructure->save()) {
            return response()->json([
                'info' => '创建成功',
                'status' => 1,
                'data' => $structure
            ]);
        }
        return response()->json(['info' => '创建失败:服务器写入错误', 'status' => 0]);
    }

    /**
     * 在指定目录路径下创建新目录
     *
     * 广度优先搜索算法（BFS）
     *
     * @param array $structure 目录树数组
     * @param string $needle 指定目录路径（注意是已存在的路径，新建的文件夹会在这个路径下）
     * @param array $new_dir_data 新文件夹信息，包括这些字段['path','label']
     * @return bool 插入成功为true，失败为false
     * @throws \Exception
     */
    private function addNodeToStructure(array &$structure, string $needle, array $new_dir_data)
    {
        $queue = new \SplQueue();
        // 判空，原始目录树的根节点不能为空
        if (count($structure) === 0) {
            throw new \Exception('文件目录树为空');
        }
        // 写入队列
        foreach ($structure as $key => $val) {
            $obj = new \stdClass();
            $obj->data = &$structure[$key];
            $queue->enqueue($obj);
        }
        // 开始自动化操作
        while (!$queue->isEmpty()) {
            $obj = $queue->dequeue();
            // 检查是否当前为当前路径
            if ($obj->data['path'] === $needle) {
                // 有子目录的，先检查是否有同名文件夹，已存在的则抛出异常
                if (isset($obj->data['children'])) {
                    foreach ($obj->data['children'] as $dir) {
                        if ($dir['path'] === $new_dir_data['path']) {
                            throw new \Exception('已存在相同文件夹');
                        }
                    }
                } else {
                    // 没有子目录的，创建后继续
                    $obj->data['children'] = [];
                }
                // 写入新目录
                $obj->data['children'][] = $new_dir_data;
                return true;
            }
            // 当前不是目标路径的情况，将其他
            if (isset($obj->data['children'])) {
                foreach ($obj->data['children'] as $key => $val) {
                    // 将数据放入变量中以维持引用的状态，避免发生复制
                    $obj2 = new \stdClass();
                    $obj2->data = &$obj->data['children'][$key];
                    $queue->enqueue($obj2);
                }
            }
        }
        throw new \Exception('没有找到指定路径');
    }

    /**
     * 删除目录
     *
     * 删除指定虚拟路径
     *
     * POST
     * virtual_post: 要删除的虚拟路径
     * @param Request $request
     * @param string $project_id 项目id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delDir(Request $request, $project_id)
    {
        $virtual_path = $request->post('virtual_path');
        if (empty($virtual_path)) {
            return response()->json(['info' => '路径不能为空', 'status' => 0]);
        }
        $structure_json = DirStructure::where('project_id', $project_id)->first()->value('structure');
        $structure = json_decode($structure_json, true);

        // 递归搜索删除目录
        $isFinish = $this->delNodeFromStructure($structure, $virtual_path);

        if (!$isFinish) {
            return response()->json([
                'info' => '删除失败，无法找到指定路径',
                'status' => 0
            ]);
        }
        // 写入记录
        return response()->json([
            'info' => '删除成功',
            'status' => 1,
            'data' => $structure
        ]);
    }

    /**
     * 删除指定路径
     *
     * 注意：
     * 该方法可直接删除任意路径。
     * 该方法会在找到指定路径，操作完成后立即结束，不会进行多余的遍历。
     *
     * @param array $structure 目录树数组
     * @param string $needle 指定目录路径（注意是已存在的路径，新建的文件夹会在这个路径下）
     * @return bool 插入成功为true，失败为false
     */
    private function delNodeFromStructure(array &$structure, string $needle)
    {
        // 对于任意一个数组都进行遍历
        if (is_array($structure)) {
            // 必须带&引用，递归内部需要对$structure造成修改
            foreach ($structure as $key => &$value) {
                // 当前节点path即为目标路径时，删除文件夹，结束递归
                if ($needle == $value['path']) {
                    // 删除节点
                    unset($structure[$key]);
                    // 重新索引
                    $structure = array_merge($structure);
                    return true;
                }
                // 如果有children段，则取出进行递归
                if (isset($value['children'])) {
                    $isFinish = $this->delNodeFromStructure($value['children'], $needle);
                    // 如果成功删除，再次检查 children 内是否还有目录，没有的话，清理 children 字段
                    if ($isFinish && count($value['children']) === 0) {
                        unset($structure[$key]['children']);
                    }
                    return $isFinish;
                }
            }
        }
        return false;
    }
}
