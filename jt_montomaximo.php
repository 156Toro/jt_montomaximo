<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class jt_montomaximo extends Module
{
    const MAX_AMOUNT = 500000;

    public function __construct()
    {
        $this->name = 'jt_montomaximo'; 
        $this->tab = 'front_office_features';
        $this->version = '1.0.0'; 
        $this->author = 'Jorge Toro (Steward 2025)';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        parent::__construct();

        $this->displayName = $this->l('Control de Monto Máximo');
        $this->description = $this->l('Limita el monto máximo de pedidos a $500.000 CLP');

        // Verificar que la versión sea compatible
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99'
        ];
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Registrar hooks
        return $this->registerHook('header')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->registerHook('actionCartUpdateQuantityBefore')
            && $this->registerHook('displayShoppingCart');
    }

    
    public function hookHeader($params)
    {
        $this->context->controller->registerJavascript(
            'module-jt_montomaximo-js',
            'modules/'.$this->name.'/views/js/jt_montomaximo.js',
            [
                'position' => 'bottom',
                'priority' => 200,
                
            ]
        );
    }
   

    public function hookActionCartUpdateQuantityBefore($params)
    {
        $cart = $this->context->cart;
        $orderTotal = $cart->getOrderTotal();
        
        if ($orderTotal > self::MAX_AMOUNT) {
            // Para peticiones AJAX
            if (Tools::getValue('ajax')) {
                die(json_encode([
                    'hasError' => true,
                    'errors' => [$this->l('El pedido máximo permitido es de $500.000 CLP')]
                ]));
            }
            
            // Para peticiones normales
            $this->context->controller->errors[] = $this->l('El pedido máximo permitido es de $500.000 CLP');
            return false;
        }
    }

    public function hookDisplayShoppingCart($params)
    {
        $cart = $this->context->cart;
        $orderTotal = $cart->getOrderTotal();
        
        if ($orderTotal > self::MAX_AMOUNT) {
            $this->context->smarty->assign([
                'max_amount' => self::MAX_AMOUNT,
                'current_amount' => $orderTotal
            ]);
            
            return $this->display(__FILE__, 'jt_montomaximo.tpl');
        }
        
        return '';
    }

    public function getContent()
    {
        // Configuración del módulo (opcional)
        $output = '';
        
        if (Tools::isSubmit('submit'.$this->name)) {
            $max_amount = (float)Tools::getValue('JT_MAX_AMOUNT');
            Configuration::updateValue('JT_MAX_AMOUNT', $max_amount);
            $output .= $this->displayConfirmation($this->l('Configuración actualizada'));
        }
        
        $form = new HelperForm();
        $form->fields_value['JT_MAX_AMOUNT'] = Configuration::get('JT_MAX_AMOUNT');
        
        $output .= $form->generateForm([
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuración de Monto Máximo'),
                    'icon' => 'icon-cog'
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Monto máximo permitido (CLP)'),
                        'name' => 'JT_MAX_AMOUNT',
                        'required' => true,
                        'suffix' => 'CLP'
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Guardar')
                ]
            ]
        ]);
        
        return $output;
    }
}
