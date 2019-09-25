<?php

namespace App\Http\Controllers\WeApp;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Module;
use App\Models\User;

class SubjectController extends Controller
{
    public function getAllSubjects(Request $request)
    {
        $data = Subject::get();
        $subjects = [];
        foreach($data as $v) {
            $subjects[$v->subject_type][] = $v;
        }
        $data = [];
        foreach($subjects as $k => $v) {
            $data[] = ['name' => $k, 'list' => $v];
        }

        return weappReturn(SUCCESS, '获取成功', $data);
    }

    public function chooseSubject(Request $request)
    {
        User::where('id', getWeappUserId())->update([
            'subject_code' => $request->subjectCode,
            'subject_name' => $request->subjectName
        ]);

        return weappReturn(SUCCESS, '切换成功');
    }

    public function getMySubject(Request $request)
    {
        $subject = User::where('id', getWeappUserId())->first(['subject_code', 'subject_name']);

        if ($subject->subject_code) {
            $data = Module::where('subject_code', $subject->subject_code)->get();
            $temp = [];
            foreach($data as $v) {
                $temp[$v->module_type][] = $v;
            }
            $modules = [];
            foreach($temp as $k => $v) {
                $modules[] = ['name' => $k, 'list' => $v];
            }
        } else {
            $modules = [];
        }

        return weappReturn(SUCCESS, '获取成功', ['subject_code' => $subject->subject_code, 'subject_name' => $subject->subject_name, 'modules' => $modules]);
    }

    public function updateUserInfo(Request $request)
    {
        if (! $request->nickname) {
            return weappReturn(ERROR, '昵称必填');
        }

        $user = User::where('id', getWeappUserId())->update(['weapp_nickname' => $request->nickname]);

        return weappReturn(SUCCESS, '保存成功');
    }
}