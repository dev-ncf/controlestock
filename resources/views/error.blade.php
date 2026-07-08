<div id="error-container" style="display: flex; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 2000; justify-content: center; align-items: center; font-family: 'Segoe UI', sans-serif;">
    
    <div style="background: white; padding: 35px; border-radius: 20px; width: 350px; text-align: center; box-shadow: 0 15px 35px rgba(0,0,0,0.2); border-top: 6px solid #af4c4c; animation: popIn 0.3s ease-out;">
        
        <div class="error-icon-container" style="width: 80px; height: 80px; margin: 0 auto 20px;">
            <svg class="error-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="error-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="error-line1" fill="none" d="M16 16l20 20"/>
                <path class="error-line2" fill="none" d="M36 16L16 36"/>
            </svg>
        </div>

        <h2 style="color: #7d2e2e; margin-top: 0; font-size: 1.5rem;">Oops! Erro</h2>
        
        <ul style="text-align: left; color: #555; background: #fff5f5; border-radius: 8px; padding: 15px 15px 15px 35px; margin-bottom: 25px; font-size: 0.9rem; line-height: 1.4;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button class="button-erro" onclick="closeError()" style="background-color: #af4c4c; color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: bold; cursor: pointer; width: 100%; transition: background 0.2s;">
            Tentar Novamente
        </button>
    </div>
</div>

<style>
/* Animação do Círculo de Erro */
.error-svg {
    width: 80px;
    height: 80px;
    stroke-width: 4;
    stroke: #af4c4c;
    stroke-linecap: round;
}

.error-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.error-line1, .error-line2 {
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
}

.error-line1 {
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
}

.error-line2 {
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.7s forwards;
}

@keyframes stroke {
    100% { stroke-dashoffset: 0; }
}

@keyframes popIn {
    0% { transform: scale(0.8); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<script>
function closeError() {
    const errorModal = document.getElementById("error-container");
    errorModal.style.display = 'none';
}
</script>


