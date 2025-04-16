console.log('✅ jt_montomaximo.js cargado y ejecutado');

document.addEventListener('DOMContentLoaded', function() {
    const MAX_AMOUNT = 500000; // $500.000 CLP
    
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-CL', {
            style: 'currency',
            currency: 'CLP',
            minimumFractionDigits: 0
        }).format(amount).replace('CLP', '').trim();
    }

    function checkMaxAmount() {
        const totalElement = document.querySelector('.cart-summary-totals span.value');
        if (!totalElement) return;
        
        const totalText = totalElement.textContent;
        const total = parseFloat(totalText.replace(/\s|[^0-9,]/g, '').replace(',', '.')) || 0;

        
        const checkoutBtn = document.querySelector('a.btn.btn-primary[href*="pedido"]');
        const alertBox = document.getElementById('jt-max-order-alert');
        
        if (total > MAX_AMOUNT) {
            // Deshabilitar botón
            if (checkoutBtn) {
                checkoutBtn.classList.add('disabled');
                checkoutBtn.style.pointerEvents = 'none';
                checkoutBtn.style.opacity = '0.5';
            }
            
            // Mostrar mensaje
            if (!document.getElementById('jt-max-order-alert')) {
                    const alertDiv = document.createElement('div');
                    alertDiv.id = 'jt-max-order-alert';
                    alertDiv.className = 'alert alert-warning';
                    alertDiv.style.cssText = `
                        background-color: #fff3cd;
                        color: #856404;
                        border: 1px dashed red;
                        padding: 10px 15px;
                        margin-top: 15px;
                        font-weight: bold;
                        font-size: 14px;
                        display: block;
                        z-index: 9999;
                    `;
                    alertDiv.innerHTML = `
                        ⚠️ Para compras superiores a ${formatCurrency(MAX_AMOUNT)} pesos,<br>
                        contáctanos al correo <strong>arriendo@steward.cl</strong> para recibir asistencia personalizada de nuestra ejecutiva web.
                    `;
                
                    const alertContainer = document.querySelector('.card.cart-summary');
                    if (alertContainer) {
                        alertContainer.prepend(alertDiv);
                        console.log('⚠️ Alerta insertada correctamente');
                    } else {
                        console.warn('❌ No se encontró el contenedor de la alerta');
                    }
                }


        } else {
            // Eliminar mensaje si existe
            if (alertBox) {
                alertBox.remove();
            }
            // Habilitar botón
            if (checkoutBtn) {
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('disabled');
            }
        }
    }

    // Verificación inicial
    setTimeout(checkMaxAmount, 500);
    
    // Eventos de actualización
    if (typeof PrestaShop !== 'undefined') {
        PrestaShop.on('updateCart', checkMaxAmount);
    }
    
    // Observador de mutación para cambios dinámicos
    const observer = new MutationObserver(checkMaxAmount);
    const cartElement = document.querySelector('.cart-summary');
    if (cartElement) {
        observer.observe(cartElement, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }
});