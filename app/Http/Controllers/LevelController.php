<?php

namespace App\Http\Controllers;

use App\Models\FinishedLevel;
use App\Models\SelectionSatisfactionQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class LevelController extends Controller
{
    /**
     *
     */
    public function setLevel(Request $request): array
    {
        $status = false;
        $result = null;
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $oldLevels = FinishedLevel::where("level", $request->level_id)->whereUserId($user->id)->first();
            if (isset($oldLevels->user_id)) {
                return $this->response(true, array("type" => "success", "content" => "Este nivel ya lo termino el usuario."), array());
            }

            $levels = new FinishedLevel();
            $levels->user_id = $user->id;
            $levels->level = $request->level_id;
            $levels->save();

            $status = true;
            DB::commit();
        } catch (\Throwable $th) {
            $result = $th->getMessage();
            DB::rollBack();
        }
        if ($status) {
            return $this->response(true, array("type" => "success", "content" => "Done."), $levels);
        } else {
            return $this->response($status, array("type" => "error", "content" => "Ocurrio un problema al momento de guardar tu nivel"), $result);
        }
    }

    public function getLevels()
    {
        $user = Auth::user();
        $levels = FinishedLevel::whereUserId($user->id)->get();

        $levelsObject = new stdClass();

        if(count($levels) > 0){
            foreach($levels as $value){
                if($value->level == 1){
                    $levelsObject->level2 = true;
                }else if($value->level == 2){
                    $levelsObject->level3 = true;
                }
            }
        }else{
            $levelsObject->level2 = false;
            $levelsObject->level3 = false;
        }
        return $this->response(true, array("type" => "success", "content" => "Done."), $levelsObject);
    }

    public function setSatisfaction(Request $request)
    {
        $status = false;
        $result = null;
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $satisfaction = new SelectionSatisfactionQuestion();
            $satisfaction->user_id = $user->id;
            $satisfaction->question_id = $request->response;
            $satisfaction->save();

            $status = true;
            DB::commit();
        } catch (\Throwable $th) {
            $result = $th->getMessage();
            DB::rollBack();
        }
        if ($status) {
            return $this->response(true, array("type" => "success", "content" => "Done."), $satisfaction);
        } else {
            return $this->response($status, array("type" => "error", "content" => "Ocurrio un problema al momento de guardar tu nivel"), $result);
        }
    }
}
