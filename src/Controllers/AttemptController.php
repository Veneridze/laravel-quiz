<?php
use Veneridze\LaravelQuestion\Models\Answer;
use Veneridze\LaravelQuestion\Models\Attempt;
use Veneridze\LaravelQuestion\Models\Option;
use Veneridze\LaravelQuestion\Models\Question;
use Veneridze\LaravelQuestion\Models\Quiz;
class AttemptController {
    public function index(Quiz $quiz) {
        return $quiz->attempts()->whereBelongsTo(Auth::user());
    }
    public function start(Quiz $quiz) {
        //create attempt
        $attempt = $quiz->getActiveAttempt() ?? $quiz->attempts()->create([
            'user_id' => Auth::id(),
        ]);


        return [
            'id' => $attempt->id,
            'started' => $attempt->started_at,
            'questions' => $quiz->questions->map(fn(Question $question) => [
                'label' => $question->label,
                'multiple' => $question->multiple,
                'type' => $question->type,
                'options' => $question->options->map(fn(Option $option) => [
                    'id' => $option->id,
                    'label' => $option->label,
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
            'correct' => $attempt->correctAnswers,
            'wrong' => $attempt->wrongAnswers
        ];
    }

    public function answer(Quiz $quiz, Request $request) {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['exists:questions_options,id']
        ]);
        $attempt = $quiz->getActiveAttempt();
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
        $attempt = $quiz->getActiveAttempt();
        abort_if(!$attempt, 400, 'Нет активных попыток прохождения теста');
        $attempt->finish();
        return $this->results($attempt);
    }
}