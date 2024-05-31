<?php

namespace App\Http\Controllers;

use App\Models\MedicamentModel;
use Illuminate\Http\Request;

class MedicamentController extends Controller
{
    public function AddMedicament(Request $request){
        $request->validate([
                'name' => 'required',
        ]);

        MedicamentModel::create([
               'name' => $request->name,
        ]);

        return response()->json([
            "message" => 'Traitement réussi avec succès!',
            "code" => 200
        ], 200);


 }

 public function UpdateMedicament(Request $request,$id)
 {
    $request->validate([
        'name' => 'required',
    ]);

    $medicament=MedicamentModel::find($id);
    if($medicament){
        $medicament->name=$request->name;
        $medicament->save();
        return response()->json([
            "message" => "La modification réussie"
        ], 200);
    }else{
        return response()->json([
            "message" => "Erreur de la modification",
        ], 422);
    }

 }

 public function ListeMedicament(Request $request)
 {
    return response()->json([
           "message" => "Liste des medicaments",
           "code" => "200",
           "data" => MedicamentModel::orderby('name','ASC')->get(),
    ]);
 }
}
