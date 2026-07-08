<div id="success-container" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 2000; justify-content: center; align-items: center; font-family: sans-serif;">
    
    <div style="background: white; padding: 40px; border-radius: 20px; width: 320px; text-align: center; box-shadow: 0 15px 35px rgba(0,0,0,0.2); border-top: 6px solid #4CAF50; animation: popIn 0.3s ease-out;">
        
        <div class="success-icon-container" style="width: 80px; height: 80px; margin: 0 auto 20px;">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h2 style="color: #2e7d32; margin-bottom: 10px; font-size: 1.8rem;">Sucesso!</h2>
        <p style="color: #666; margin-bottom: 25px; line-height: 1.5;">
            {{ session('success') ?? 'Operação realizada com êxito!' }}
        </p> 

        <button onclick="closeSuccess()" style="background-color: #4CAF50; color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 1rem; transition: transform 0.2s;">
            Continuar
        </button>
    </div>
</div>
<style>
/* Animação do Checkbox (Nike style) */
.checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 3;
    stroke: #4CAF50;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4CAF50;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 3;
    stroke-miterlimit: 10;
    stroke: #4CAF50;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke { 100% { stroke-dashoffset: 0; } }
@keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
@keyframes fill { 100% { box-shadow: inset 0px 0px 0px 30px #f1f8e9; } }

/* Animação de entrada do Modal */
@keyframes popIn {
    0% { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
function closeSuccess() {
    const successModal = document.getElementById("success-container");
    successModal.style.display = 'none';
}
</script>