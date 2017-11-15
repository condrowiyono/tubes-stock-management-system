<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class Pemesanan extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cabang = \App\UserRole::where('user_id', Auth::user()->id)->first();
         $data = \App\Pemesanan::where('cabang_id', $cabang->id)->get();
        
        return view('order.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = \App\UserRole::where('user_id', Auth::user()->id)->first();

        return view('order.create',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'kode_pemesanan' => 'required',
        ]);
        
        $tambah = new \App\Pemesanan();
        $tambah->cabang_id =  $request['cabang_id'];
        $tambah->kode_pemesanan =  $request['kode_pemesanan'];
        $tambah->save();

        return redirect()->route('rincian',$tambah->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = \App\Pemesanan::findOrFail($id);
        $rincian = \App\RincianPemesanan::where('pemesanan_id',$id)->get();
        return view('order.show', compact('data', 'rincian'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = \App\Pemesanan::findOrFail($id);
        $delete->delete();
        return redirect()->route('pemesanan.index')->with('pesan','Berhasil dihapus');
    }

    public function Rincian($id)
    {
        $data = \App\Pemesanan::findOrFail($id);
        $allBarang = \App\Barang::all();
        return view('order.detail', compact('data','allBarang'));
    }

    public function Finish($id)
    {
        $data = \App\RincianPemesanan::where('pemesanan_id',$id)->get();

        $total = 0;

        foreach ($data as $d) {
            $total = $d->harga_total + $total; 
        }
        
        $simpan = \App\Pemesanan::findOrFail($id);
        $simpan->harga = $total;
        $simpan->save();

        return redirect()->route('pemesanan.index')->with('pesan','Berhasil membuat baru');
        
    }

    //OLEH ADMIN
    public function Admin()
    {
        
        $data = \App\Pemesanan::all();


        return view('admin.confirmorder.index', compact('data')) ;
        
    }
    public function GantiStatusView($id)
    {
        
        $data = \App\Pemesanan::findOrFail($id);
        $rincian = \App\RincianPemesanan::where('pemesanan_id',$id)->get();
        return view('admin.confirmorder.show', compact('data', 'rincian'));
        
    }

    public function GantiStatusStore(Request $request, $id)
    {
        $data = \App\Pemesanan::findOrFail($id);
        $rincian = \App\RincianPemesanan::where('pemesanan_id',$id)->get();

        $flag = 0;
        $stoknya = 0;

        foreach ($rincian as $r) {
            $stok = \App\Barang::where('id', $r->barang_id)->first();
            if ($stok->stok >= $r->qty) {
                $flag = 0;
            } else {
                $flag = 1;
            }
        }

        $pesan = '';

        if ($flag==0) {
            foreach ($rincian as $r) {
                $stok = \App\Barang::where('id', $r->barang_id)->first();
                    $stok->stok = $stok->stok - $r->qty;
                    $stoknya = $stok->stok;
                    $stok->save();
            }
            
            $pesan = 'Berhasil' ;
            $data->status = 1;
            $data->save();
        } else {
            $pesan = 'Gagal' ;
            $data->status = -1;
            $data->save();
        } 
        //return $stoknya;
        return redirect()->route('pemesanan-admin')->with('pesan',$pesan);

       
        
    }

}
