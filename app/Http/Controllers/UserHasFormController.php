<?php

namespace App\Http\Controllers;

use App\Models\formsModel;
use App\Models\ResponseForm;
use App\Models\structureSanteModel;
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

    public function get_by_stucture(Request $request, $id)
    {
        $structure = structureSanteModel::find($id);
        $arrayForm  = formsModel::where('type', 'afiagap')->get();

        if ($structure)
        {

            $array = [];
            foreach ($arrayForm as $key => $value) {
                $form = ResponseForm::with('hasForm.structure','hasForm.response')
                    ->whereRelation('hasForm.form', 'id', $value->id)
                    ->where('value', $structure->id)->get();
                foreach ($form as $key => $item) {
                    array_push($array, $item);
                }
            }
            return response()->json([
                'code' => 200,
                'message' => "Data form",
                'data' => $array
            ]);

        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Structure not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'response' => 'required',
            'formid' => 'required',
            'structure_id' => 'required',
        ]);

        // Store the data in the database
        $user = Auth::user();
        $form = formsModel::find($request->formid);
        if ($form) {
            $hasuser = [
                'userid' => $user->id,
                'formid' => $request->formid,
                'structure_id' => $request->structure_id
            ];

            $hasform = UserHasForm::create($hasuser);

            $hasform->response()->detach();
            foreach ($request->response as $item) {
                $hasform->response()->attach([$hasform->id => [
                    'field_id' => $item['field_id'],
                    'value' => $item['value'],
                ]]);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Saved successfully'
            ], 200);
        } else {
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
