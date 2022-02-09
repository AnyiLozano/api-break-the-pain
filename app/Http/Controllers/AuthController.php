<?php

namespace App\Http\Controllers;

use App\Models\FinishedLevel;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthController extends Controller
{
    /**
     * This function is used from register a new user to the game.
     * @param string "$request->fullname", with the full name of the new user.
     * @param string "$request->email", with the email of the new user.
     * @param string "$request->phone", with the phone of the new user.
     * @param string "$request->pharmacy", with the pharmacy of the new user.
     * @return array with the response of the request.
     */
    public function register(Request $request)
    {
        $status = false;
        $result = null;
        DB::beginTransaction();
        try {
            $beforeUser = User::where('fullname', $request->fullname)->first();

            if (isset($beforeUser->id)) {
                return $this->response(false, array('type' => 'error', 'content' => 'Ya existe un usuario con el mismo nombre intenta ingresando con tu nombre completo'), array());
            }

            $pharmacy = Pharmacy::where('name', 'LIKE', $request->pharmacy)->first();

            $user = null;

            if (isset($pharmacy->id)) {
                $user = new User();
                $user->uid = Hash::make($request->fullname);
                $user->fullname = $request->fullname;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = Hash::make('password');
                $user->status_id = $this->active_status;
                $user->pharmacy_id = $pharmacy->id;
                $user->save();
            
            } else {
                $newPharmacy = new Pharmacy();
                $newPharmacy->name = $request->pharmacy;
                $newPharmacy->status_id = $this->active_status;
                $newPharmacy->save();

                $user = new User();
                $user->uid = Hash::make($request->fullname);
                $user->fullname = $request->fullname;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->password = Hash::make('password');
                $user->status_id = $this->active_status;
                $user->pharmacy_id = $newPharmacy->id;
                $user->save();
            }

            $status = true;
            DB::commit();
        } catch (\Throwable $th) {
            $result = $th->getMessage();
            DB::rollBack();
        }
        if ($status) {
            return $this->response(true, array('type' => 'success', 'content' => 'Se creo el usuario exitosamente.'), $user);
        } else {
            return $this->response(false, array('type' => 'error', 'content' => 'Ocurrio un problema al momento de crear el usuario.'), $result);
        }
    }

    /**
     * This function is used from the user can login in to de game.
     * @param string $request- >fullname, with the fullname of the user to login.
     * @param string $request- >password, with the password of the user to login.
     * @return Array with the response obtained in the request.
     */
    public function login(Request $request)
    {
        $user = User::where('fullname', $request->fullname)->first();

        if (isset($user->id) && $user->status_id === $this->inactive_status) {
            return $this->response(false, array('type' => 'error', 'content' => 'No estas autorizado para poder ingresar al juego.'), array());
        }

        if (isset($user->id)) {
            $credentials = array('email' => $user->email, 'password' => $request->password);

            if (Auth::attempt($credentials)) {
                $authUser = Auth::user();
                $levels = FinishedLevel::whereUserId($user->id)->get();

                $levelsObject = new stdClass();

                if (count($levels) > 0) {
                    foreach ($levels as $value) {
                        if ($value->level == 1) {
                            $levelsObject->level2 = true;
                        } else if ($value->level == 2) {
                            $levelsObject->level3 = true;
                        }
                    }
                } else {
                    $levelsObject->level2 = false;
                    $levelsObject->level3 = false;
                }

                $getUser = User::with('pharmacy')->find($authUser->id);
                $token = $user->createToken('Uvamin')->accessToken;
                return $this->response(true, array('type' => 'success', 'content' => 'Done.'), array('token' => $token, 'user' => $getUser, 'levels' => $levelsObject));
            } else {
                return $this->response(false, array('type' => 'error', 'content' => 'Los datos ingresados son invalidos'), array());
            }
        } else {
            return $this->response(false, array('type' => 'error', 'content' => 'Los datos ingresados son invalidos'), array());
        }
    }

    /**
     * This function is used from register a new user to the game.
     * @param string $request->fullname, with the full name of the new user.
     * @param string $request->email, with the email of the new user.
     * @param string $request->phone, with the phone of the new user.
     * @param string $request->pharmacy, with the pharmacy of the new user.
     * @return array with the response of the request.
     */
    public function editUser(Request $request)
    {
        $status = false;
        $result = null;
        DB::beginTransaction();
        try {
            $pharmacy = Pharmacy::where('name', $request->pharmacy)->first();

            $user = null;

            if (isset($pharmacy->id)) {
                $user = User::find($request->id);
                $user->uid = Hash::make($request->fullname);
                $user->fullname = $request->fullname;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->pharmacy_id = $pharmacy->id;
                $user->save();
            } else {
                $newPharmacy = new Pharmacy();
                $newPharmacy->name = $request->pharmacy;
                $newPharmacy->status_id = $this->active_status;
                $newPharmacy->save();

                $user = User::find($request->id);
                $user->uid = Hash::make($request->fullname);
                $user->fullname = $request->fullname;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->pharmacy_id = $newPharmacy->id;
                $user->save();
            }

            $finalUser = User::with('pharmacy')->find($user->id);

            $status = true;
            DB::commit();
        } catch (\Throwable $th) {
            $result = $th->getMessage();
            DB::rollBack();
        }
        if ($status) {
            return $this->response(true, array('type' => 'success', 'content' => 'Se creo el usuario exitosamente.'), $finalUser);
        } else {
            return $this->response(false, array('type' => 'error', 'content' => 'Ocurrio un problema al momento de crear el usuario.'), $result);
        }
    }
}
