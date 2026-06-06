<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista Previa - Certificado EFB</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Times New Roman', Georgia, serif;
            background-color: #e8e8e8;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .certificado {
            background: white;
            width: 800px;
            border: 8px double #c9a03d;
            padding: 50px 60px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .iglesia {
            border-bottom: 2px solid #c9a03d;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .iglesia h1 {
            font-size: 28px;
            color: #2c3e4e;
            letter-spacing: 2px;
        }
        
        .iglesia h3 {
            font-size: 16px;
            color: #5d6d7e;
            font-weight: normal;
            margin-top: 5px;
        }
        
        .certifica {
            font-size: 18px;
            margin: 30px 0 10px;
        }
        
        .nombre {
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            color: #2c3e4e;
            border-bottom: 1px dashed #c9a03d;
            padding-bottom: 15px;
            display: inline-block;
            width: 100%;
        }
        
        .nivel {
            background: #f9f5e8;
            padding: 15px;
            font-size: 20px;
            margin: 25px 0;
        }
        
        .fecha {
            margin: 40px 0 20px;
        }
        
        .firma {
            margin-top: 50px;
        }
        
        .firma-linea {
            width: 200px;
            border-top: 1px solid #333;
            margin: 0 auto 8px auto;
        }
        
        .btn-imprimir {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-imprimir button {
            background: #2c3e4e;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-imprimir button:hover {
            background: #c9a03d;
        }
        
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .btn-imprimir { display: none; }
            .certificado { box-shadow: none; border: 8px double #000; }
        }
    </style>
</head>
<body>
    <div>
        <div class="certificado">
            <div class="iglesia">
                <h1>IGLESIA DIOS EN CASA</h1>
                <h3>Escuela de Formación Bíblica</h3>
            </div>
            
            <div class="subtitulo" style="font-size: 24px; margin: 20px 0; letter-spacing: 4px;">
                C E R T I F I C A D O
            </div>
            
            <div class="certifica">
                <p>Se certifica que</p>
            </div>
            
            <div class="nombre">
                MARÍA GONZÁLEZ PÉREZ
            </div>
            
            <div class="certifica">
                <p>ha cursado y aprobado satisfactoriamente el nivel de formación</p>
            </div>
            
            <div class="nivel">
                <strong>1A - Escuela para Bautismo</strong>
            </div>
            
            <div class="fecha">
                Caracas, 06 de junio de 2026
            </div>
            
            <div class="firma">
                <div class="firma-linea"></div>
                <p>Coordinación Académica</p>
            </div>
        </div>
        
        <div class="btn-imprimir">
            <button onclick="window.print();">🖨️ IMPRIMIR / GUARDAR COMO PDF</button>
        </div>
    </div>
</body>
</html>