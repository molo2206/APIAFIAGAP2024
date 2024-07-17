<?php

namespace App\Http\Controllers;

use App\Models\formsModel;
use App\Models\UserHasForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHasFormController extends Controller
{
    public function index()
    {

    }

    public function get_by_id($id)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'response' => 'required',
            'formid' => 'required',
        ]);

        // Store the data in the database
        $user = Auth::user();
        $form=formsModel::find($request->formid);
        if($form){

            $hasuser = [
                'userid' => $user->id,
                'formid' => $request->formid,
            ];

            $hasform = UserHasForm::create($hasuser);

            $hasform->response()->detach();

            foreach ($request->response as $item) {
                $hasform->response()->attach([$hasform->id=>[
                    'field_id' => $item['field_id'],
                    'value' => $item['value'],
                ]]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Saved successfully'
            ], 200);
        }else{
            return response()->json([
                'code' => 404,
               'message' => 'Form not found'
            ], 404);
        }
    }
    public function destroy(Request $request)
    {

    }
    public function update()
    {

    }
}
