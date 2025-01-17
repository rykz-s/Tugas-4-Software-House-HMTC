<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\User;
use App\Answer;

class QuestionController extends Controller
{
    public function store(Request $request) {
        $user = User::find($request->user_id);

        if($user !== null) {
            $question = $user->question()->create([
                'question' => $request->question
            ]);
        }
        
        return redirect()->route('view', $question->id); 
    }

    public function edit($id){
        $question = Question::find($id);

        return view('EditQuestion', compact('question'));
    }

    public function update(Request $request){
        Question::where('id', $request->id)->update([
            'question' => $request->question
        ]);
        
        return redirect()->back()->with('sukses','sukses diperbarui');;
    }
    
    public function delete($id) {
        Question::where('id', $id)->delete();

        return redirect()->route('forum');
    }

    public function forum()
    {
        $questions = Question::orderBy('updated_at', 'desc')->paginate(5);
        $answers = Answer::orderBy('updated_at', 'desc')->get();

        return view('forums', compact(['questions','answers']));
    }

    public function view($id){
        $question = Question::find($id);
        
        return view('view',compact('question'));
    }

    public function questionByUser() {
        $user = User::find(auth()->user()->id);
        $questions = $user->question()->orderBy('created_at', 'DESC')->paginate(6);

        return view('listQ', compact(['questions']));
    }

    public function search_f(Request $request){
        if($request->has('search') && $request->search!=''){
            return redirect()->route('search_utility',$request->search)->with('ketemu',$request->search);
        }
        return redirect()->route('forum')->with('no_result','tidak ada hasil pencarian');
    }

    public function search_utility(Request $request){
        $questions = Question::where('question','LIKE','%'.$request->search.'%')
                            ->orderBy('created_at','desc')
                            ->paginate(6);
        $answers = Answer::Where('answer','LIKE','%'.$request->search.'%')->get();
        return view('forums',compact(['questions','answers']));
    }
}
