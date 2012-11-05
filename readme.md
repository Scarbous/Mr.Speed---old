Mr.Speed
====================

Das Plugin fässt alle JavaScript und CSS Dateien zu jeweils einer zusammen und entfernt überflüssige leerzeichen und Zeilenumbrüche. Des weiteren wird eine GZIP Version mit angeboten wenn der Browser dies unterstützt.

Die HTML Inhalte werden auch verkleinert, indem HTML-Kommentare und überflüssige Leerzeichen und Zeilenumbrüche entfernt werden. Auch hier wird eine GZIP Version angeboten.

Das Plugin befindet sich noch im Aufbau, funktioniert aber in meinen Projekte sehr gut.
Für Verbesserungsvorschläge, egal ob Fehler oder neue Funktionen bin ich immer offen: SHeilmeier@gmail.com

vor dem Aktivieren der Funktionen müssen diese Zeilen in der .htaccess Datei ergänzt werden:

    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^css/(.*) /wp-content/plugins/mr_speed/cache/$1 [L]
    RewriteRule ^js/(.*) /wp-content/plugins/mr_speed/cache/$1 [L]
    </IfModule>

    <FilesMatch "\.(js\.gzip|css\.gzip)">
        <IfModule mod_headers.c>
            Header set Content-Encoding gzip
    		Header append Vary: Accept-Encoding
        </IfModule>
    </FilesMatch>