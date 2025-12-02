<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function start(Request $request)
    {
        $questions = Question::inRandomOrder()->limit(10)->get();

        $quizAttempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'started_at' => now(),
        ]);

        return response()->json([
            'quiz_attempt_id' => $quizAttempt->id,
            'questions' => $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'options' => [
                        'a' => $question->option_a,
                        'b' => $question->option_b,
                        'c' => $question->option_c,
                        'd' => $question->option_d,
                    ],
                ];
            }),
        ]);
    }

    public function submitAnswer(Request $request)
    {
        $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|in:a,b,c,d',
        ]);

        $quizAttempt = QuizAttempt::findOrFail($request->quiz_attempt_id);

        if ($quizAttempt->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($quizAttempt->completed_at) {
            return response()->json(['error' => 'Quiz already completed'], 400);
        }

        $question = Question::findOrFail($request->question_id);
        $isCorrect = $question->correct_answer === $request->answer;

        QuizAnswer::create([
            'quiz_attempt_id' => $quizAttempt->id,
            'question_id' => $question->id,
            'user_answer' => $request->answer,
            'is_correct' => $isCorrect,
        ]);

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
        ]);
    }

    public function complete(Request $request)
    {
        $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
            'time_seconds' => 'required|integer|min:0',
        ]);

        $quizAttempt = QuizAttempt::findOrFail($request->quiz_attempt_id);

        if ($quizAttempt->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($quizAttempt->completed_at) {
            return response()->json(['error' => 'Quiz already completed'], 400);
        }

        $correctAnswers = $quizAttempt->answers()->where('is_correct', true)->count();
        $wrongAnswers = $quizAttempt->answers()->where('is_correct', false)->count();
        $score = $correctAnswers * 10;

        $quizAttempt->update([
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'score' => $score,
            'time_seconds' => $request->time_seconds,
            'completed_at' => now(),
        ]);

        return response()->json([
            'quiz_attempt' => $quizAttempt->load('answers.question'),
            'summary' => [
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'score' => $score,
                'time_seconds' => $request->time_seconds,
            ],
        ]);
    }

    public function myAttempts(Request $request)
    {
        $attempts = QuizAttempt::where('user_id', $request->user()->id)
            ->whereNotNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->with('answers.question')
            ->get();

        return response()->json($attempts);
    }
}
