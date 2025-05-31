<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\WorkEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function show(WorkEntry $workEntry)
    {
        if (!Gate::allows('view-work-entry', $workEntry)) {
            return redirect('/dashboard')->with('error', 'Nie masz uprawnień do przeglądania tego wpisu.');
        }
        $workEntry->load(['employee', 'enteredBy', 'comments.user']);
        return view('work_entries.show', compact('workEntry'));
    }

    public function store(Request $request, WorkEntry $workEntry): RedirectResponse
    {
        if (!Gate::allows('add-comment', $workEntry)) {
             return redirect()->back()->with('error', 'Nie możesz dodać komentarza do tego wpisu.');
        }

        $validatedData = $request->validate([
            'comment_text' => 'required|string|min:1',
        ]);

        $comment = new Comment();
        $comment->work_entry_id = $workEntry->id;
        $comment->user_id = Auth::id();
        $comment->comment_text = $validatedData['comment_text'];
        $comment->save();

        return redirect()->route('work-entries.show', $workEntry)->with('success_comment', 'Komentarz został dodany.');
    }

public function update(Request $request, Comment $comment): RedirectResponse|JsonResponse
{
    if (!Gate::allows('update-comment', $comment)) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Nie masz uprawnień do edycji tego komentarza.'], 403);
        }
        return redirect()->back()->with('error_comment', 'Nie masz uprawnień do edycji tego komentarza.');
    }

    $validatedData = $request->validate([
        'comment_text' => 'required|string|min:1',
    ]);

    $comment->comment_text = $validatedData['comment_text'];
    $comment->save();

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Komentarz został zaktualizowany.',
            'comment' => $comment 
        ]);
    }

    return redirect()->route('work-entries.show', $comment->work_entry_id)->with('success_comment', 'Komentarz został zaktualizowany.');
}

    public function destroy(Comment $comment): RedirectResponse
    {
        if (!Gate::allows('delete-comment', $comment)) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do usunięcia tego komentarza.');
        }

        $workEntryId = $comment->work_entry_id;
        $comment->delete();

        return redirect()->route('work-entries.show', $workEntryId)->with('success_comment', 'Komentarz został usunięty.');
    }
}