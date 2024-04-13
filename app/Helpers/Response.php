<?php
namespace App\Helpers;

use App\Models\Admin;
use App\Models\Student;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Log;

use function Psy\debug;

trait Response{
    public static function success($data=null,$withMessage=true,$message="Success!.",$code=200){
        //$data=collect($data);
        $res=collect();
        if($withMessage){
            $res['message']=$message;
        }
        if($data){
            if($withMessage){
                $res['data']=$data;
            }else {
                $res=$data;
            }
        }
        abort(response()->json($res,$code));
    }
    public static function error($code=400,$message="Something went wrong!.",$errors=null,$errorDetail=null,$rollback=false){
        $result=[
            'message'=>$message
        ];
        if($errors){
            $result['errors']=$errors;
        }
        if($errorDetail){
            $result['errorDetail']=$errorDetail;
        }
        $result=collect($result);
        if($rollback)
        DB::rollBack();
        abort(response()->json($result,$code));
    }
    public function queryError(QueryException $error,
        $duplicate='Duplicate error!',
        $other='Something went Wrong!',
        $rollback=false){
        $code=$error->errorInfo[1];
        $msg=$code==1062?$duplicate:$other;
        if($rollback)
        DB::rollBack();
        $this->error(422,$msg,errorDetail:$error->errorInfo[2]);
    }
    public function lazyQueryTry($toTry,$dupMsg=null
    ,$otherMsg="Something went wrong: Query Error."
    ,$rollbackOnFailure=true,$withDBTransaction=false,$rollOnAbort=true){
        try{
            if($withDBTransaction){
                DB::beginTransaction();
            }
            $result=$toTry();
        }catch(HttpResponseException $e){
            if($withDBTransaction&&$rollOnAbort)DB::rollBack();
            throw $e;
        }catch(QueryException $e){
            $code=$e->errorInfo[1];
            $this->reportError($e);
            if($code==1062 && $dupMsg!=null){
                $this->queryError($e,$dupMsg,rollback:$rollbackOnFailure||$withDBTransaction);
            }else{
                $this->queryError($e,other:$otherMsg,rollback:$rollbackOnFailure||$withDBTransaction);
            }
        }
        if($withDBTransaction){
            DB::commit();
        }
        return $result;
    }

    public function lazyTry($toTry,$customMessage=null,$withDBTransaction=false,$rollOnAbort=false){
        try{
            if($withDBTransaction){
                DB::beginTransaction();
            }
            $result= $toTry();
            if($withDBTransaction){
                DB::commit();
            }
            return $result;
        }catch(HttpResponseException $e){
            if($withDBTransaction&&$rollOnAbort)DB::rollBack();
            throw $e;
        }
        catch(Exception $e){
            $mainMessage = "Something Went Wrong, Please wait until this problem is resolved.";
            if($this->isAdmin()){
                $mainMessage=$customMessage??__('custom.unknown_error');
                $m=$e->getMessage();
                $details=[
                    'message'=>empty($m)?"No more details found!.":$m,
                    'file'=>$e->getFile().":".$e->getLine(),
                    'trace'=>$e->getTraceAsString()
                ];
            }
            $this->reportError($e);
            $this->error(
                400,
                message:$mainMessage,
                errorDetail:$details??null,
                rollback:$withDBTransaction
            );
        }
    }
    public function reportError(Exception $e,$customData=null){
        $exceptionFormat = "\nAppName-EXCEPTION \nMESSAGE:: %s \nFILE:: %s \nLINE::%s \nFull trace::%s \n\n";
        $stackTrace = $e->getTraceAsString();

        // Split the stack trace into lines
        $stackTraceLines = explode("\n", $stackTrace);
        // // Filter out lines containing Laravel's files
        $filteredStackTraceLines = array_filter($stackTraceLines, function($line) {
            return !strpos($line, "laravel\\framework\\src");
        });
        // Reconstruct the filtered stack trace string
        $filteredStackTrace = implode("\n", $filteredStackTraceLines);
        Log::error(sprintf($exceptionFormat,
            // some exceptions don't come with a message
            !empty(trim($e->getMessage()))
                ? $e->getMessage()
                : get_class($e),
            $e->getFile(),
            $e->getLine(),
            $filteredStackTrace
        ));
    }
    public function getConnectedDoctor(){
        $doctor=request()->user();
        if($doctor==null){
            self::error(message:__('custom.not_doctor'));
        }
        return $doctor;
    }
}
