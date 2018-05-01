#/bin/bash
#
#   Move client folder.
cp -r ../../html/client ../../../../
#   Move server folder.
cp -r ../../html/vendor ../../../../
#   Move index file.
cp  ../../html/index.html ../../../../
#   Move serial file.
cp  ../../serial/gkv_udp_send ../../../../
#
#
#   Move update file.
cp  ../php/update.php ../../../
#   Move update file.
cp  copy_repo.sh ../../../