<?php

/**
 * Clase que representa un miembro de la dbf miembros.DBF
 *
 * @author Ieltxu Algañarás (ieltxu.alganaras@gmail.com)
 */
class Default_Model_Miembro
{
    
    protected $nombre;
    protected $apellido;
    protected $dni;
    protected $ocupacion;
    protected $ingresos;
    protected $conNombre;
    protected $conApellido;
    protected $conDni;
    protected $conOcupacion;
    protected $conIngresos;
    protected $otrosIngresos;
    protected $domicilio;
    protected $montoSubsidio;
    protected $destinoSubsidio;
    protected $beneficiario;
    protected $dniBeneficiario;
    protected $obs;

    function __construct() {
        
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getDni() {
        return $this->dni;
    }

    public function getOcupacion() {
        return $this->ocupacion;
    }

    public function getIngresos() {
        return $this->ingresos;
    }

    public function getConNombre() {
        return $this->conNombre;
    }

    public function getConApellido() {
        return $this->conApellido;
    }

    public function getConDni() {
        return $this->conDni;
    }

    public function getConOcupacion() {
        return $this->conOcupacion;
    }

    public function getConIngresos() {
        return $this->conIngresos;
    }

    public function getOtrosIngresos() {
        return $this->otrosIngresos;
    }

    public function getDomicilio() {
        return $this->domicilio;
    }

    public function getMontoSubsidio() {
        return $this->montoSubsidio;
    }

    public function getDestinoSubsidio() {
        return $this->destinoSubsidio;
    }

    public function getBeneficiario() {
        return $this->beneficiario;
    }

    public function getDniBeneficiario() {
        return $this->dniBeneficiario;
    }

    public function getObs() {
        return $this->obs;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function setDni($dni) {
        $this->dni = $dni;
    }

    public function setOcupacion($ocupacion) {
        $this->ocupacion = $ocupacion;
    }

    public function setIngresos($ingresos) {
        $this->ingresos = $ingresos;
    }

    public function setConNombre($conNombre) {
        $this->conNombre = $conNombre;
    }

    public function setConApellido($conApellido) {
        $this->conApellido = $conApellido;
    }

    public function setConDni($conDni) {
        $this->conDni = $conDni;
    }

    public function setConOcupacion($conOcupacion) {
        $this->conOcupacion = $conOcupacion;
    }

    public function setConIngresos($conIngresos) {
        $this->conIngresos = $conIngresos;
    }

    public function setOtrosIngresos($otrosIngresos) {
        $this->otrosIngresos = $otrosIngresos;
    }

    public function setDomicilio($domicilio) {
        $this->domicilio = $domicilio;
    }

    public function setMontoSubsidio($montoSubsidio) {
        $this->montoSubsidio = $montoSubsidio;
    }

    public function setDestinoSubsidio($destinoSubsidio) {
        $this->destinoSubsidio = $destinoSubsidio;
    }

    public function setBeneficiario($beneficiario) {
        $this->beneficiario = $beneficiario;
    }

    public function setDniBeneficiario($dniBeneficiario) {
        $this->dniBeneficiario = $dniBeneficiario;
    }

    public function setObs($obs) {
        $this->obs = $obs;
    }

}

