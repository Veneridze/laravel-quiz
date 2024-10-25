<?php
namespace Veneridze\LaravelQuestion\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Veneridze\LaravelQuestion\Models\Quiz;
use Veneridze\LaravelQuestion\Models\Answer;
use Veneridze\LaravelQuestion\Models\Option;
use Veneridze\LaravelQuestion\Models\Attempt;
use Veneridze\LaravelQuestion\Models\Question;

class AttemptController extends \Illuminate\Routing\Controller {
    public function index(Quiz $quiz) {
        return $quiz->attempts()->whereBelongsTo(Auth::user());
    }
    public function start(Quiz $quiz) {
        //create attempt
        $attempt = $quiz->getActiveAttempt(Auth::user()) ?? $quiz->attempts()->create([
            'user_id' => Auth::id(),
        ]);


        return [
            'id' => $attempt->id,
            'started' => $attempt->created_at,
            'questions' => $quiz->questions->map(fn(Question $question) => [
                'label' => $question->label,
                'multiple' => $question->multiple,
                'type' => $question->type,
                'options' => $question->options->map(fn(Option $option) => [
                    'id' => $option->id,
                    'label' => $option->name,
                    'checked' => $attempt->answers()->whereBelongsTo($option)->exists()
                ])
            ])
        ];
    }

    public function results(Attempt $attempt) {
        abort_if($attempt->isActive, 400, 'Попытка ещё не завершена');
        return [
            'id' => $attempt->id,
            'passed' => $attempt->isPassed(),
            'mark' => $attempt->mark,
            'correct' => $attempt->correctAnswersCount,
            'wrong' => $attempt->wrongAnswersCount
        ];
    }

    public function answer(Quiz $quiz, Request $request) {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['exists:questions_options,id']
        ]);
        $attempt = $quiz->getActiveAttempt(Auth::user());
        abort_if(!$attempt, 400, 'Нет активных попыток прохождения теста');
        $answers = $request->input('answers');

        foreach ($answers as $answer) {
            $attempt->answers()->firstOrCreate([
                'attempt_id' => $attempt->id,
                'option_id' => $answer
            ]);
        }
        return response(null, 201);
    }

    public function finish(Quiz $quiz) {
        $attempt = $quiz->getActiveAttempt(Auth::user());
        abort_if(!$attempt, 400, 'Нет активных попыток прохождения теста');
        $attempt->finish();
        return $this->results($attempt);
    }
}