<?php

namespace App\Http\Controllers\WeApp;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Question;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserCollect;
use DB;

class QuestionController extends Controller
{
    public function getQuestions(Request $request)
    {
        if ($request->moduleCode && $request->moduleCode != 'all') {
            $data = DB::table($request->subjectCode . '_questions')->select('id', 'module_code', 'title', 'options', 'answer', 'type', 'analysis')->where('module_code', $request->moduleCode)->skip($request->page * 100)->take(50)->get();
            $count = DB::table($request->subjectCode . '_questions')->where('module_code', $request->moduleCode)->count();
            $ids = DB::table($request->subjectCode . '_questions')->get(['id']);

            $myAnswer = UserAnswer::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->get();
            $myAnswerArray = [];
            $correctNum = 0;
            $errorNum = 0;
            foreach($myAnswer as $v) {
                if ($v->status == 1) {
                    $correctNum++;
                }
                if ($v->status == 2) {
                    $errorNum++;
                }
                $myAnswerArray[$v->question_id] = $v;
            }
            $myCollect = UserCollect::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->get();
            $myCollectArray = [];
            foreach($myCollect as $v) {
                $myCollectArray[$v->question_id] = $v;
            }

        } elseif ($request->subjectCode) {
            $data = DB::table($request->subjectCode . '_questions')->select('id', 'module_code', 'title', 'options', 'answer', 'type', 'analysis')->skip($request->page * 100)->take(50)->get();
            $count = DB::table($request->subjectCode . '_questions')->count();
            $ids = DB::table($request->subjectCode . '_questions')->get(['id']);

            $myAnswer = UserAnswer::where('user_id', getWeappUserId())->where('subject_code', $request->subjectCode)->get();
            $myAnswerArray = [];
            $correctNum = 0;
            $errorNum = 0;
            foreach($myAnswer as $v) {
                if ($v->status == 1) {
                    $correctNum++;
                }
                if ($v->status == 2) {
                    $errorNum++;
                }
                $myAnswerArray[$v->question_id] = $v;
            }
            $myCollect = UserCollect::where('user_id', getWeappUserId())->where('subject_code', $request->subjectCode)->get();
            $myCollectArray = [];
            foreach($myCollect as $v) {
                $myCollectArray[$v->question_id] = $v;
            }
        }

        $myAnswerList = [];
        foreach($data as &$v) {
            $v->options = json_decode($v->options);
            if (isset($myAnswerArray[$v->id]) && isset($myCollectArray[$v->id])) {
                $myAnswerList[] = ['question_id' => $v->id, 'answer' => $myAnswerArray[$v->id]['answer'], 'status' => $myAnswerArray[$v->id]['status'], 'collect' => $myCollectArray[$v->id]['collect']];
            } elseif (isset($myAnswerArray[$v->id]) && !isset($myCollectArray[$v->id])) {
                $myAnswerList[] = ['question_id' => $v->id, 'answer' => $myAnswerArray[$v->id]['answer'], 'status' => $myAnswerArray[$v->id]['status'], 'collect' => 0];
            } elseif (! isset($myAnswerArray[$v->id]) && isset($myCollectArray[$v->id])) {
                $myAnswerList[] = ['question_id' => $v->id, 'answer' => '', 'status' => 0, 'collect' => $myCollectArray[$v->id]['collect']];
            }else {
                $myAnswerList[] = ['question_id' => $v->id, 'answer' => '', 'status' => 0, 'collect' => 0];
            }
        }

        return weappReturn(SUCCESS, '获取成功', ['count' => $count, 'list' => $data, 'myAnswerList' => $myAnswerList, 'correctNum' => $correctNum, 'errorNum' => $errorNum]);
    }

    public function postAnswer(Request $request)
    {

        if (UserAnswer::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $request->questionId)->exists()) {
            UserAnswer::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $request->questionId)->update([
                'answer' => $v['answer'], 'status' => $v['status']
            ]);
        } else {
            UserAnswer::create([
                'user_id' => getWeappUserId(), 'subject_code' => $request->subjectCode, 'module_code' => $request->moduleCode,
                'question_id' => $request->questionId, 'answer' => $request->answer, 'status' => $request->status
            ]);
        }

        return weappReturn(SUCCESS, '保存成功');
    }

    public function postCollect(Request $request)
    {
        if ($request->collect == 0) {
            UserCollect::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $request->questionId)->delete();
        } elseif (! UserCollect::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $request->questionId)->exists()) {
            UserCollect::create([
                'user_id' => getWeappUserId(), 'subject_code' => $request->subjectCode, 'module_code' => $request->moduleCode,
                'question_id' => $request->questionId, 'collect' => $request->collect]);
        } 

        return weappReturn(SUCCESS, '保存成功');
    }

    public function postAnswers(Request $request)
    {
        if (! $request->myAnswerList) {
            return weappReturn(ERROR, '答案必填');
        }
        foreach ($request->myAnswerList as $v) {
            if ($v['status'] || $v['collect']) {
                if (UserAnswer::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $v['question_id'])->exists()) {
                    UserAnswer::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $v['question_id'])->update([
                        'answer' => $v['answer'], 'status' => $v['status'], 'collect' => $v['collect']
                    ]);
                } else {
                    UserAnswer::create([
                        'user_id' => getWeappUserId(), 'subject_code' => $request->subjectCode, 'module_code' => $request->moduleCode,
                        'question_id' => $v['question_id'], 'answer' => $v['answer'], 'status' => $v['status'], 'collect' => $v['collect']
                    ]);
                }
            }
            if (! $v['status'] && ! $v['collect']) {
                UserAnswer::where('user_id', getWeappUserId())->where('module_code', $request->moduleCode)->where('question_id', $v['question_id'])->delete();
            }
        }

        return weappReturn(SUCCESS, '保存成功');
    }


}