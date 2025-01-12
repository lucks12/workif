<?php

namespace App\Http\Controllers;

use App\Models\Ponto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class BaterPontoController extends Controller
{
    public function index()
    {
        $pontos = Ponto::where('user_id', auth()->user()->id)->whereDate('created_at', Carbon::today())->get();
        return view("pontos.index")->with('pontos', $pontos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $hoje = Carbon::now('America/Sao_Paulo')->toDateString(); // Data atual
        $usuarioId = auth()->user()->id; // ID do usuário
    
        $periodo = null;
    
        if ($this->periodoManha()) {
            $periodo = 1;
        }
    
        if ($this->periodoTarde()) {
            $periodo = 2;
        }
    
        if ($this->periodoNoite()) {
            $periodo = 3;
        }
    
        // Verifica se já existe um ponto para o período e data atual
        $pontoExistente = Ponto::where('user_id', $usuarioId)
            ->where('periodo', $periodo)
            ->whereDate('created_at', $hoje)
            ->exists();
    
        if ($pontoExistente) {
            return redirect()->route('bater-ponto.index')
                ->with('response', 'Você já registrou um ponto para este período hoje!');
        }
    
        // Cria um novo ponto
        $ponto = new Ponto();
        $ponto->latitude = 0;
        $ponto->longitude = 0;
        $ponto->user_id = $usuarioId;
        $ponto->created_at = Carbon::now('America/Sao_Paulo');
        $ponto->updated_at = $ponto->created_at;
        $ponto->periodo = $periodo;
    
        $ponto->save();
    
        return redirect()->route('bater-ponto.index')
            ->with('response', 'Ponto batido com sucesso!');
    }

    public function fechar()
    {
        $ponto = Ponto::where('user_id', auth()->user()->id)->latest()->first();
        $ponto->saida = now('America/Sao_Paulo');
        $ponto->save();

        return redirect()->route('bater-ponto.index')
            ->with('response', 'Ponto batido com sucesso!');
    }

    private function periodoManha()
    {
        $startDate = Carbon::createFromFormat('H:i a', '06:00 AM');
        $endDate = Carbon::createFromFormat('H:i a', '12:00 AM');
        return Carbon::now('America/Sao_Paulo')->between($startDate, $endDate);
    }

    private function periodoTarde()
    {
        $startDate = Carbon::createFromFormat('H:i a', '12:00 AM');
        $endDate = Carbon::createFromFormat('H:i a', '18:30 PM');
        return Carbon::now('America/Sao_Paulo')->between($startDate, $endDate);
    }

    private function periodoNoite()
    {
        $startDate = Carbon::createFromFormat('H:i a', '18:30 PM');
        $endDate = Carbon::createFromFormat('H:i a', '23:00 PM');
        return Carbon::now('America/Sao_Paulo')->between($startDate, $endDate);
    }
}
