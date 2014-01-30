<?php

class Default_Form_PrestacionesForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
        $this->setName('Candidata');

        $this->_addClassNames('well');
        
        $apros = $this->createElement('text', 'apros');
        $apros->setRequired()->addFilters(array('StringTrim','StripTags'))
                ->setLabel('N° de APROS');
                
        $otrosIngresos = $this->createElement('text', 'ingresosNum');
        $otrosIngresos->setRequired()->addFilters(array('StringTrim','StripTags'))
                ->setLabel('Otros Ingresos (MONTO)');
        
        $otrosIngresosLetras = $this->createElement('text', 'ingresosLetras');
        $otrosIngresosLetras->setRequired()->addFilters(array('StringTrim','StripTags'))
                ->setLabel('Otros Ingresos (EN LETRAS)')
                ->setAttrib('class', 'input-xxlarge');
        
        $observaciones = $this->createElement('text', 'observaciones');
        $observaciones->setRequired()->addFilters(array('StringTrim','StripTags'))
                ->setLabel('Observaciones')
                ->setAttrib('class', 'input-xxlarge');
        
        $submit = $this->createElement('button', 'submit', array(
            'label'         => 'Guardar!',
            'type'          => 'submit'
        ));
        $submit->setAttribs(array('disableLoadDefaultDecorators' => true, 'decorators'=>array('Actions')));
        
        $this->setElements(array($apros,$otrosIngresos,$otrosIngresosLetras,$observaciones,$submit));
    }


}

