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
        // avoid questions the user has already answered in previous attempts
        $answeredQuestionIds = QuizAnswer::whereHas('quizAttempt', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->pluck('question_id')->toArray();

        // try to get up to 10 questions excluding previously answered ones
        $query = Question::query();
        if (!empty($answeredQuestionIds)) {
            $query->whereNotIn('id', $answeredQuestionIds);
        }

        $questions = $query->inRandomOrder()->limit(10)->get();

        // if not enough new questions remain, fill the rest with any other questions
        if ($questions->count() < 10) {
            $needed = 10 - $questions->count();
            $existingIds = $questions->pluck('id')->toArray();
            $additional = Question::whereNotIn('id', $existingIds)->inRandomOrder()->limit($needed)->get();
            // concat maintains uniqueness within this quiz
            $questions = $questions->concat($additional);
        }

        $quizAttempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'started_at' => now(),
        ]);

        return response()->json([
            'quiz_attempt_id' => $quizAttempt->id,
            'questions' => $questions->map(function ($question) {
                $options = [];
                $options[] = ['id' => 'a', 'option_text' => $question->option_a];
                $options[] = ['id' => 'b', 'option_text' => $question->option_b];
                $options[] = ['id' => 'c', 'option_text' => $question->option_c];
                $options[] = ['id' => 'd', 'option_text' => $question->option_d];

                return [
                    'id' => $question->id,
                    'question_text' => $question->question,
                    'options' => $options,
                ];
            }),
        ]);
    }

    public function submitAnswer(Request $request)
    {
        // Accept either 'option_id' (a|b|c|d) or 'answer' for backward compatibility
        $validated = $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|in:a,b,c,d',
            'answer' => 'nullable|in:a,b,c,d',
        ]);

        $answer = $request->input('option_id') ?? $request->input('answer');
        if (!$answer) {
            return response()->json(['error' => 'Answer is required (option_id or answer)'], 422);
        }

        $quizAttempt = QuizAttempt::findOrFail($request->quiz_attempt_id);

        if ($quizAttempt->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($quizAttempt->completed_at) {
            return response()->json(['error' => 'Quiz already completed'], 400);
        }

        $question = Question::findOrFail($request->question_id);
        $isCorrect = $question->correct_answer === $answer;

        QuizAnswer::create([
            'quiz_attempt_id' => $quizAttempt->id,
            'question_id' => $question->id,
            'user_answer' => $answer,
            'is_correct' => $isCorrect,
        ]);

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
        ]);
    }

    public function complete(Request $request)
    {
        // Accept time_seconds or time_spent (frontend uses time_spent)
        $validated = $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
            'time_seconds' => 'nullable|integer|min:0',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $timeSeconds = $request->input('time_seconds') ?? $request->input('time_spent');
        if (!is_numeric($timeSeconds)) {
            return response()->json(['error' => 'time_seconds or time_spent is required'], 422);
        }

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
            'time_seconds' => (int)$timeSeconds,
            'completed_at' => now(),
        ]);

        return response()->json([
            'quiz_attempt' => $quizAttempt->load('answers.question'),
            'summary' => [
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'score' => $score,
                'time_seconds' => (int)$timeSeconds,
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
