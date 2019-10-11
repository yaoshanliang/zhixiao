<?php

namespace App\Http\Controllers\WeApp;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Module;
use App\Models\User;
use App\Models\UserAnswer;
use DB;

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

        $totalCount = 0;
        $doneCount = 0;
        $errorCount = 0;
        if ($subject->subject_code) {
            $data = Module::where('subject_code', $subject->subject_code)->get();
            $count = DB::table($subject->subject_code . '_questions')->groupby('module_code')->get(['module_code', DB::raw("count(id) as count")]);
            $countArray = [];
            foreach($count as $v) {
                $countArray[$v->module_code] = $v->count;
            }

            $count = UserAnswer::where('user_id', getWeappUserId())->where('subject_code', $subject->subject_code)->where('status', '!=', 0)
                ->groupby('module_code')->get(['module_code', DB::raw("count(id) as count")]);
            $countAnswer = [];
            foreach($count as $v) {
                $countAnswer[$v->module_code] = $v->count;
            }

            $temp = [];
            
            foreach($data as $v) {
                $v->total_count = $countArray[$v->module_code] ?? 0;
                $v->done_count = $countAnswer[$v->module_code] ?? 0;
                $temp[$v->module_type][] = $v;

                $totalCount += $v->total_count;
                $doneCount += $v->done_count;
            }
            $modules = [];
            foreach($temp as $k => $v) {
                $modules[] = ['name' => $k, 'list' => $v];
            }
            $errorCount = UserAnswer::where('user_id', getWeappUserId())->where('subject_code', $subject->subject_code)->where('status', 2)->count();
        } else {
            $modules = [];
        }

        return weappReturn(SUCCESS, '获取成功', [
            'subject_code' => $subject->subject_code, 'subject_name' => $subject->subject_name, 'modules' => $modules, 
            'totalCount' => $totalCount, 'doneCount' => $doneCount, 'errorCount' => $errorCount
        ]);
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