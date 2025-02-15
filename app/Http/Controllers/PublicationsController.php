<?php

namespace App\Http\Controllers;

use App\Mail\contac_customs;
use App\Mail\Contact;
use App\Models\CategoryPublicattion;
use App\Models\Notifications;
use App\Models\PublicationsModel;
use App\Models\Tags;
use App\Models\TokenUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PublicationsController extends Controller
{
    public function addpublication(Request $request)
    {
        $user = Auth::user();
        if ($user->checkPermissions('Publication', 'create')) {
            $request->validate([
                "image" => "required|image",
                "title" => "required",
                "content" => "required",
                "auteur" => "required",
                "image" => "required",
                "date_post" => "required|date",
                "cat_id" => "required",
                "legend" => "required",
            ]);
            $user = Auth::user();
            $image = UtilController::uploadImageUrl($request->image, '/uploads/publications/');
            $pub = PublicationsModel::UpdateOrCreate(['title' => $request->title], [
                "image" => $image,
                "title" => $request->title,
                "content" => $request->content,
                "auteur" => $request->auteur,
                "legend" => $request->legend,
                "date_post" => $request->date_post,
                "cat_id" => $request->cat_id,

            ]);
            // $dataToken_for_user = TokenUsers::all();
            // foreach ($dataToken_for_user as $item) {
            //     PushNotification::sendPushNotification($item->token, $request->title, $request->content, $image);
            // }
            foreach (User::get() as $key => $value) {
                Notifications::create([
                    "user_id" => $value->id,
                    "title" => "Une publication a été mise en ligne par Cosamed",
                    "description" => $request->title,
                    "id_type" => $pub->id,
                    "type" => "publication"
                ]);
            }

            return response()->json([
                "message" => 'Liste des publications',
                "data" => PublicationsModel::with('category')->where('deleted', 0)->orderby('date_post', 'desc')->get()
            ], 200);
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }
    public function update_post(Request $request, $id)
    {
        $request->validate([
            "title" => "required",
            "content" => "required",
            "auteur" => "required",

            "date_post" => "required|date",
            "cat_id" => "required",
            "legend" => "required",
        ]);
        $user = Auth::user();
        if ($user->checkPermissions('Publication', 'update'))
        {
            $image = UtilController::uploadImageUrl($request->image, '/uploads/publications/');
            $pub = PublicationsModel::find($id);
            $pub->image = $image ? $image : $pub->image;
            $pub->title = $request->title;
            $pub->content = $request->content;
            $pub->auteur = $request->auteur;
            $pub->legend = $request->legend;
            $pub->date_post = $request->date_post;
            $pub->cat_id = $request->cat_id;
            $pub->save();

            // $dataToken_for_user = TokenUsers::all();
            // foreach ($dataToken_for_user as $item) {
            //     PushNotification::sendPushNotification($item->token, $request->title, $request->content, $image);
            // }
            foreach (User::get() as $key => $value) {
                Notifications::create([
                    "user_id" => $value->id,
                    "title" => "Une publication a été mise en ligne par Cosamed",
                    "description" => $request->title,
                    "id_type" => $pub->id,
                    "type" => "publication"
                ]);
            }

            return response()->json([
                "message" => 'Liste des publications',
                "data" => PublicationsModel::with('category')->where('deleted', 0)->orderby('date_post', 'desc')->get()
            ], 200);
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
        if ($user->checkPermissions('Publication', 'delete'))
        {
            $pub = PublicationsModel::where('deleted', 0)->where('id', $id)->first();
            if ($pub) {
                $pub->deleted = 1;
                $pub->update();
                return response()->json([
                    "message" => "Publications",
                    "code" => "200",
                    "data" => PublicationsModel::where('deleted', 0)->orderby('date_post', 'desc')->get(),
                ]);
            } else {
                return response()->json([
                    "message" => "Identifiant not found",
                    "code" => "402"
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "not authorized",
                "code" => 404,
            ], 404);
        }
    }

    public function getpublication()
    {
        return response()->json([
            "message" => 'Liste des publications',
            "data" => PublicationsModel::with('category')->where('deleted', 0)->orderBy('date_post', 'DESC')->get(),
            "code" => 200,
        ], 200);
    }

    public function getOnePublication($id)
    {
        $pub = PublicationsModel::where('deleted', 0)->where('id', $id)->first();
        if ($pub) {
            return response()->json([
                "message" => 'Liste des publications',
                "data" => PublicationsModel::with('category')->where('deleted', 0)->where('id', $id)->first(),
                "code" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => "Identifiant not found",
                "code" => "404"
            ], 404);
        }
    }

    public function getcategory()
    {
        return response()->json([
            "message" => 'Liste des categories',
            "data" => CategoryPublicattion::all(),
            "code" => 200,
        ], 200);
    }

    public function contact(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'phone' => 'required',
            'name' => 'required',
            'content' => 'required',
        ]);
        Mail::to($request->email)->send(new contac_customs(env('MAIL_FROM_ADDRESS')));
        Mail::to(env('MAIL_FROM_ADDRESS'))->send(new Contact($request->email, $request->name, $request->phone, $request->content));
        return response()->json([
            "message" => "Votre message à été envoyer avec succès.",
            "code" => 200,
        ], 200);
    }

    public function recherche_publication(Request $request)
    {
        $user = Auth::user();
        $data = PublicationsModel::with('category')->where('title', 'like', '%' . $request->keyword . '%')
            ->orwhere('content', 'like', '%' . $request->keyword . '%')
            ->orwhere('auteur', 'like', '%' . $request->keyword . '%')
            ->orwhere('legend', 'like', '%' . $request->keyword . '%')
            ->leftJoin('category_publication', 'category_publication.id', '=', 't_publications.cat_id')
            ->select(
                't_publications.id',
                't_publications.title',
                't_publications.content',
                't_publications.auteur',
                't_publications.image',
                'category_publication.name as category_publication'
            );

        $alldata = $data->get();
        if (count($alldata) > 0) {
            $user->tags()->UpdateOrCreate([
                'name' => $request->keyword,
            ], [
                'name' => $request->keyword,
            ]);
        }

        return response([
            "message" => "Success",
            "code" => 200,
            "data" => $alldata,
        ], 200);
    }
}
