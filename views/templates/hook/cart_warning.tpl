<div class="alert alert-danger jt-max-amount-alert">
    <i class="icon-warning-sign"></i>
    {l s='El pedido máximo permitido es de %s %s' sprintf=[$jt_max_amount|number_format:0:',','.'], $jt_currency mod='jt_montomaximo'}
    {l s='Actual: %s %s' sprintf=[$cart->getOrderTotal()|number_format:0:',','.'], $jt_currency mod='jt_montomaximo'}
</div>