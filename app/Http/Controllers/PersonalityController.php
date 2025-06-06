<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class PersonalityController extends Controller
{
    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answers' => 'required|array|min:2',  // يجب أن تحتوي على على الأقل خيارين
            'answers.*' => 'required|string|max:255',  // كل اختيار يجب أن يكون نص
        ]);

        $question = Question::create([
            'question' => $validated['question'],
        ]);

        foreach ($validated['answers'] as $answer) {
            $question->answers()->create(['answer' => $answer]);
        }

        return response()->json(['message' => 'Question Added Successfully'], 201);
    }

    public function updateQuestion(Request $request, $questionId)
    {
        $validated = $request->validate([
            'question' => 'nullable|string|max:255',
            'answers' => 'nullable|array|min:2',
            'answers.*' => 'nullable|string|max:255',
        ]);

        // البحث عن السؤال
        $question = Question::findOrFail($questionId);

        // تحديث السؤال إذا كان موجودًا
        if (isset($validated['question'])) {
            $question->update(['question' => $validated['question']]);
        }

        if (isset($validated['answers'])) {
            $question->answers()->delete();

            foreach ($validated['answers'] as $answer) {
                $question->answers()->create(['answer' => $answer]);
            }
        }

        return response()->json(['message' => 'Question Updated Successfully'], 201);
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);

        $question->answers()->delete();

        $question->delete();

        return response()->json(['message' => 'Question Deleted Successfully'], 201);
    }

    public function getAllQuestions()
    {
        $questions = Question::with('answers')->get();
        return response()->json($questions);
    }


}
