import os

# Creamos un directorio para los archivos si no existe
if not os.path.exists('test_files'):
    os.makedirs('test_files')

for i in range(1, 1001):
    filename = f'file_{i}.xml'
    with open(os.path.join('test_files', filename), 'w') as f:
        f.write(f'<?xml version="1.0" encoding="UTF-8"?>\n')
        f.write(f'<data>\n')
        f.write(f'    <record id="{i}">\n')
        f.write(f'        <name>Test Name {i}</name>\n')
        f.write(f'        <value>Some random value for testing performance.</value>\n')
        f.write(f'    </record>\n')
        f.write(f'</data>\n')

print("Se generaron 1000 archivos de prueba en la carpeta 'test_files'.")
