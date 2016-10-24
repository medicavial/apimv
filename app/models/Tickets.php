<?php 

class Tickets extends Eloquent {
	public $timestamps = false;
	protected $connection = 'mysql';
    protected $table = 'TicketSeguimiento';
    protected $primaryKey ='TSeg_clave';

    public function scopeFecha($query,$fechaini,$fechafin)
    {
        return $query->leftJoin('TicketCat', 'TicketCat.TCat_clave', '=', 'TicketSeguimiento.TCat_clave')
				->leftJoin('TicketSubcat',  'TicketSubcat.TSub_clave', '=', 'TicketSeguimiento.TSub_clave')
				->leftJoin('TicketStatus', 'TicketStatus.TStatus_clave', '=', 'TicketSeguimiento.TStatus_clave')
				->leftJoin('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->leftJoin('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->leftJoin('Compania', 'Compania.Cia_clave', '=', 'TicketSeguimiento.Cia_clave')
				->leftJoin('Usuario', 'Usuario.Usu_login', '=', 'TicketSeguimiento.Usu_registro')
				->select(DB::raw('TSeg_clave as Folio_Interno, TicketSeguimiento.Exp_folio as Folio_Web, TSeg_etapa as Etapa, TCat_nombre as Categoria, TSub_nombre as Subcategoria,
									TStatus_nombre as Status, TSeg_obs as Observaciones, UNI_nombreMV as Unidad, TSeg_Asignado as Asignado, TSeg_fechareg as Registro,
									Usu_nombre as Usuario_Registro, Tseg_fechaactualizacion as Ultima_Actualizacion, CONCAT(Exp_nombre," ", Exp_paterno," ", Exp_materno) As Lesionado,Cia_nombrecorto as Cliente,
									TicketSeguimiento.Cia_clave, Usuario.Usu_login, TicketSeguimiento.Uni_clave,TicketSeguimiento.TStatus_clave,(SELECT TN_descripcion FROM TicketNotas where EXP_folio = TicketSeguimiento.Exp_folio limit 1) As Observaciones'))
				->whereBetween('TSeg_fechareg', array($fechaini, $fechafin))
				->orderBy('TSeg_clave')
				->get();
    }

    public function scopeFolio($query,$folio)
    {
        return $query->leftJoin('TicketCat', 'TicketCat.TCat_clave', '=', 'TicketSeguimiento.TCat_clave')
				->leftJoin('TicketSubcat',  'TicketSubcat.TSub_clave', '=', 'TicketSeguimiento.TSub_clave')
				->leftJoin('TicketStatus', 'TicketStatus.TStatus_clave', '=', 'TicketSeguimiento.TStatus_clave')
				->leftJoin('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->leftJoin('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->leftJoin('Compania', 'Compania.Cia_clave', '=', 'TicketSeguimiento.Cia_clave')
				->leftJoin('Usuario', 'Usuario.Usu_login', '=', 'TicketSeguimiento.Usu_registro')
				->select(DB::raw('TSeg_clave as Folio_Interno, TicketSeguimiento.Exp_folio as Folio_Web, TSeg_etapa as Etapa, TCat_nombre as Categoria, TSub_nombre as Subcategoria,
									TStatus_nombre as Status, TSeg_obs as Observaciones, UNI_nombreMV as Unidad, TSeg_Asignado as Asignado, TSeg_fechareg as Registro,
									Usu_nombre as Usuario_Registro, Tseg_fechaactualizacion as Ultima_Actualizacion, CONCAT(Exp_nombre," ", Exp_paterno," ", Exp_materno) As Lesionado,Cia_nombrecorto as Cliente,
									TicketSeguimiento.Cia_clave, Usuario.Usu_login, TicketSeguimiento.Uni_clave,TicketSeguimiento.TStatus_clave'))
				->where('TicketSeguimiento.Exp_folio', $folio)
				->orderBy('TSeg_clave')
				->get();
    }

    public function scopeInterno($query,$folio)
    {
        return $query->leftJoin('TicketCat', 'TicketCat.TCat_clave', '=', 'TicketSeguimiento.TCat_clave')
				->leftJoin('TicketSubcat',  'TicketSubcat.TSub_clave', '=', 'TicketSeguimiento.TSub_clave')
				->leftJoin('TicketStatus', 'TicketStatus.TStatus_clave', '=', 'TicketSeguimiento.TStatus_clave')
				->leftJoin('Expediente', 'Expediente.Exp_folio', '=', 'TicketSeguimiento.Exp_folio')
				->leftJoin('Unidad', 'Unidad.Uni_clave', '=', 'Expediente.Uni_clave')
				->leftJoin('Compania', 'Compania.Cia_clave', '=', 'TicketSeguimiento.Cia_clave')
				->leftJoin('Usuario', 'Usuario.Usu_login', '=', 'TicketSeguimiento.Usu_registro')
				->select(DB::raw('TSeg_clave as Folio_Interno, TicketSeguimiento.Exp_folio as Folio_Web, TSeg_etapa as Etapa, TCat_nombre as Categoria, TSub_nombre as Subcategoria,
									TStatus_nombre as Status, TSeg_obs as Observaciones, UNI_nombreMV as Unidad, TSeg_Asignado as Asignado, TSeg_fechareg as Registro,
									Usu_nombre as Usuario_Registro, Tseg_fechaactualizacion as Ultima_Actualizacion, CONCAT(Exp_nombre," ", Exp_paterno," ", Exp_materno) As Lesionado,Cia_nombrecorto as Cliente,
									TicketSeguimiento.Cia_clave, Usuario.Usu_login, TicketSeguimiento.Uni_clave,TicketSeguimiento.TStatus_clave'))
				->where('TSeg_clave',$folio)
				->orderBy('TSeg_clave')
				->get();
    }


}


		
