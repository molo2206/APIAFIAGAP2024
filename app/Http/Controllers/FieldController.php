<?php

namespace App\Http\Controllers;

use App\Models\FieldsModel;
use App\Models\FieldsTypeModel;
use App\Models\formsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            return response()->json([
                "message" => "Forms list",
                "code" => "200",
                "data" => FieldsModel::where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function show($otp)
    {
        $form = formsModel::where('otp_form', $otp)->first();
        if ($form) {
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
                "data" => formsModel::with('fields.typefield', 'project.struturesantes')->where('otp_form', $form->otp_form)->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Otp not found",
                "code" => "404"
            ], 404);
        }
    }



    public function show_by_id($id)
    {
        $form = formsModel::find($id);
        if ($form) {
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
                "data" => formsModel::with('fields.typefield')->where('id', $form->id)->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Id not found",
                "code" => "404"
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $request->validate([
                'form_id' => 'required',
                'fields' => 'required',
            ]);
            $form = formsModel::where('id', $request->form_id)->first();
            if ($form) {
                foreach ($request->fields as $key => $item) {
                    if (count($form->fields) > 0) {
                        if ($request->fields[$key]['id'] != null) {
                            foreach ($form->fields as $field) {
                                $field->where('id', $item['id'])->update(
                                    [
                                        'name' => $item['name'],
                                        'label' => $item['label'],
                                        'fieldtype_id' => $item['fieldtype_id'],
                                        'isOptional' => $item['isOptional'],
                                        'number' => $item['number'],
                                    ]
                                );
                            }
                        } else {
                            $form->fields()->create(
                                [
                                    'name' => $item['name'],
                                    'label' => $item['label'],
                                    'fieldtype_id' => $item['fieldtype_id'],
                                    'isOptional' => $item['isOptional'],
                                    'number' => $item['number'],
                                ]
                            );
                        }
                    } else {
                        $form->field()->attach([$form->id =>
                        [
                            'name' => $item['name'],
                            'label' => $item['label'],
                            'fieldtype_id' => $item['fieldtype_id'],
                            'isOptional' => $item['isOptional'],
                            'number' => $item['number'],
                        ]]);
                    }
                }
                return response()->json([
                    "message" => "Saved successfully",
                    "code" => 200,
                    "data" => formsModel::with('fields.typefield')->where('id', $form->id)->first()
                ], 200);
            } else {
                return response()->json([
                    "message" => "Id not found",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $request->validate([
                'name' => 'required',
                'label' => 'required',
                'form_id' => 'required',
                'fieldtype_id' => 'required',
            ]);

            $form = FieldsModel::with('fields.typefield')->find($id);

            if ($form) {

                $form->name = $form->name ? $form->name : $request->name;
                $form->label = $form->label ? $form->label : $request->label;
                $form->form_id = $form->form_id ? $form->form_id : $request->form_id;
                $form->fieldtype_id = $form->fieldtype_id ? $form->fieldtype_id : $request->fieldtype_id;
                $form->number = $form->number ? $form->number : $request->number;
                $form->save();

                return response()->json([
                    "message" => "Updated successfully",
                    "code" => 200,
                    "data" => $form,
                ], 200);
            } else {

                return response()->json([
                    "message" => "Id not found",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function addfield(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $request->validate([
                'form_id' => 'required',
                'label' => 'required',
                'fieldtype_id' => 'required',
                'isOptional' => 'required',
                'number' => 'required',
            ]);
            $form = formsModel::where('id', $request->form_id)->first();
            $typefield = FieldsTypeModel::where('id', $request->fieldtype_id)->first();
            if ($form) {
                if ($typefield) {
                    if (FieldsModel::where('name', $request->name)->exists()) {
                        return response()->json([
                            "message" => "Name field already exists",
                            "code" => 404,
                        ], 404);
                    } else {
                        FieldsModel::create([
                            'form_id' => $request->form_id,
                            'label' => $request->label,
                            'fieldtype_id' => $request->fieldtype_id,
                            'isOptional' => $request->isOptional,
                            'number' => $request->number
                        ]);
                        return response()->json([
                            "message" => "Saved successfully",
                            "code" => 200,
                            "data" => formsModel::with('fields.typefield')->where('id', $form->id)->first()
                        ], 200);
                    }
                } else {
                    return response()->json([
                        "message" => "Id typefield not found",
                        "code" => 404,
                    ], 404);
                }
            } else {
                return response()->json([
                    "message" => "Id form not found",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function status(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $form = FieldsModel::find($id);
            if ($form) {
                $form->status = $request->status;
                $form->save();

                return response()->json([
                    "message" => "Status successfully",
                    "code" => 200,
                    "data" => FieldsModel::where('deleted', 0)->where('status', 1)->get()
                ], 200);
            } else {
                return response()->json([
                    "message" => "Id not found",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function destroy($id, $field)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $form = formsModel::find($id);
            if ($form) {
                $field = $form->fields()->find($field);
                if (!$field) {
                    return response()->json([
                        "message" => "Id field not found",
                        "code" => 404,
                    ], 404);
                }
                $field->delete();
                return response()->json([
                    "message" => "Deleted successfully",
                    "code" => 200,
                    "data" => FieldsModel::where('deleted', 0)->where('status', 1)->get()
                ], 200);
            } else {
                return response()->json([
                    "message" => "Id not found",
                    "code" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }
}
