<?php

namespace App\Http\Controllers\WeApp;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Question;
use App\Models\User;
use DB;

class QuestionController extends Controller
{
    public function getQuestions(Request $request)
    {
        if ($request->moduleCode) {
            $data = DB::table($request->subjectCode . '_questions')->select('id', 'title', 'options', 'answer', 'type', 'analysis')->where('module_code', $request->moduleCode)->skip($request->page * 100)->take(100)->get();
            $count = DB::table($request->subjectCode . '_questions')->where('module_code', $request->moduleCode)->count();
            $ids = DB::table($request->subjectCode . '_questions')->get(['id']);
        }
        $myAnswer = [];
        foreach($data as &$v) {
            $v->options = json_decode($v->options);
            $myAnswer[$v->id] = '';
        }

        return weappReturn(SUCCESS, '获取成功', ['count' => $count, 'list' => $data, 'myAnswer' => $myAnswer]);
    }

    public function chooseSubject(Request $request)
    {
        User::where('id', getWeappUserId())->update([
            'subject_id' => $request->subjectId,
            'subject_name' => $request->subjectName
        ]);

        return weappReturn(SUCCESS, '切换成功');
    }

    public function getMySubject(Request $request)
    {
        $data = User::where('id', getWeappUserId())->first(['subject_name']);

        return weappReturn(SUCCESS, '获取成功', $data);
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