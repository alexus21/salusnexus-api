@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Test de Consejos de Salud con IA</div>

                <div class="card-body">
                    <form id="health-tip-form">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="diseases">Seleccione enfermedades:</label>
                            <select class="form-control disease-select" id="diseases" name="disease_ids[]" multiple required>
                                <option value="">Cargando enfermedades...</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="service">Servicio de IA:</label>
                            <select class="form-control" id="service" name="service">
                                <option value="gemini_openai">Google Gemini (OpenAI)</option>
                                <option value="gemini">Google Gemini (Nativo)</option>
                                <option value="deepseek">DeepSeek</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar consejo</button>
                    </form>

                    <div class="mt-4">
                        <h4>Resultado:</h4>
                        <div id="loading" style="display: none;">
                            <div class="alert alert-info">
                                Generando consejo de salud... Esto puede tardar unos segundos.
                            </div>
                        </div>
                        <div id="result" class="border p-3 mt-3" style="display: none;"></div>
                        <div id="validation-result" class="mt-3"></div>
                        <pre id="raw-json" class="bg-light p-3 mt-3" style="display: none;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar enfermedades al iniciar
        loadDiseases();
        
        // Manejar envío del formulario
        document.getElementById('health-tip-form').addEventListener('submit', function(e) {
            e.preventDefault();
            generateHealthTip();
        });
    });
    
    function loadDiseases() {
        fetch('/api/diseases')
            .then(response => response.json())
            .then(data => {
                if (data.status && data.diseases) {
                    const select = document.getElementById('diseases');
                    select.innerHTML = '';
                    
                    data.diseases.forEach(disease => {
                        const option = document.createElement('option');
                        option.value = disease.id;
                        option.textContent = disease.name;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error cargando enfermedades:', error);
            });
    }
    
    function generateHealthTip() {
        // Mostrar carga
        document.getElementById('loading').style.display = 'block';
        document.getElementById('result').style.display = 'none';
        document.getElementById('raw-json').style.display = 'none';
        document.getElementById('validation-result').innerHTML = '';
        
        // Obtener datos del formulario
        const formData = new FormData(document.getElementById('health-tip-form'));
        const diseaseIds = Array.from(document.getElementById('diseases').selectedOptions).map(option => option.value);
        const service = document.getElementById('service').value;
        
        fetch('/api/health-tips/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                disease_ids: diseaseIds,
                service: service
            })
        })
        .then(response => response.json())
        .then(data => {
            // Ocultar carga
            document.getElementById('loading').style.display = 'none';
            
            if (data.status && data.health_tip) {
                // Mostrar resultado formateado
                const resultElement = document.getElementById('result');
                resultElement.style.display = 'block';
                resultElement.innerHTML = formatHealthTip(data.health_tip);
                
                // Mostrar JSON crudo
                const rawJson = document.getElementById('raw-json');
                rawJson.style.display = 'block';
                rawJson.textContent = JSON.stringify(data.health_tip, null, 2);
                
                // Validación exitosa
                document.getElementById('validation-result').innerHTML = 
                    '<div class="alert alert-success">El formato del consejo es válido y cumple con la estructura requerida.</div>';
            } else {
                // Mostrar error
                document.getElementById('validation-result').innerHTML = 
                    `<div class="alert alert-danger">Error: ${data.message}</div>`;
                
                if (data.raw_response) {
                    const rawJson = document.getElementById('raw-json');
                    rawJson.style.display = 'block';
                    rawJson.textContent = data.raw_response;
                }
            }
        })
        .catch(error => {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('validation-result').innerHTML = 
                `<div class="alert alert-danger">Error: ${error.message}</div>`;
        });
    }
    
    function formatHealthTip(tip) {
        let html = `
            <h3>${tip.title}</h3>
            <p><em>${tip.greeting}</em></p>
            <p>${tip.tip}</p>
            <h4>Pasos a seguir:</h4>
            <ul>
        `;
        
        tip.actionable_steps.forEach(step => {
            html += `<li>${step}</li>`;
        });
        
        html += `
            </ul>
            <h4>Señales de advertencia:</h4>
            <ul>
        `;
        
        tip.warning_signs.forEach(sign => {
            html += `<li>${sign}</li>`;
        });
        
        html += `
            </ul>
            <p><strong>${tip.conclusion}</strong></p>
        `;
        
        return html;
    }
</script>
@endsection 