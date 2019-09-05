<?php

namespace App\Http\Controllers\WeApp;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\User;

class QuestionController extends Controller
{
    public function getSubjects(Request $request)
    {
        $data = Subject::get();
        $subjects = [];
        foreach($data as $v) {
            $subjects[$v->subject_type_name][] = $v;
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
            'subject_id' => $request->subjectId,
            'subject_name' => $request->subjectName
        ]);

        return weappReturn(SUCCESS, '切换成功');
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