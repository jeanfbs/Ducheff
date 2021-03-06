<?php 
/**
*       TECHMOB - Empresa Júnior da Faculdade de Computação - UFU 
*       
*       Controlador do Web Site
*
*       @author: Jean Fabrício <jeanufu21@gmail.com>
*       @since 12/02/2016
*       
*/
class SiteController extends BaseController{

	/*******************************************
	*  Ação que retorna a view root do web site
	********************************************/
	public function getIndex()
	{
		$cardapio = TipoPratoModel::where("deletada","<>",1)
		->orderBy("nome","asc")
		->get();

		foreach ($cardapio as $key => $value) {

			$value["foto_url"] = preg_replace("/..\//", '', $value["foto_url"],1);
			
		}
		$dados = 
		[
			"cardapio" => $cardapio
		];
		
		return View::make('site.home')->with($dados);
	}

	/*******************************************
	*  Ação que retorna a view de cardapio
	********************************************/
	public function getCardapio($cod_tipo_prato = null)
	{

		if($cod_tipo_prato == null)
			$cardapio = TipoPratoModel::orderBy("cod","asc")->get();
		else
			$cardapio = TipoPratoModel::where("cod",$cod_tipo_prato)
			->orderBy("cod","asc")
			->get();

		
		$resultado = array();

		foreach ($cardapio as $key => $value) {
			$rtmp = array();
			$rtmp["cod"] = $value["cod"];
			$rtmp["nome"] = $value["nome"];
			$rtmp["pratos"] = array();

			$pratos = PratosModel::where("deletada","<>",1)
			->where("cod_tipo_prato",$value["cod"])
			->orderBy("cod_tipo_prato","asc")
			->orderBy("nome","asc")
			->get();

			foreach ($pratos as $key_1 => $value_1) {
				$tmp = array();
				$tmp["cod"] = $value_1["cod"];
				$tmp["nome"] = $value_1["nome"];
				$tmp["valor"] = number_format((float)$value_1["valor"], 2, '.', '');
				$tmp["descricao"] = $value_1["descricao"];
				if($value_1["foto_url"] != null)
					$tmp["foto_url"] = preg_replace("/..\//", '', $value_1["foto_url"],1);
				else
					$tmp["foto_url"] = null;
				array_push($rtmp["pratos"], $tmp);

			}

			array_push($resultado, $rtmp);
		}
		
		$dados = 
		[
			"resultado" => $resultado
		];

		return View::make('site.cardapio')->with($dados);

	}

	/*******************************************
	*  Ação que retorna a view Donwload App
	********************************************/
	public function getAplicativo()
	{
		return View::make("site.app");
	}
	/*******************************************
	*  Ação que retorna a view Contatos
	********************************************/
	public function getContato()
	{
		return View::make("site.contato");
	}
	/*******************************************
	*  Ação que envia um email com a mensagem do 
	*  usuario do site.
	********************************************/
	public function postContato()
	{
		$dados = Input::all();

		$data = [
		'nome' => $dados["nome"],
		'email' => $dados["email"],
		'assunto' => $dados["assunto"],
		'msg' => $dados["msg"]
		];

		/* ENVIA O EMAIL */
			Mail::send('emails.email',$data , function($m) use($dados){
				$m->to("jeanufu21@gmail.com")->subject($dados["assunto"]);
			});
			
		$data = 
		[
			"msg" => Lang::get('geral.msg_contatos')
		];
		return View::make("site.contato")->with($data);
	}


}