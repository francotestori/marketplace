<?php

namespace App\Http\Controllers;

use App\EventThreads;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        // All threads, ignore deleted/archived participants
        // $threads = Thread::getAllLatest()->get();

        // All threads that user is participating in
        $threads = Thread::forUser(Auth::id())->latest('updated_at')->get();

        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages(Auth::id())->latest('updated_at')->get();

        return view('messaging.messenger.index', compact('threads'));
    }

    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try
        {
            $thread = Thread::findOrFail($id);
        }
        catch (ModelNotFoundException $e)
        {
            Session::flash('errors', 'The thread with ID: ' . $id . ' was not found.');
            return redirect()->route('messages');
        }

        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // don't show the current user in list
        $userId = Auth::id();
        
        $allowed = $thread->participants()->pluck('user_id')->toArray();
        if(in_array($userId, $allowed)){
            $event = EventThreads::where('thread_id', $id)->first()->getEvent();
            $thread->markAsRead($userId);
            return view('messaging.messenger.show', compact('thread', 'users', 'event'));
        }

        Session::flash('errors', Lang::get('messages.forbidden'));
        return redirect()->route('messages');
    }


    /**
     * Adds a new message to a current thread.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        try
        {
            $thread = Thread::findOrFail($id);
        }
        catch (ModelNotFoundException $e)
        {
            Session::flash('errors', 'The thread with ID: ' . $id . ' was not found.');
            return redirect()->route('messages');
        }

        // don't show the current user in list
        $userId = Auth::id();

        $allowed = $thread->participants()->pluck('user_id')->toArray();
        if(in_array($userId, $allowed)){
            $thread->activateAllParticipants();

            $message = Input::get('message');
            if(empty($message))
            {
                Session::flash('errors', Lang::get('messages.messenger.empty_message'));
                return redirect()->route('messages.show', $id);
            }

            // Message
            Message::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'body' => $message,
            ]);

            return redirect()->route('messages.show', $id);
        }

        Session::flash('errors', Lang::get('messages.forbidden'));
        return redirect()->route('messages');
    }
}
