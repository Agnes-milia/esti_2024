<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\CallLike;

class LendingController extends Controller
{
    public function index(){
        return Lending::all();
    }
    
    public function show ($user_id, $copy_id, $start)
    {
        $lending = Lending::where('user_id', $user_id)
        ->where('copy_id', $copy_id)
        ->where('start', $start)
        ->get();
        return $lending[0];
    }

    public function store(Request $request){
        $record = new Lending();
        $record->fill($request->all());
        $record->save();
    }

    public function storeAuth(Request $request){
        $user = Auth::user();
        $record = new Lending();
        $record->user_id = $user->id;
        $record->copy_id = $request->copy_id;
        $record->start = date(now());
        $record->warning = 0;
        $record->extension = 0;
        $record->save();
    }

    public function update(Request $request, $user_id, $copy_id, $start){
        $record = $this->show($user_id, $copy_id, $start);
        $record->fill($request->all());
        $record->save();
    }

    public function destroy($user_id, $copy_id, $start){
        $this->show($user_id, $copy_id, $start)->delete();
    }

    //összes kölcsönzési adatok a példányok adataival
    public function lendingsWithCopies(){
        $records = Lending::with('toCopies')->get();
        return $records;
    }

    //bejelentkezett felhasználó összes kölcsönzési adatai a példányok adataival
    public function userLendingsWithCopies(){
        $user = Auth::user();
        $records = Lending::with('toCopies')
        ->where('user_id', $user->id)
        ->get();
        return $records;
    }

    public function lendingWithUsers($date){
        $records = Lending::with('lendingtoUsers')
        ->where('start', '=', $date)
        ->get();

        return $records;
    }

    //hány kölcsönzése van a bej-tt felh-nak?
    public function lendingCount(){
        //bej-tt felh-ó
        $user = Auth::user();
        $count = DB::table('lendings')
        ->where("user_id", "=", $user->id)
        ->count();

        return $count;
    }

    //hány könyvkölcsönzése volt/van a bej-tt felh-nak?
    public function lendingBookCount(){
        //bej-tt felh-ó
        $user = Auth::user();
        $count = DB::table('lendings as l')
        ->join('copies as c', 'l.copy_id', 'c.copy_id')
        ->where("l.user_id", "=", $user->id)
        ->distinct('book_id')
        ->count();

        return $count;
    }

    //Hány példány van nálam - nyers?
    public function lendingCountWithMe(){
        //bej-tt felh-ó
        $user = Auth::user();
        $count = DB::select("SELECT 
            COUNT(*)
            FROM lendings
            WHERE end is null
            and user_id = $user->id");
        
        return $count;
    }


    //Hány példány van nálam?
    public function lendingCountWithMe2(){
        //bej-tt felh-ó
        $user = Auth::user();
        $count = DB::table("lendings")
        ->whereNull('end')
        ->where("user_id" , $user->id)
        ->count();
        
        return $count;
    }

    //Milyen könyvek vannak nálam? (szerző, cím, book_id)
    public function bookWithMe(){
        //bej-tt felh-ó
        $user = Auth::user();
        $records = DB::table('lendings as l')
        //ha nincs select, akkor minden adattal visszatér
        ->select('author', 'title')
        ->join('copies as c', 'l.copy_id', 'c.copy_id')
        ->join('books as b', 'c.book_id', 'b.book_id')
        ->where("l.user_id", "=", $user->id)
        ->whereNull('end')
        ->get();

        return $records;
    }

    public function bringBack($copy_id, $start){
        $user = Auth::user();
        $record = $this->show($user->id, $copy_id, $start);
        $record->end = date(now());
        $record->save();
        //másik esemény
        DB::table('copies')
        ->where('copy_id', $copy_id)
        //0: könyvtárban, 1: felh-nál, 2: selejtes
        ->update(['status' => 0]);
    }

    public function bringBack2($copy_id, $start){
        $user = Auth::user();
        $record = $this->show($user->id, $copy_id, $start);
        $record->end = date(now());
        $record->save();
        //másik esemény
        /* DB::table('copies')
        ->where('copy_id', $copy_id)
        //0: könyvtárban, 1: felh-nál, 2: selejtes
        ->update(['status' => 0]); */
        //eljárással, mindig DB::select
        DB::select('CALL toStore(?)', [$copy_id]);
    }
}
