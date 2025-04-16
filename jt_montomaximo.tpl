<div class="alert alert-danger jt-montomaximo-alert">
    <i class="icon-warning-sign"></i>
    {l s='El pedido máximo permitido es de' mod='jt_montomaximo'} 
    {$max_amount|escape:'html':'UTF-8'|string_format:"%.0f"|replace:',':'.'} CLP.
    {l s='Actual:' mod='jt_montomaximo'} 
    {$current_amount|escape:'html':'UTF-8'|string_format:"%.0f"|replace:',':'.'} CLP
</div>