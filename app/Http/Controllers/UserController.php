<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UtilController;
use App\Mail\Createcount;
use App\Mail\NewPswd;
use App\Mail\Verificationmail;
use App\Models\AffectationModel;
use App\Models\AffectationPermission;
use App\Models\codeValidation;
use App\Models\Organisation;
use App\Models\Permission;
use App\Models\RoleModel;
use App\Models\TokenUsers;
use App\Models\Type_users;
use App\Models\User;
use App\Models\User_has_Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function Login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'pswd' => 'required',
            ]);

            if (User::where('email', $request->email)->exists()) {
                $user = User::where('email', $request->email)->first();
                if ($user->status == 1) {
                    if (Hash::check($request->pswd, $user->pswd)) {
                        $token = $user->createToken("accessToken")->plainTextToken;
                        Log::channel(channel: 'slack')->critical(message: $user);
                        if ($request->token) {
                            $token_data = TokenUsers::where('token', $request->token)->first();
                            if ($token_data != null) {
                                $token_data->userid = $user->id;
                                $token_data->save();
                            } else {
                                if ($token_data != null && $token_data->userid == $user->id) {
                                    $token_data->userid = $user->id;
                                    $token_data->save();
                                } else {
                                    TokenUsers::create([
                                        'token' => $request->token,
                                        'userid' => $user->id
                                    ]);
                                }
                            }
                        }
                        return response()->json([
                            "message" => 'success',
                            "data" => $user::with('affectation.role', 'typeUser', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                                ->where('id', $user->id)->first(),
                            "status" => 1,
                            "token" => $token
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => 'Le mot de passe est incorrect'
                        ], 422);
                    }
                } else {
                    return response()->json([
                        "message" => 'Votre compte n\'est pas activé'
                    ], 422);
                }
            } else {
                return response()->json([
                    "message" => "Cette adresse email n'existe pas"
                ], 404);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
    public function getUsers()
    {
        return response()->json([
            "message" => "Liste des utilisateurs",
            "data" => User::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                ->get(),
        ]);
    }

    public function getuser()
    {
        $user = Auth::user();
        return response()->json([
            "message" => 'success',
            "data" => $user::with('affectation.role', 'typeUser', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                ->where('id', $user->id)->first(),
        ], 200);
    }

    public function getuserId($id)
    {

        if (User::where('id', $id)->exists()) {
            $user = User::where('id', $id)->first();

            $token = $user->createToken("accessToken")->plainTextToken;
            return response()->json([
                "message" => 'success',
                "data" => $user::with('affectation.role', 'typeUser', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                    ->where('id', $user->id)->first(),
                "status" => 1,
                "token" => $token
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette adresse email n'existe pas"
            ], 404);
        }
    }

    public function askcodevalidateion(Request $request)
    {
        $request->validate([
            "email" => "required"
        ]);
        if (User::where('email', $request->email)->exists()) {
            $code = mt_rand(1, 9999);
            $val = CodeValidation::where('email', $request->email)->first();
            if ($val) {
                $val->code = $code;
                $val->save();
            } else {
                codeValidation::create(['email' => $request->email, 'code' => $code]);
            }
            Mail::to($request->email)->send(new Verificationmail($request->email, $code));
            return response()->json([
                "code" => 200,
                "message" => "Un code de validation vous a été envoyé à l'adresse " . $request->email,
                "code_validation" => $code
            ], 200);
        } else {
            return response()->json([
                "code" => 422,
                "message" => "Cette adresse email n'existe pas!"
            ], 422);
        }
    }
    public function listeUsersAffecter(Request $request)
    {
        return response()->json([
            "message" => 'Liste des utilisateurs',
            "data" => User::with('affectation.role', 'typeUser', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)->orderBy('full_name', 'asc')->paginate(10),
            "status" => 200,
        ], 200);
    }
    public function listeUsersParOrganisation($idorg)
    {
        $org = Organisation::find($idorg);
        if ($org) {
            return response()->json([
                "message" => 'Liste des utilisateurs',
                "data" => User::with('affectation.role', 'typeUser', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)->paginate(),
                "status" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => 'Not found',
                "status" => 422,
            ], 422);
        }
    }


    public function InfosUserBy($idaffectation)
    {
        $affectation = AffectationModel::where('id', $idaffectation)->first();
        if ($affectation) {
            return response()->json([
                "message" => 'Liste des utilisateurs',
                "data" => $affectation::with('role', 'organisation', 'affectation.allpermission.permission')
                    ->where('deleted', 0)->first(),
                "status" => 200,
            ], 200);
        } else {
            return response()->json([
                "message" => 'Not found',
                "status" => 422,
            ], 422);
        }
    }

    public function NewUser(Request $request)
    {
        $request->validate([
            "full_name" => "required|string",
            "email" => 'required|email',
            "phone" => "required|string",
            "gender" => 'required|string',
            "orgid"  => "required|string",
        ]);

        $user = Auth::user();
        $permission = Permission::where('name', 'create_user')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $request->orgid)->first();
        $permission_user = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_user) {
                if (User::where('email', $request->email)->exists()) {
                    return response()->json([
                        "message" => 'Cette adresse est déjà utilisée!'
                    ], 402);
                } else {
                    if (User::where('phone', $request->phone)->exists()) {
                        return response()->json([
                            "message" => 'Ce numèro phone est déjà utilisée!'
                        ], 402);
                    } else {
                        if ($request->profil == "") {
                            $users = User::create([
                                "full_name" => $request->full_name,
                                "email" => $request->email,
                                "pswd" => Hash::make("000000"),
                                "phone" => $request->phone,
                                "email" => $request->email,
                                "profil" => "https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png",
                                "gender" => $request->gender,
                                "dateBorn" => $request->dateBorn,
                                "orgid" => $request->orgid,
                            ]);
                            $type =  Type_users::where('name', 'user')->first();
                            if ($user) {
                                User_has_Type::create([
                                    'userid' => $users->id,
                                    'typeid' => $type->id,
                                ]);
                            }
                            Mail::to($request->email)->send(new Createcount($request->email, "000000"));
                            Log::channel(channel: 'slack')->critical(message: $users);
                            return response()->json([
                                "message" => 'Utilisateur créer avec succès!',
                                "status" => 200,
                            ], 200);
                        } else {
                            $image = UtilController::uploadImageUrl($request->image, '/uploads/user/');
                            $users = User::create([
                                "full_name" => $request->full_name,
                                "email" => $request->email,
                                "pswd" => Hash::make("000000"),
                                "phone" => $request->phone,
                                "email" => $request->email,
                                "profil" => $image,
                                "gender" => $request->gender,
                                "dateBorn" => $request->dateBorn,
                                "orgid" => $request->orgid,
                            ]);
                            $type =  Type_users::where('name', 'admin')->first();
                            if ($user) {
                                User_has_Type::create([
                                    'userid' => $users->id,
                                    'typeid' => $type->id,
                                ]);
                            }
                            Mail::to($request->email)->send(new Createcount($request->email, "000000"));
                            Log::channel(channel: 'slack')->critical(message: $users);
                            return response()->json([
                                "message" => 'Utilisateur créer avec succès!',
                                "status" => 200,
                            ], 200);
                        }
                    }
                }
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action",
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                "code" => 402
            ], 402);
        }
    }
    public function Register(Request $request)
    {
        $request->validate([
            "full_name" => "required|string",
            "email" => 'required|email',
            "phone" => "required|string",
            "pswd" => [
                'required',
                'min:8',
            ],

        ]);

        if ($request->orgid == null) {
            $request->orgid = '9a280ab0-1b61-4e17-a4f8-75b67807d346';
            if (Organisation::where('id', $request->orgid)->exists()) {
                if (User::where('email', $request->email)->exists()) {
                    return response()->json([
                        "message" => 'Cette adresse est déjà utilisée!'
                    ], 402);
                } else {
                    $codeValidation = (codeValidation::where('email', $request->email)->exists() ||
                        codeValidation::where('status', 1));
                    $codeVal = (codeValidation::where('code', $request->code)->exists());

                    if ($request->code == false || $codeVal == null) {
                        if ($codeValidation == true) {
                            $code = UtilController::generateCode();
                            $val = CodeValidation::where('email', $request->email)->first();
                            if ($val) {
                                $val->code = $code;
                                $val->save();
                            } else {
                                codeValidation::create(['email' => $request->email, 'code' => $code]);
                            }
                            Mail::to($request->email)->send(new Verificationmail($request->email, $code));
                            return response()->json([
                                "message" => "Un code de validation vous a été envoyé à l'adresse " . $request->email,
                                "code_validation" => $code
                            ], 200);
                        }
                    } else {
                        if (User::where('phone', $request->email)->exists()) {
                            return response()->json([
                                "message" => "C'est numéro de phone existe déjà"
                            ], 402);
                        } else {
                            $codeValidation = (codeValidation::where('code', $request->code)->exists());

                            if ($codeValidation == null) {
                                return response()->json([
                                    "message" => "Code de validation invalide!"
                                ], 402);
                            } else {
                                $code = mt_rand(1, 9999);
                                $user = User::create([
                                    "full_name" => $request->full_name,
                                    "email" => $request->email,
                                    "pswd" => Hash::make($request->pswd),
                                    "phone" => $request->phone,
                                    "email" => $request->email,
                                    "gender" => null,
                                    "provider" => 0,
                                    "deleted" => 0,
                                    "status" => 1,
                                    "dateBorn" => null,
                                    "orgid" => $request->orgid,
                                    "profil" => 'https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png'
                                ]);
                                $type =  Type_users::where('name', 'user')->first();
                                if ($user) {
                                    User_has_Type::create([
                                        'userid' => $user->id,
                                        'typeid' => $type->id,
                                    ]);
                                }

                                $change = CodeValidation::where('code', $request->code)->first();
                                $change->update([
                                    "status" => 1,
                                ]);

                                Mail::to($request->email)->send(new Createcount($request->email, $request->pswd));
                                $token = $user->createToken("accessToken")->plainTextToken;
                                Log::channel(channel: 'slack')->critical(message: $user);
                                if ($request->token) {
                                    $token_data = TokenUsers::where('token', $request->token)->first();

                                    if ($token_data != null) {
                                        $token_data->userid = $user->id;
                                        $token_data->save();
                                    } else {
                                        if ($token_data != null && $token_data->userid == $user->id) {
                                            $token_data->userid = $user->id;
                                            $token_data->save();
                                        } else {
                                            TokenUsers::create([
                                                'token' => $request->token,
                                                'userid' => $user->id
                                            ]);
                                        }
                                    }
                                }
                                return response()->json([
                                    "message" => 'success',
                                    "data" => $user::with('affectation.role', 'typeUser', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                                        ->where('id', $user->id)->first(),
                                    "status" => 1,
                                    "token" => $token
                                ], 200);
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    "message" => 'Cette organisation n\'est pas reconnue dans le système',
                    "code" => 402
                ], 402);
            }
        } else {
            if (Organisation::where('id', $request->orgid)->exists()) {
                if (User::where('email', $request->email)->exists()) {
                    return response()->json([
                        "message" => 'Cette adresse est déjà utilisée!'
                    ], 402);
                } else {
                    $codeValidation = (codeValidation::where('email', $request->email)->exists() ||
                        codeValidation::where('status', 1));
                    $codeVal = (codeValidation::where('code', $request->code)->exists());

                    if ($request->code == false || $codeVal == null) {
                        if ($codeValidation == true) {
                            $code = UtilController::generateCode();
                            $val = CodeValidation::where('email', $request->email)->first();
                            if ($val) {
                                $val->code = $code;
                                $val->save();
                            } else {
                                codeValidation::create(['email' => $request->email, 'code' => $code]);
                            }
                            Mail::to($request->email)->send(new Verificationmail($request->email, $code));
                            return response()->json([
                                "message" => "Un code de validation vous a été envoyé à l'adresse " . $request->email,
                                "code_validation" => $code
                            ], 200);
                        }
                    } else {
                        if (User::where('phone', $request->email)->exists()) {
                            return response()->json([
                                "message" => "C'est numéro de phone existe déjà"
                            ], 402);
                        } else {
                            $codeValidation = (codeValidation::where('code', $request->code)->exists());

                            if ($codeValidation == null) {
                                return response()->json([
                                    "message" => "Code de validation invalide!"
                                ], 402);
                            } else {
                                $code = mt_rand(1, 999999);
                                $user = User::create([
                                    "full_name" => $request->full_name,
                                    "email" => $request->email,
                                    "pswd" => Hash::make($request->pswd),
                                    "phone" => $request->phone,
                                    "email" => $request->email,
                                    "gender" => null,
                                    "provider" => 0,
                                    "deleted" => 0,
                                    "dateBorn" => null,
                                    "orgid" => $request->orgid,
                                    "profil" => 'https://apiafiagap.cosamed.org/public/uploads/user/a01f3ca6e3e4ece8e1a30696f52844bc.png'
                                ]);
                                $type =  Type_users::where('name', 'user')->first();
                                if ($user) {
                                    User_has_Type::create([
                                        'userid' => $user->id,
                                        'typeid' => $type->id,
                                    ]);
                                }
                                $change = CodeValidation::where('code', $request->code)->first();
                                $change->update([
                                    "status" => 1,
                                ]);
                                Mail::to($request->email)->send(new Createcount($request->email, $request->pswd));
                                $token = $user->createToken("accessToken")->plainTextToken;
                                Log::channel(channel: 'slack')->critical(message: $user);
                                if ($request->token) {
                                    $token_data = TokenUsers::where('token', $request->token)->first();
                                    if ($token_data != null) {
                                        $token_data->userid = $user->id;
                                        $token_data->save();
                                    } else {
                                        if ($token_data != null && $token_data->userid == $user->id) {
                                            $token_data->userid = $user->id;
                                            $token_data->save();
                                        } else {
                                            TokenUsers::create([
                                                'token' => $request->token,
                                                'userid' => $user->id
                                            ]);
                                        }
                                    }
                                }
                                return response()->json([
                                    "message" => 'success',
                                    "data" => $user::with('affectation.role', 'typeUser','affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                                        ->where('id', $user->id)->first(),
                                    "status" => 1,
                                    "token" => $token
                                ], 200);
                            }
                        }
                    }
                }
            } else {
                return response()->json([
                    "message" => 'Cette organisation n\'est pas reconnue dans le système',
                    "code" => 402
                ], 402);
            }
        }
    }

    public function addfingerprint(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'fingerprint' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($request->fingerprint == null) {
                $user->fingerprint = $user->fingerprint;
            } else {
                $user->fingerprint = $request->fingerprint;
            }
            $user->save();
            return response()->json([
                "message" => "Fingerprint modifier avec succès",
                "data" => $user::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                    ->where('id', $user->id)->first(),
            ], 200);
        } else {
            return response()->json([
                "message" => "Identifiant incorrect"
            ], 422);
        }
    }

    public function Test_code_validation(Request $request)
    {
        $request->validate([
            "code" => 'required',
        ]);
        $code = codeValidation::where('code', $request->code)->first();

        if (codeValidation::where('code', $request->code)->exists()) {

            if (codeValidation::where('status', 1)->exists() == true) {
                $code->update([
                    'status' => 0,
                ]);
                return response()->json([
                    "message" => 'Code de validation correct!',
                    "code" => 200
                ], 200);
            } else {
                return response()->json([
                    "message" => 'code invalide',
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => 'code invalide',
                "code" => 402
            ], 402);
        }
    }

    public function Lost_pswd(Request $request)
    {
        $request->validate([
            "email" => 'required|email',
            "pswd" => [
                'required',
                'min:8',
            ],
            "pswdconfirm" => [
                'required',
                'min:8',
            ],
        ]);
        if (User::where('email', $request->email)->exists() == false) {
            return response()->json([
                "message" => 'Cette adresse n\'existe pas'
            ], 402);
        } else {
            if ($request->pswd != $request->pswdconfirm) {
                return response()->json([
                    "message" => 'Mot de passe n\'est pas identique'
                ], 402);
            } else {
                $change = User::where('email', $request->email)->first();
                $change->update([
                    "pswd" => Hash::make($request->pswd),
                ]);
                Mail::to($request->email)->send(new NewPswd($request->email, $request->pswd));
                return response()->json([
                    "message" => "Votre mot de passe à été modifier avec succès.",
                    "code" => 200,
                ], 200);
            }
        }
    }

    public function AuthProvider(Request $request)
    {
        $request->validate([
            "email" => "required|email",
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                "full_name" => $request->fullname,
                "email" => $request->email,
                "provider" => 1,
                "profil" => $request->image
            ]);
            $type =  Type_users::where('name', 'user')->first();
            if ($user) {
                User_has_Type::create([
                    'userid' => $user->id,
                    'typeid' => $type->id,
                ]);
            }
            $token = $user->createToken("accessToken")->plainTextToken;
            Log::channel(channel: 'slack')->critical(message: $user);
            return response()->json([
                "message" => 'success',
                "data" => $user::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')
                    ->where('id', $user->id)->where('deleted', 0)->first(),
                "status" => 1,
                "token" => $token
            ], 200);
        } else {
            $user = User::create([
                "full_name" => $request->fullname,
                "email" => $request->email,
                "provider" => 1,
                "profil" => $request->image
            ]);
            $type =  Type_users::where('name', 'user')->first();
            if ($user) {
                User_has_Type::create([
                    'userid' => $user->id,
                    'typeid' => $type->id,
                ]);
            }
            $token = $user->createToken("accessToken")->plainTextToken;
            Log::channel(channel: 'slack')->critical(message: $user);
            return response()->json([
                "message" => 'success',
                "data" => $user,
                "status" => 1,
                "token" => $token
            ], 200);
        }
    }
    public function changePswdProfil(Request $request)
    {
        $request->validate([
            "old_pswd" => "required",
            "new_pass" => "required"
        ]);

        if (Auth::user()) {
            $datauser = Auth::user();
            $user = User::find($datauser->id);
            if (Hash::check($request->old_pswd, $user->pswd)) {
                $user->update([
                    "pswd" => Hash::make($request->new_pass)
                ]);
                return response()->json([
                    "message" => "Modification mot de passe réussie!",
                    "code" => 200
                ], 200);
            } else {
                return response()->json([
                    "message" => "Ancien mot de passe incorrect!",
                    "code" => 422
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "Ta session a expirer",
                "code" => 422
            ], 422);
        }
    }
    public function editProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'email' => 'required|email|unique:t_users,email,' . $user->id,
        ]);
        if (!Auth::user()) {
            return response()->json([
                "message" => "Identifant incorrect"
            ], 422);
        } else {
            if ($user) {
                if ($request->full_name == null) {
                    $user->full_name = $user->full_name;
                } else {
                    $user->full_name = $request->full_name;
                }
                if ($request->phone == null) {
                    $user->phone = $user->phone;
                } else {
                    $user->phone = $request->phone;
                }
                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }
                if ($request->dateBorn == null) {
                    $user->dateBorn = $user->dateBorn;
                } else {
                    $user->dateBorn = $request->dateBorn;
                }
                if ($request->email == null) {
                    $user->email = $user->email;
                } else {
                    $user->email = $request->email;
                }
                $user->save();
                return response()->json([
                    "message" => "Profile modifier avec succès",
                    "data" => $user::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                        ->where('id', $user->id)->first(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Identifiant incorrect"
                ], 422);
            }
        }
    }
    public function UpdateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        $request->validate([
            'email' => 'required|email|unique:t_users,email,' . $user->id,
        ]);
        if ($id == null) {
            return response()->json([
                "message" => "Identifant incorrect"
            ], 422);
        } else {

            if ($user) {
                if ($request->full_name == null) {
                    $user->full_name = $user->full_name;
                } else {
                    $user->full_name = $request->full_name;
                }
                if ($request->phone == null) {
                    $user->phone = $user->phone;
                } else {
                    $user->phone = $request->phone;
                }
                if ($request->gender == null) {
                    $user->gender = $user->gender;
                } else {
                    $user->gender = $request->gender;
                }
                if ($request->dateBorn == null) {
                    $user->dateBorn = $user->dateBorn;
                } else {
                    $user->dateBorn = $request->dateBorn;
                }
                if ($request->email == null) {
                    $user->email = $user->email;
                } else {
                    $user->email = $request->email;
                }
                $user->save();
                return response()->json([
                    "message" => "Profile modifier avec succès",
                    "data" => $user::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)->orderBy('updated_at', 'desc')->get(),
                ], 200);
            } else {
                return response()->json([
                    "message" => "Identifiant incorrect"
                ], 422);
            }
        }
    }
    public function SupprimerUser(Request $request, $userid, $orgid)
    {
        $user = Auth::user();
        $permission = Permission::where('name', 'delete_user')->first();
        $organisation = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $affectationuser = AffectationModel::where('userid', $user->id)->where('orgid', $orgid)->first();
        $permission_gap = AffectationPermission::with('permission')->where('permissionid', $permission->id)
            ->where('affectationid', $affectationuser->id)->where('deleted', 0)->where('status', 0)->first();
        if ($organisation) {
            if ($permission_gap) {
                $users = User::where('id', $userid)->first();
                if ($users) {
                    if ($users->deleted == 0) {
                        $users->deleted = 1;
                        $users->save();
                        return response()->json([
                            "message" => "Utilisateur est supprimé avec succès",
                            "data" => User::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)->orderBy('full_name', 'asc')->paginate(10),

                        ], 200);
                    } else {
                        $users->deleted = 0;
                        $users->save();
                        return response()->json([
                            "message" => "Vous venez de restorer cet utilisateur!",
                            "data" => User::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)->orderBy('full_name', 'asc')->paginate(10),
                        ], 200);
                    }
                } else {
                    return response()->json([
                        "message" => "cette userid n'existe pas",
                        "code" => 402
                    ], 402);
                }
            } else {

                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action",
                    "code" => 402
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "cette organisationid" . $organisation->id . "n'existe pas",
                "code" => 402
            ], 402);
        }
    }

    public function editImage(Request $request)
    {
        $request->validate([
            "image" => "required|image"
        ]);

        $image = UtilController::uploadImageUrl($request->image, '/uploads/user/');
        $user = Auth::user();
        $user->profil = $image;
        $user->provider = 1;
        $user->save();
        return response()->json([
            "message" => 'Photo de profile mise à jour',
            "status" => 1,
            "data" => $user::with('affectation.role', 'affectation.organisation', 'affectation.allpermission.permission')->where('deleted', 0)
                ->where('id', $user->id)->first(),
        ], 200);
    }

    public function add_type_user(Request $request)
    {
        $request->validate([
            "name" => "required|string|unique:type_user,name"
        ]);

        $type = Type_users::create([
            "name" => $request->name
        ]);

        return response()->json([
            "message" => 'Type d\'utilisateur créé avec succès',
            "status" => 1,
            "data" => $type
        ], 200);
    }

    public function update_type_user(Request $request, $id)
    {
        $type = Type_users::where('id', $id)->first();
        if ($type) {
            $type->name = $request->name;
            $type->save();
            return response()->json([
                "message" => 'Type d\'utilisateur modifié avec succès',
                "code" => 200,
                "data" => $type
            ], 200);
        } else {
            return response()->json([
                "message" => 'Type d\'utilisateur non trouvé',
                "code" => 404
            ], 404);
        }
    }
    public function status(Request $request, $id)
    {
        $request->validate([
            "status" => "required|boolean"
        ]);

        $type = Type_users::where('id', $id)->first();
        if ($type) {
            $type->status = $request->status;
            $type->save();
            return response()->json([
                "message" => 'Type d\'utilisateur modifié avec succès',
                "code" => 200,
                "data" => $type
            ], 200);
        } else {
            return response()->json([
                "message" => 'Type d\'utilisateur non trouvé',
                "code" => 404
            ], 404);
        }
    }

    public function delete($id)
    {
        $type = Type_users::where('id', $id)->first();
        if ($type) {
            $type->deleted = 1;
            $type->save();
            return response()->json([
                "message" => 'Type d\'utilisateur modifié avec succès',
                "code" => 200,
                "data" => $type
            ], 200);
        } else {
            return response()->json([
                "message" => 'Type d\'utilisateur non trouvé',
                "code" => 404
            ], 404);
        }
    }

    public function index()
    {
        return response()->json([
            "message" => 'Liste des types d\'utilisateurs',
            "code" => 200,
            "data" => Type_users::where('status', 1)->where('deleted', 0)->get()
        ], 200);
    }
}
