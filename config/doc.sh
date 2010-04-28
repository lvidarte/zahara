#!/bin/bash
dir_doc=/var/local/www/nitragin.calcifer.com.ar/public/doc
dir_phpdoc=/var/local/www/devel.calcifer.com.ar/PhpDocumentor-1.4.2
file_ini=$dir_phpdoc/user/nitragin.ini
title=`awk '/^title/{print $3}' $file_ini` `svn up`

## Borrado de archivos anteriores
rm -rf $dir_doc/*

## Creacion de nueva documentacion
/usr/bin/php5 $dir_phpdoc/phpdoc -c $file_ini
