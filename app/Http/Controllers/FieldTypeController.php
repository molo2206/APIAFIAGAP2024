<?php

namespace App\Http\Controllers;

use App\Models\FieldsTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldTypeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            return response()->json([
                "message" => "TypeField list",
                "code" => "200",
                "data" => FieldsTypeModel::where('deleted', 0)->get(),
            ]);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap'))
        {

            $request->validate([
                'name' => 'required',
                'label' => 'required',
                'icon' => 'required',
            ]);

            $icon = UtilController::uploadImageUrl($request->icon, '/uploads/formulaire/icons/');
            $typefield = [
                'name' => $request->name,
                'label' => $request->label,
                'icon'  =>  $icon,
            ];

            FieldsTypeModel::create($typefield);
            return response()->json([
                "message" => "Saved successfully",
                "code" => 200,
            ], 200);

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
        if ($user->checkPermission('create_gap'))
        {
            $request->validate([
                'name' => 'required',
                'label' => 'required',
            ]);

            $icon = UtilController::uploadImageUrl($request->icon, '/uploads/formulaire/icons/');
            $typefield = [
                'name' => $request->title,
                'label' => $request->description,
                'icon'  =>  $icon,
            ];

            $typefield = FieldsTypeModel::find($id);
            if ($typefield) {

                $typefield->icon  = $typefield->$icon ? $typefield->icon : $icon;
                $typefield->name  = $request->name;
                $typefield->label = $request->label;
                $typefield->save();

                return response()->json([
                    "message" => "Updated successfully",
                    "code" => 200,
                    "data" => $typefield,
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


    public function status(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $form = FieldsTypeModel::find($id);
            if ($form) {
                $form->status = $request->status;
                $form->save();
                return response()->json([
                    "message" => "Status successfully",
                    "code" => 200,
                    "data" => FieldsTypeModel::where('deleted', 0)->where('status', 1)->get()
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

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->checkPermission('create_gap')) {
            $form = FieldsTypeModel::find($id);
            if ($form) {

                $form->deleted = 1;
                $form->save();

                return response()->json([
                    "message" => "Deleted successfully",
                    "code" => 200,
                    "data" => FieldsTypeModel::where('deleted', 0)->where('status', 1)->get()
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
