// loader.js
function showLoader() {
    const loaderHTML = `
    <div id="loader" class="fixed inset-0 flex justify-center items-center" style="background-color: #ABEBC6; z-index: 9999;">
        <div class="flex flex-col items-center gap-4"> 
            <div class="flex flex-row gap-2">
                <img src="../resources/img/Pconnect Logo.png" alt="Logo" class="w-12 h-12 spin-animation">
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.1s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">P</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.2s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">C</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.3s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">o</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.4s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">n</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.5s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">n</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.6s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">e</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.7s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">c</div>
                <div class="text-5xl text-white font-bold animate-bounce [animation-delay:.8s]" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.3)">t</div>
            </div>
            <p class="text-[8px] font-light">Loading, please wait...</p> 
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loaderHTML);
}

function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.remove();
    }
}