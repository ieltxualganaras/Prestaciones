<?php
include APPLICATION_PATH . '/../public/mpdf/mpdf.php';

class Default_MiembrosController extends Zend_Controller_Action
{

    /**
     * @var Default_Model_Miembro()
     *
     */
    private $_solicitante = null;

    private $_dbfpath = null;

    private $_grupo = null;

    private $_ingresosGrupo = null;

    private $_existePareja = null;

    public function init()
    {
        $this->_dbfpath = APPLICATION_PATH . '/../public/dbfs/';
        $this->_grupo = array();
        $this->_solicitante = new Default_Model_Miembro();
    }

    public function indexAction()
    {
        
    }

    public function consultarAction()
    {
        $form = new Twitter_Bootstrap_Form_Search(array(
            'renderInNavBar' => false,
            'inputName' => 'apros',
            'submitLabel' => 'Ingrese el N° de APROS'
        ));
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            //aca validar mediante expresion regular que sea un numero
            if (true === true) {
                $apros = $this->_getParam('apros');
                $db = dbase_open($this->_dbfpath . 'cobertura.DBF', 0);
                $observaciones = 'SIN OBSERVACIONES';
                if($this->_getParam('observaciones')) {
                    $observaciones = $this->_getParam('observaciones');
                }
                $otrosIngresosNum = $this->_getParam('ingresosNum');
                $otrosIngresosLetras = $this->_getParam('ingresosLetras');
                if ($db) {
                    $filas = dbase_numrecords($db);
                    for ($i = 1; $i <= $filas; $i++) {

                        $temp = dbase_get_record($db, $i);

                        if (in_array($apros, $temp)) {
                             //if (in_array("20131010", $temp)) {
                            //buscamos a todos los miembros del grupo familiar y los
                            //almacenamos en la variable de clase $_grupo
                            $this->buscarMiembros($apros, $temp[1]);
                            $this->calcularIngresosGrupo(); //ya tengo los ingresos totales de todo el grupo
                            $this->_existePareja = $this->determinarMiembroYConyuge($temp[1]); //se quienes son el solicitante y cónyuge
                            $this->_solicitante->setDomicilio($this->buscarHogar($apros));
                            $this->_solicitante->setMontoSubsidio($temp[9]);
                            $this->_solicitante->setDestinoSubsidio($this->buscarDestino($temp[5]));
                            $this->_solicitante->setObs($observaciones);
                            $planilla = $this->prepararPlanilla($apros,$otrosIngresosNum,$otrosIngresosLetras);
                            
                            $sesion = new Zend_Session_Namespace('apros');
                            $sesion->planilla = $planilla;
                            $this->_helper->redirector('apros');
                         //   }
                        }
                    }
                }
                dbase_close($db);
            }
        }
    }

    /**
     * Devuelve los miembros del grupo familiar.
     *
     * Esta función almacena, en la variable de clase $_grupo, a todos los 
     * registros de cada uno de los integrantes del grupo familiar relacionado
     * con la prestación.
     *
     *
     * @param integer $apros número de apros
     * @param integer $codMiembro representa el codigo miembro dentro del grupo
     * familiar
     *
     * @return void
     *
     */
    private function buscarMiembros($apros, $codMiembro)
    {

        $db = dbase_open($this->_dbfpath . 'miembros.DBF', 0);
        if ($db) {
            $filas = dbase_numrecords($db);
            for ($i = 1; $i <= $filas; $i++) {
                $temp = dbase_get_record($db, $i);
                if (in_array($apros, $temp)) {
                    $this->_grupo[] = dbase_get_record($db, $i);
                    unset($temp[$i]);
                    //$this->_miembro = new Default_Model_Miembro();
                    //$this->buscarPorCodMiembro($temp, $codMiembro);
                    //return $this->_miembro;
                }
            }
        }
        dbase_close($db);
    }

    private function buscarOcupacion($codOcupacion)
    {

        $db = dbase_open($this->_dbfpath . 'ACTIVIDADES.DBF', 0);
        if ($db) {
            $filas = dbase_numrecords($db);
            for ($i = 1; $i <= $filas; $i++) {
                $temp = dbase_get_record($db, $i);
                if ($temp[0] === $codOcupacion) {
                    return $temp[1];
                }
            }
        }
        dbase_close($db);
    }

    private function buscarHogar($apros)
    {
        $db = dbase_open($this->_dbfpath . 'hogares8.dbf', 0);
        if ($db) {
            $filas = dbase_numrecords($db);
            for ($i = 1; $i <= $filas; $i++) {
                $temp = dbase_get_record($db, $i);
                if ($temp[0] === $apros) {
                    return $temp[8] . ' ' . $temp[9] . ' ' . $temp[15] . ' ' . $temp[10] . ' ' . $temp[11];
                }
            }
        }
        dbase_close($db);
    }

    private function buscarDestino($codPrestacion)
    {
        $db = dbase_open($this->_dbfpath . 'prestaciones.dbf', 0);
        if ($db) {
            $filas = dbase_numrecords($db);
            for ($i = 1; $i <= $filas; $i++) {
                $temp = dbase_get_record($db, $i);
                echo $temp[0] . $codPrestacion;
                if ($temp[0] === $codPrestacion) {
                    var_dump($temp);
                    dbase_close($db);
                    return $temp[1];
                }
            }
        }
        dbase_close($db);
    }

    /**
     * calcula los ingresos del grupo familiar 
     *
     * @return boolean 
     *
     */
    public function calcularIngresosGrupo()
    {
        $ingresosAux = 0.0;
        foreach ($this->_grupo as $index => $miembro) {
            if (($miembro[19])) {
                $this->_grupo[$index]['ingresos'] = $miembro[19];
                $ingresosAux = $ingresosAux + $miembro[19];
            }
        }
        $this->_ingresosGrupo = $ingresosAux;
//var_dump($this->_grupo);die();
    }

    public function determinarMiembroYConyuge($codMiembro)
    {
        $existePareja = false;
        foreach ($this->_grupo as $index => $miembro) {
            //si damos con el miembro que solicita
            if ($miembro[1] === $codMiembro) {
                //entonces nos fijamos si es o no jefe o jefa de familia    
                if ($miembro[1] === $codMiembro &&
                        ($miembro[5] === '01' ||
                        $miembro[5] === '02')) {
                    // si es seteamos sus datos como solicitante
                    $this->_solicitante->setNombre($miembro[3]);
                    $this->_solicitante->setApellido($miembro[2]);
                    $this->_solicitante->setDni($miembro[7]);
                    $this->_solicitante->setIngresos($miembro[19]);
                    $this->_solicitante->setOcupacion($this->buscarOcupacion($miembro[14]));
                    
                    // lo sacamos del array que estamos recorriendo y
                    // buscamos la existencia de conyuge o pareja
                    unset($this->_grupo[$index]);
                    if (count($this->_grupo) > 0) { //si queda alguien en el grupo familiar
                        foreach ($this->_grupo as $miembroRestante) {
                            if ($miembroRestante[5] === '01' ||
                                    $miembroRestante[5] === '02') {
                                $this->_solicitante->setConNombre($miembroRestante[3]);
                                $this->_solicitante->setConApellido($miembroRestante[2]);
                                $this->_solicitante->setConDni($miembroRestante[7]);
                                $this->_solicitante->setConIngresos($miembroRestante[19]);
                                $this->_solicitante->setConOcupacion($this->buscarOcupacion($miembroRestante[14]));
                                $existePareja = true;
                            }
                        }
                    }
                } else {
                    //este es el caso cuando el solicitante no es jefe o jefa
                    //del grupo familiar
                    $this->_solicitante->setConNombre($miembro[3]);
                    $this->_solicitante->setConApellido($miembro[2]);
                    $this->_solicitante->setConDni($miembro[7]);
                    $this->_solicitante->setConIngresos($miembro[19]);
                    $this->_solicitante->setOcupacion($this->buscarOcupacion($miembro[14]));
                }
            }
        }
        return $existePareja;
    }

    /**
     * @param integer $apros
     *
     * @return array Planilla preparada con todos los datos
     *
     */
    public function prepararPlanilla($apros, $otrosIngresosNum, $otrosIngresosLetras)
    {
        $apellidoYnombreSolicitante = $this->_solicitante->getApellido() .', '. $this->_solicitante->getNombre();
        $dniSolicitante = $this->_solicitante->getDni();
        $profSolicitante = $this->_solicitante->getOcupacion();
        $ingresosSolicitante = $this->_solicitante->getIngresos();
        $totalIngresos = $this->_ingresosGrupo;
        $ingresos = $this->_ingresosGrupo - $this->_solicitante->getIngresos();
        $domicilio = $this->_solicitante->getDomicilio();
        if($this->_existePareja) {
        $apellidoYnombreConSolicitante = $this->_solicitante->getConApellido() .', '. $this->_solicitante->getConNombre();
        $dniConSolicitante = $this->_solicitante->getConDni();
        $profConSolicitante = $this->_solicitante->getConOcupacion();
        $ingresosConSolicitante = $this->_solicitante->getConIngresos();
        $ingresos = $ingresos - $ingresosConSolicitante;
        } else {
        $apellidoYnombreConSolicitante = 'N/C';
        $dniConSolicitante = 'N/C';
        $profConSolicitante = 'N/C';
        $ingresosConSolicitante = 'N/C';
        }
        
        $otrosIngresosFinal = $ingresos + $otrosIngresosNum;
        $montoDelSubsidio = $this->_solicitante->getMontoSubsidio();
        $destino = $this->_solicitante->getDestinoSubsidio();
        $benficiarioDni = 'VER';
        $observaciones = $this->_solicitante->getObs();
        
        $planilla = array(
            'apellidoYnombreSolicitante' => $apellidoYnombreSolicitante,
            'dniSolicitante' => $dniSolicitante,
            'profSolicitante' => $profSolicitante,
            'ingresosSolicitante' => $ingresosSolicitante,
            'domicilio' => $domicilio,
            'apellidoYNombreConSolicitante' => $apellidoYnombreConSolicitante,
            'dniConSolicitante' => $dniConSolicitante,
            'profConSolicitante' => $profConSolicitante,
            'ingresosConSolicitante' => $ingresosConSolicitante,
            'otrosIngresosFinal' => $otrosIngresosFinal,
            'totalIngresos' => $totalIngresos,
            'montoDelSubsidio' => $montoDelSubsidio,
            'destino' => $destino,
            'beneficiarioDni' => $benficiarioDni,
            'observaciones' => $observaciones,
            'apros' => $apros
        );
        return $planilla;
    }

    public function aprosAction()
    {
        $sesion = new Zend_Session_Namespace('apros');
        $planilla = $sesion->planilla;
        $this->view->planilla = $planilla;
                
        $html = $this->view->render('miembros/apros.phtml');
        
        $mpdf=new mPDF();

        // send the captured HTML from the output buffer to the mPDF class for processing
        $mpdf->WriteHTML($html);

        $mpdf->Output();
        exit;
    }


}


