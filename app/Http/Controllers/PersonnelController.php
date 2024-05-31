<?php

namespace App\Http\Controllers;

use App\Models\PersonnelModel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function AddPersonel(Request $request){
        $request->validate([
                'name' => 'required',
        ]);

        PersonnelModel::create([
               'name' => $request->name,
        ]);

        return response()->json([
            "message" => 'Traitement réussi avec succès!',
            "code" => 200
        ], 200);


 }

 public function UpdatePersonel(Request $request,$id)
 {
    $request->validate([
        'name' => 'required',
    ]);

    $perso=PersonnelModel::find($id);
    if($perso){
        $perso->name=$request->name;
        $perso->save();
        return response()->json([
            "message" => "La modification réussie"
        ], 200);
    }else{
        return response()->json([
            "message" => "Erreur de la modification",
        ], 422);
    }

 }

 public function ListePersonel(Request $request)
 {
    return response()->json([
           "message" => "Liste type des personnels",
           "code" => "200",
           "data" => PersonnelModel::all(),
    ]);
 }
}
