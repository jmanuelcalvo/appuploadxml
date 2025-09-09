# appuploadxml
App creada con ChatGPT para la gestion de archivos XML

Si tiene el codigo php y lo quiere ejecutar en su maquina local y tiene instalado el PHP, lo puede hacer con el comando

```cd /Users/jmanuelcalvo/appuploadxml```

```php -S 127.0.0.1:8000 -t .```

Para cargar un archivo a traves de la linea de comandos:


Cargar archivo XML
`curl -X POST -F "xmlFile=@./Factura-1.xml" http://127.0.0.1:8000/api.php`

Consultar contenido archivo XML
`curl "http://127.0.0.1:8000/api.php?file=settings.xml"`

Consultar todos los archivos XML
`curl http://127.0.0.1:8000/api.php`

