for i in {1..1000}; do
  curl -s -X POST -F "xmlFile=@./test_files/file_${i}.xml" https://appuploadxml-git-appxml01.apps.cluster-6km4j.6km4j.sandbox2643.opentlc.com/api.php &
  #curl -s -X POST -F "xmlFile=@./test_files/file_${i}.xml" http://127.0.0.1:8000/api.php &
done
